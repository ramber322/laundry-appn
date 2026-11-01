<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class AnalyticsController extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();

        // Fetch ALL orders (both active and completed)
        // We need all of them to calculate unpaid balances and today's snapshot
        $orders = $orderModel->orderBy('created_at', 'DESC')->findAll();

        return view('adminViews/analytics', [
            'orders' => $orders
        ]);
    }
}