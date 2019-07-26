<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
MPESA_ENV=sandbox
MPESA_CONSUMER_KEY=b5ynoZLKK2vEGcmDT2zOFw6fRecikilT
MPESA_CONSUMER_SECRET=LnrDNLai9kWc1XRy
MPESA_PASSKEY = bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
MPESA_LNM_BUS_SHORTCODE=174379
INITIATOR_NAME = apitest486
SECURITY_CREDENTIAL = VJd+owOOyHQZa1VzkHA4nuR/EcXJ1yDTEJBF0xqlfAdxKfNkTpuSfuaEcLOEueRyssQVIODk9rRJV1aGmHg0E5GD23GgHO70Lf/SLy18DWzseyPep/6+HDY2fdsn51Af6Vu3G7sAYGVWyaCcx2zcKHVc+FhP5Wk8EuzWGT/MXJXZRDPFzvUnJQOheuCBDxvpvRFK+7EdZjhUZQp62fBbwRTknfRitCpM4eGkPoAfOYdZnNBSi9pFjhFf6WRLqdGnQNVUDUpfDAEERPNB2ocjyoyJuMooAVk7xCzkQxSp7G/dpOzgdjL37KXWD5DkrWlWf8QJCtVA3BFFmW1D90kuHw==
B2C_COMMAND_ID= SalaryPayment

HOST=https://a43b04b3.ngrok.io
B2C_QUERY_TIMEOUT_URL = https://a43b04b3.ngrok.io/api/callback
B2C_RESULT_URL= https://a43b04b3.ngrok.io/api/callback
B2C_PARTY_A = 601486
B2C_PARTY_B = 254708374149
SHORTCODE = 601486
SHORTCODE2 = 600000
C2B_COMMAND_ID = CustomerPayBillOnline
CallBackURL = https://a43b04b3.ngrok.io/api/callback
ValidationURL = validation
ConfirmationURL = confirmation
LNM_PARTY_B = 174379

TRX_STATUS_COMMMAND_ID = TransactionStatusQuery
TrxStatus_IdentifierType = 1
}
