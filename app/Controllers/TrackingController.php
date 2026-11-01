<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class TrackingController extends BaseController
{
    // 1. Show the Search Page (or the Order Page if a code is passed)
   public function index()
{
    $code = $this->request->getGet('code');

    if (!$code) {
        return view('tracking'); // Show empty search box
    }

    $orderModel = new OrderModel();
    $order = $orderModel->where('claim_code', strtoupper($code))->first();

    if (!$order) {
        return redirect()->to('/track')->with('error', 'Order not found. Please check your code.');
    }

    // Fetch settings for the brand name
    $db = \Config\Database::connect();
    $results = $db->table('settings')->get()->getResultArray();
    $settings = [];
    foreach ($results as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return view('tracking', [
        'order' => $order,
        'settings' => $settings
    ]);
}

    // 2. Handle the specific tracking code
    public function view($code)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->where('claim_code', strtoupper($code))->first();

        if (!$order) {
            return redirect()->to('/track')->with('error', 'Order not found. Please check your code.');
        }

        // Fetch settings for the brand name
        $db = \Config\Database::connect();
        $results = $db->table('settings')->get()->getResultArray();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        // Match Views/tracking.php
        return view('tracking', [
            'order' => $order,
            'settings' => $settings
        ]);
    }
}