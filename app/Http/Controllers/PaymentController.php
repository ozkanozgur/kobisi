<?php

namespace App\Http\Controllers;

use App\Jobs\PaymentJob;
use App\Jobs\PaymentQueueJob;
use App\Models\CompanyPackage;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function makePayment($price){
        $hash = hexdec(sha1('payment'.$price.time()));

        return substr($hash, -1) % 2 != 0;
    }

    public function checkPackagePayments(){
        /**
         * Find expired accounts
         */
        $companyPackages = CompanyPackage::where('end_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('status', '=', 1)->get();

        foreach ($companyPackages as $companyPackage){
            PaymentJob::dispatch($companyPackage);
        }
    }
}
