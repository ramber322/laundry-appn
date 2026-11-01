<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class PaymentLogsController extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();

        // Fetch ONLY completed and paid orders (these are your successful transactions)
        $payments = $orderModel->where('status', 'completed')
                               ->where('paid', true)
                               ->orderBy('completed_at', 'DESC')
                               ->findAll();

        // Calculate the summary cards (All time)
        $totalCollected = 0;
        foreach ($payments as $p) {
            $totalCollected += (float)$p['price'];
        }
        
        $totalTransactions = count($payments);
        $avgPayment = $totalTransactions > 0 ? $totalCollected / $totalTransactions : 0;

        return view('adminViews/payment_logs', [
            'payments' => $payments,
            'totalCollected' => $totalCollected,
            'totalTransactions' => $totalTransactions,
            'avgPayment' => $avgPayment
        ]);
    }
}