<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PaymentController extends Controller
{
    public function makePayment($price){
        $hash = hexdec( sha1('payment'.$price.time()));

        return substr($hash, -1) % 2 != 0;
    }
}
