<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class CompletedOrderController extends BaseController
{
    public function index()
    {
        $orderModel = new OrderModel();

        // Fetch only COMPLETED orders
        $orders = $orderModel->where('status', 'completed')
                             ->orderBy('completed_at', 'DESC')
                             ->findAll();

        // Calculate Stats for the top cards
        $totalRevenue = 0;
        $todayRevenue = 0;
        $todayOrders = 0;
        $today = date('Y-m-d');

        foreach ($orders as $o) {
            $totalRevenue += $o['price'];
            if (strpos($o['completed_at'], $today) === 0) {
                $todayRevenue += $o['price'];
                $todayOrders++;
            }
        }

        $avgOrder = count($orders) > 0 ? $totalRevenue / count($orders) : 0;

        return view('adminViews/completed_orders', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'todayOrders' => $todayOrders,
            'avgOrder' => $avgOrder
        ]);
    }
}