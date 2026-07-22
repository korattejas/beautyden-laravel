<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$transactions = \App\Models\RazorpayTransaction::whereNull('payment_details')->get();
if($transactions->count() > 0) {
    $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    foreach($transactions as $t) {
        if($t->razorpay_payment_id) {
            try {
                $payment = $api->payment->fetch($t->razorpay_payment_id);
                $t->payment_details = $payment->toArray();
                $t->save();
                echo 'Updated ' . $t->id . PHP_EOL;
            } catch (\Exception $e) {
                echo 'Error for ' . $t->id . ': ' . $e->getMessage() . PHP_EOL;
            }
        }
    }
} else {
    echo 'No transactions to update.';
}
