<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Safaricom\Mpesa\Facade\Mpesa;

class MpesaController extends Controller
{
    public function b2c(){
        $mpesa= new \Safaricom\Mpesa\Mpesa();


        return $b2cTransaction=$mpesa->b2c($InitiatorName, $SecurityCredential, $CommandID, $Amount, $PartyA, $PartyB, $Remarks, $QueueTimeOutURL, $ResultURL, $Occasion);
    }

}
