<?php

namespace App\Jobs;

use App\Http\Controllers\PaymentController;
use App\Models\CompanyPackage;
use App\Models\CompanyPayment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var CompanyPackage
     */
    private $companyPackage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CompanyPackage $companyPackage)
    {

        $this->companyPackage = $companyPackage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $paymentController = new PaymentController();

        $lastPayments = CompanyPayment::where('company_id', '=', $this->companyPackage->company_id)
            ->where('package_id', '=', $this->companyPackage->package_id)
            ->orderBy('created_at', 'DESC')
            ->take(3)
            ->get();

        $lastDeclinedPaymentsCount = $lastPayments->where('status', '=', 'declined')->count();


        $payment = new CompanyPayment();
        $payment->company_id = $this->companyPackage->company_id;
        $payment->package_id = $this->companyPackage->package_id;
        $payment->price = $this->companyPackage->package->price;

        if($paymentController->makePayment($this->companyPackage->package->price) == true){
            $payment->status = 'approved';
        }else{
            $payment->status  = 'declined';
        }

        $payment->save();

        if($lastDeclinedPaymentsCount==2 && $payment->status == 'declined') {
            $this->companyPackage->status = 0;
            $this->companyPackage->save();
        }else{
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->companyPackage->end_date)->addMonths($this->companyPackage->package->period)->format('Y-m-d H:i:s');
            $this->companyPackage->end_date = $endDate;
            $this->companyPackage->save();
        }
        Log::debug('***** Payment new queue worked *****');
    }
}
