<?php

namespace Ollyo\Task\Controllers;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;
use PayPal\Api\PayerInfo;
use Exception;

class PaymentController
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                'Aa80mgHcy-GYgDRrVTNVT65_P5gySZRDgFMsnutyBhN2OwiZhpIoU_YpvJA4KerSe3t_f04ZeipVMYa8', // Replace with your PayPal client ID
                'ECPz9x58JWFpCjQkubmDv4mjjS8XrLHTfE30mxuHmZhO09xHBUK5HiuMeLTfW9uMFO-dfhIwFyOLVvuc'     // Replace with your PayPal secret key
            )
        );
        $this->apiContext->setConfig([
            'mode' => 'sandbox', // 'live' for production
        ]);
    }

    public function createPayment($data)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        
        $payerInfo = new PayerInfo();
        $payerInfo->setEmail($data['email'])
                  ->setFirstName($data['name'])
                  ->setPayerId($data['email']);

        // Create and set the ShippingAddress object properly
        $shippingAddress = new \PayPal\Api\Address();
        $shippingAddress->setLine1($data['address']) // Address line 1
                        ->setCity($data['city']) // City
                        ->setPostalCode($data['post_code']) // Postal code
                        ->setCountryCode('GB'); // Country code (use 'GB' for UK, or adjust accordingly)

        $payerInfo->setShippingAddress($shippingAddress);

        $payer->setPayerInfo($payerInfo);

        $amount = new Amount();
        $amount->setCurrency('USD') 
               ->setTotal($data['total']);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setDescription('Product Purchase');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl('https://9530-37-111-193-180.ngrok-free.app//success')  
                     ->setCancelUrl('https://9530-37-111-193-180.ngrok-free.app//cancel');  

        $payment = new Payment();
        $payment->setIntent('sale') 
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->apiContext);
            $approvalUrl = $payment->getApprovalLink(); 
            header("Location: $approvalUrl");
            exit;
        } catch (Exception $ex) {
            error_log('PayPal API Error: ' . $ex->getMessage());
            die('Error occurred while creating payment: ' . $ex->getMessage());
        }
    }

    public function executePayment($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->apiContext);
            return $result;
        } catch (Exception $ex) {
            error_log('PayPal API Execution Error: ' . $ex->getMessage());
            die('Error occurred while executing payment: ' . $ex->getMessage());
        }
    }
}
