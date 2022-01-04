<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyInformationResource;
use App\Models\Company;
use App\Models\CompanyPackage;
use App\Models\CompanyPayment;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\PaymentController;

class CompanyController extends Controller
{
    /**
     * @var PaymentController
     */
    private $paymentController;

    public function __construct(PaymentController $paymentController){

        $this->paymentController = $paymentController;
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Company register method for api request
     */
    public function registerCompany(Request $request){
        /**
         * Validation for api request
         **/
        $request->validate([
            "site_url"              => 'required',
            "name"                  => 'required',
            "last_name"             => 'required',
            "company_name"          => 'required',
            "email"                 => 'required|email|unique:companies,email',
            "password"              => 'required|min:8',
        ], [
            "site_url.required"     => __("Please provide a site url for your site"),
            "name.required"         => __("Please provide a name"),
            "last_name.required"    => __("Please provide a last name"),
            "company_name.required" => __("Please provide your company name"),
            "email.required"        => __("Please provide an email address"),
            "email.email"           => __("Please provide an correct email address"),
            "email.unique"          => __("This email address is used"),
            "password.required"     => __("Please provide your password"),
        ]);

        /**
         * Creating company
         */
        $company = new Company();
        $company->site_url = $request->site_url;
        $company->name = $request->name;
        $company->last_name = $request->last_name;
        $company->company_name = $request->company_name;
        $company->email = $request->email;
        $company->password = Hash::make($request->password);
        $company->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $company->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $company->save();

        /**
         * Creating token for company
         */
        $token = $company->createToken('Kobisi')->plainTextToken;

        return response()->json([
            'status'        => 'success',
            'token'         => $token,
            'company_id'    => $company->id,
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Regenarete token for company
     */
    public function loginCompany(Request $request){
        /**
         * Validation for api request
         **/
        $request->validate([
            "email"                 => 'required|email',
            "password"              => 'required',
        ], [
            "email.required"        => __("Please provide an email address"),
            "email.email"           => __("Please provide an correct email address"),
            "password.required"     => __("Please provide your company name"),
        ]);

        $company = Company::where('email', '=', $request->email)->firstOrFail();

        if(!$company && !Hash::check($request->password, $company->password)){
            return reponse()->json([
                "status"    => "fail",
                "message"   => __('Invalid email or password'),
            ], 401);
        }

        $token = $company->createToken('Kobisi')->plainTextToken;

        return response()->json([
            'status'        => 'success',
            'token'         => $token,
            'company_id'    => $company->id,
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Register package for company
     */
    public function registerPackage(Request $request){
        /**
         * Validation for api request
         **/
        $request->validate([
            "company_id"    => "required",
            "package_id"    => "required",
        ], [
            "company_id.required"   => __("Provide a company id"),
            "package_id.required"   => __("Provide a package id"),
        ]);

        /**
         * Get company
         */
        $company = Company::with('package')->findOrFail($request->company_id);

        /**
         * is the company id equal to the who request register package?
         */
        if(auth()->user()->id != $company->id){
            return response()->json([
                "status"    => "fail",
                "message"   => __("Invalid token"),
            ], 401);
        }

        /**
         * If company have package already returns fail message
         */
        if($company->package!=null){
            return response()->json([
                "status"    => "fail",
                "message"   => __("Already have package"),
            ], 401);
        }

        /**
         * Get package
         */
        $package = Package::findOrFail($request->package_id);

        /**
         * Try take payment
         */
        $paymentStatus = $this->paymentController->makePayment($package->price);

        if($paymentStatus == false){
            return response()->json([
                "status"    => "fail",
                "message"   => __("Payment declined"),
            ], 402);
        }

        /**
         * Calculate period start date and end date
         */
        $startDate = Carbon::now()->format('Y-m-d H:i:s');
        $endDate = Carbon::now()->addMonths($package->period)->format('Y-m-d H:i:s');

        /**
         * Creates company package
         */
        $companyPackage = new CompanyPackage();
        $companyPackage->company_id = $request->company_id;
        $companyPackage->package_id = $request->package_id;
        $companyPackage->status = 1;
        $companyPackage->start_date = $startDate;
        $companyPackage->end_date = $endDate;
        $companyPackage->save();

        /**
         * Creates company payment
         */
        $companyPayment = new CompanyPayment();
        $companyPayment->company_id = $request->company_id;
        $companyPayment->package_id = $request->package_id;
        $companyPayment->price = $package->price;
        $companyPayment->status = 'approved';
        $companyPayment->save();

        return response()->json([
            "status"            => "success",
            "start_date"        => $companyPackage->start_date,
            "end_date"          => $companyPackage->end_date,
            "package_status"    => $companyPackage->status == 1 ? 'active' : 'passive',
            "package"           => $package,
        ]);
    }

    public function getCompanyInfo(){
        return response()->json([
            "status"            => "success",
            "company"           => new CompanyInformationResource(auth()->user()),
        ]);
    }

}
