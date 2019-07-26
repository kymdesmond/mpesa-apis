<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function getCurrentRequestTime(){
    date_default_timezone_set('UTC');
    $date = new \DateTime();
    return $date->format('YmdHis');
}

    public function getPassword($shortcode, $passkey, $timestamp){
        return base64_encode($shortcode.$passkey.$timestamp);
    }

    public static function generateLiveToken(){

        try {
            $consumer_key = env(MPESA_CONSUMER_KEY);
            $consumer_secret = env(MPESA_CONSUMER_SECRET);
        } catch (\Throwable $th) {
            $consumer_key = env(MPESA_CONSUMER_KEY);
            $consumer_secret = env(MPESA_CONSUMER_SECRET);
        }

        if(!isset($consumer_key)||!isset($consumer_secret)){
            die("please declare the consumer key and consumer secret as defined in the documentation");
        }
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);

        return json_decode($curl_response)->access_token;


    }


    /**
     * use this function to generate a sandbox token
     * @return mixed
     */
    public static function generateSandBoxToken(){

        try {
            $consumer_key = env('MPESA_CONSUMER_KEY');
            $consumer_secret = env('MPESA_CONSUMER_SECRET');
        } catch (\Throwable $th) {
            $consumer_key = env('MPESA_CONSUMER_KEY');
            $consumer_secret = env('MPESA_CONSUMER_SECRET');
        }

        if(!isset($consumer_key)||!isset($consumer_secret)){
            die("please declare the consumer key and consumer secret as defined in the documentation");
        }
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);
        // echo $curl_response;
        return json_decode($curl_response)->access_token;
    }


    /**
     * @param $InitiatorName | 	This is the credential/username used to authenticate the transaction request.
     * @param $SecurityCredential | Encrypted password for the initiator to autheticate the transaction request
     * @param $CommandID | Unique command for each transaction type e.g. SalaryPayment, BusinessPayment, PromotionPayment
     * @param $Amount | The amount being transacted
     * @param $PartyA | Organization’s shortcode initiating the transaction.
     * @param $PartyB | Phone number receiving the transaction
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $QueueTimeOutURL | The timeout end-point that receives a timeout response.
     * @param $ResultURL | The end-point that receives the response of the transaction
     * @param $Occasion | 	Optional
     * @return string
     */
    public function b2c(Request $request){

        try {
            $environment = env('MPESA_ENV');
        } catch (\Throwable $th) {
            $environment = env('MPESA_ENV');
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));


        $curl_post_data = array(
            'InitiatorName' => env('INITIATOR_NAME'),
            'SecurityCredential' => env('SECURITY_CREDENTIAL'),
            'CommandID' => env('B2C_COMMAND_ID') ,
            'Amount' => $request->input('amount'),
            'PartyA' => env('B2C_PARTY_A') ,
            'PartyB' => env('B2C_PARTY_B'),
            'Remarks' => $request->input('remarks'),
            'QueueTimeOutURL' => env('B2C_QUERY_TIMEOUT_URL'),
            'ResultURL' => env('B2C_RESULT_URL'),
            'Occasion' => $request->input('occasion')
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        return json_encode($curl_response);

    }
    /**
     * Use this function to initiate a C2B transaction
     * @param $ShortCode | 6 digit M-Pesa Till Number or PayBill Number
     * @param $CommandID | Unique command for each transaction type.
     * @param $Amount | The amount been transacted.
     * @param $Msisdn | MSISDN (phone number) sending the transaction, start with country code without the plus(+) sign.
     * @param $BillRefNumber | 	Bill Reference Number (Optional).
     * @return mixed|string
     */
    public  static  function  c2b($Amount ){

        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/simulate';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));

        $curl_post_data = array(
            'ShortCode' => SHORTCODE2,
            'CommandID' => C2B_COMMAND_ID,
            'Amount' => $Amount,
            'Msisdn' => B2C_PARTY_B,
            'BillRefNumber' => 0000
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response = curl_exec($curl);

        echo $curl_response;

    }



    /**
     * Use this function to make a transaction status request
     * @param $Initiator | The name of Initiator to initiating the request.
     * @param $SecurityCredential | 	Encrypted password for the initiator to autheticate the transaction request.
     * @param $CommandID | Unique command for each transaction type, possible values are: TransactionStatusQuery.
     * @param $TransactionID | Organization Receiving the funds.
     * @param $PartyA | Organization/MSISDN sending the transaction
     * @param $IdentifierType | Type of organization receiving the transaction
     * @param $ResultURL | The path that stores information of transaction
     * @param $QueueTimeOutURL | The path that stores information of time out transaction
     * @param $Remarks | 	Comments that are sent along with the transaction
     * @param $Occasion | 	Optional Parameter
     * @return mixed|string
     */
    public function transactionStatus($TransactionID, $Remarks, $Occasion){

        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header


        $curl_post_data = array(
            'Initiator' => INITIATOR_NAME,
            'SecurityCredential' => SECURITY_CREDENTIAL,
            'CommandID' => TRX_STATUS_COMMMAND_ID,
            'TransactionID' => $TransactionID,
            'PartyA' => SHORTCODE,
            'IdentifierType' => TrxStatus_IdentifierType,
            'ResultURL' => CallBackURL,
            'QueueTimeOutURL' => CallBackURL,
            'Remarks' => $Remarks,
            'Occasion' => $Occasion
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response = curl_exec($curl);


        echo $curl_response;
    }




    /**
     * Use this function to initiate an STKPush Simulation
     * @param $BusinessShortCode | The organization shortcode used to receive the transaction.
     * @param $LipaNaMpesaPasskey | The password for encrypting the request. This is generated by base64 encoding BusinessShortcode, Passkey and Timestamp.
     * @param $TransactionType | The transaction type to be used for this request. Only CustomerPayBillOnline is supported.
     * @param $Amount | The amount to be transacted.
     * @param $PartyA | The MSISDN sending the funds.
     * @param $PartyB | The organization shortcode receiving the funds
     * @param $PhoneNumber | The MSISDN sending the funds.
     * @param $CallBackURL | The url to where responses from M-Pesa will be sent to.
     * @param $AccountReference | Used with M-Pesa PayBills.
     * @param $TransactionDesc | A description of the transaction.
     * @param $Remark | Remarks
     * @return mixed|string
     */
    public function STKPushSimulation($Amount, $PhoneNumber, $AccountReference, $TransactionDesc){
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $token = self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $token = self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        $BusinessShortCode = MPESA_LNM_BUS_SHORTCODE;
        $LipaNaMpesaPasskey = MPESA_PASSKEY;
        $TransactionType = "CustomerPayBillOnline";
        $timestamp='20'.date("ymdhis");
        $CallBackURL = CallBackURL;
        $PartyB = LNM_PARTY_B;
        $PartyA = $PhoneNumber;
        $Remark = "This is a test transaction";
        $password=base64_encode($BusinessShortCode.$LipaNaMpesaPasskey.$timestamp);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));


        $curl_post_data = array(
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallBackURL' => $CallBackURL,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionType
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response=curl_exec($curl);
        return $curl_response;


    }


    /**
     * Use this function to initiate an STKPush Status Query request.
     * @param $checkoutRequestID | Checkout RequestID
     * @param $businessShortCode | Business Short Code
     * @param $password | Password
     * @param $timestamp | Timestamp
     * @return mixed|string
     */
    public static function STKPushQuery($environment, $checkoutRequestID, $businessShortCode){
        $timestamp = Mpesa::getCurrentRequestTime();
        // getPassword($shortcode, $passkey, $timestamp)
        $password = Mpesa::getPassword($businessShortCode, MPESA_PASSKEY, $timestamp);
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));


        $curl_post_data = array(
            'BusinessShortCode' => $businessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestID
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $curl_response = curl_exec($curl);

        return $curl_response;
    }

    /**
     * Register validation and confirmation url
     */

    public function registerUrl(){
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        // $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header


        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => SHORTCODE2,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => ConfirmationURL,
            'ValidationURL' => ValidationURL
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        print_r($curl_response);

        return $curl_response;
    }
    /**
     *Use this function to confirm all transactions in callback routes
     */
    public function finishTransaction($status = true)
    {
        if ($status === true) {
            $resultArray=[
                "ResultDesc"=>"Confirmation Service request accepted successfully",
                "ResultCode"=>"0"
            ];
        } else {
            $resultArray=[
                "ResultDesc"=>"Confirmation Service not accepted",
                "ResultCode"=>"1"
            ];
        }

        header('Content-Type: application/json');

        echo json_encode($resultArray);
    }


    /**
     *Use this function to get callback data posted in callback routes
     */
    public function getDataFromCallback(){
        $callbackJSONData=file_get_contents('php://input');


        return $callbackJSONData;
    }
    public static function getDataFromCallback1(){
        $callbackJSONData=file_get_contents('php://input');
        return $callbackJSONData;
    }

    /**
     * processB2CRequestCallback
     */
    public static function processB2CRequestCallback(){

        $callbackJSONData=file_get_contents('php://input');
        $callbackData=json_decode($callbackJSONData);

        $transactionID=$callbackData->Result->ResultParameters->ResultParameter[0]->Value;
        $transactionAmount=$callbackData->Result->ResultParameters->ResultParameter[1]->Value;
        $transCompletedTime=$callbackData->Result->ResultParameters->ResultParameter[4]->Value;
        $receiverName=$callbackData->Result->ResultParameters->ResultParameter[5]->Value;

        $payment = new Payment(array(
            'user_id'=>Auth::user()->id,
            'item_id'=>1,
            'payment_mode'=>"Mpesa",
            'amount'=>$transactionAmount,
            'txn_id'=>$transactionID,
            'txn_time'=>$transCompletedTime,
            'payer_name'=>$receiverName,
        ));

        $payment->save();

        return json_encode($payment);
    }
}
