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
    private $baseUrl = "https://9530-37-111-193-180.ngrok-free.app";
    private $clientSecret = "update with your paypal client secret";
    private $clientId = "update with your paypal client id";

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->clientId,
                $this->clientSecret
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

        $shippingAddress = new \PayPal\Api\Address();
        $shippingAddress->setLine1($data['address'])
                        ->setCity($data['city'])
                        ->setPostalCode($data['post_code'])
                        ->setCountryCode('GB');

        $payerInfo->setShippingAddress($shippingAddress);

        $payer->setPayerInfo($payerInfo);

        $amount = new Amount();
        $amount->setCurrency('USD') 
               ->setTotal($data['total']);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setDescription('Product Purchase');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->baseUrl.'/success')  
                     ->setCancelUrl($this->baseUrl.'/payment-failed');  

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

}
