<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\SettingModel;

class ActiveOrderController extends BaseController
{
    // Load the page normally
    public function index()
    {
        $orderModel = new OrderModel();
        $orders = $orderModel->where('status', 'active')
                             ->orderBy('created_at', 'DESC')
                             ->findAll();

        // Read pricing + shop info from the settings table
        $s = (new SettingModel())->getAllKeyed();

        return view('adminViews/active_orders', [
            'orders' => $orders,
            'prices' => [
                'Wash & Fold' => (float) ($s['rate_wash_fold'] ?? 55),
                'Wash & Dry'  => (float) ($s['rate_wash_dry']  ?? 50),
            ],
            'shop' => [
                'name'      => $s['shop_name']    ?? 'SAS Laundry',
                'phone'     => $s['shop_phone']   ?? '',
                'address'   => $s['shop_address'] ?? '',
                'sms_ready' => $s['sms_ready']
                    ?? 'Hi {name}! Your laundry {code} is Ready for Pickup. Please bring your claim code.',
            ],
        ]);
    }

    // Save new order
    public function saveIntake()
    {
        $orderModel = new OrderModel();
        $s = (new SettingModel())->getAllKeyed();

        $service = $this->request->getPost('service');
        $weight  = (float) $this->request->getPost('weight');

        // Server-side recalculation — the Settings page is authoritative
        $rateMap = [
            'Wash & Fold' => (float) ($s['rate_wash_fold'] ?? 55),
            'Wash & Dry'  => (float) ($s['rate_wash_dry']  ?? 50),
        ];
        $rate  = $rateMap[$service] ?? 0;
        $price = round($weight * $rate, 2);

        $claimCode = 'LND-' . rand(1000, 9999);

        $data = [
            'claim_code' => $claimCode,
            'name'       => $this->request->getPost('name'),
            'phone'      => $this->request->getPost('phone'),
            'service'    => $service,
            'weight'     => $weight,
            'price'      => $price,                 // recalculated, ignores POSTed price
            'paid'       => $this->request->getPost('payment') === 'paid' ? true : false,
            'status'     => 'active',
            'stage'      => 'intake',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $orderModel->insert($data);

        return redirect()->to('/admin/active-orders')->with('success', "Order {$claimCode} created successfully!");
    }

    // ---- AJAX ENDPOINTS (Returns JSON) ----

    public function ajaxAdvanceStage()
    {
        $orderModel = new OrderModel();
        $id = $this->request->getPost('id');
        $stage = $this->request->getPost('stage');
        
        $orderModel->update($id, ['stage' => $stage]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function ajaxMarkPaid()
    {
        $orderModel = new OrderModel();
        $id = $this->request->getPost('id');
        
        $orderModel->update($id, ['paid' => true]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function ajaxCompleteOrder()
    {
        $orderModel = new OrderModel();
        $id = $this->request->getPost('id');
        
        $orderModel->update($id, [
            'status' => 'completed',
            'paid'   => true,
            'completed_at' => date('Y-m-d H:i:s')
        ]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function ajaxCancelOrder()
    {
        $orderModel = new OrderModel();
        $id = $this->request->getPost('id');
        
        $orderModel->update($id, ['status' => 'cancelled']);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function ajaxSaveIntake()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Invalid request.',
        ]);
    }

    $rules = [
        'name'    => 'required|max_length[150]',
        'phone'   => 'required|max_length[30]',
        'service' => 'required|in_list[Wash & Fold,Wash & Dry]',
        'weight'  => 'required|numeric|greater_than[0]',
        'payment' => 'required|in_list[paid,unpaid]',
    ];

    if (!$this->validate($rules)) {
        return $this->response->setStatusCode(422)->setJSON([
            'status'  => 'error',
            'message' => implode(' ', $this->validator->getErrors()),
        ]);
    }

    $orderModel = new OrderModel();
    $s = (new SettingModel())->getAllKeyed();

    $service = $this->request->getPost('service');
    $weight  = (float) $this->request->getPost('weight');

    // Server-side price recalculation — same logic as saveIntake()
    $rateMap = [
        'Wash & Fold' => (float) ($s['rate_wash_fold'] ?? 55),
        'Wash & Dry'  => (float) ($s['rate_wash_dry']  ?? 50),
    ];
    $rate  = $rateMap[$service] ?? 0;
    $price = round($weight * $rate, 2);

    // Same code format as saveIntake()
    $claimCode = 'LND-' . rand(1000, 9999);

    $isPaid = $this->request->getPost('payment') === 'paid';

    $data = [
        'claim_code' => $claimCode,
        'name'       => $this->request->getPost('name'),
        'phone'      => $this->request->getPost('phone'),
        'service'    => $service,
        'weight'     => $weight,
        'price'      => $price,
        'paid'       => $isPaid,
        'status'     => 'active',
        'stage'      => 'intake',
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $id = $orderModel->insert($data, true);

    return $this->response->setJSON([
        'status'   => 'success',
        'message'  => "Order {$claimCode} created successfully!",
        'order'    => [
            'id'         => (int) $id,
            'claim_code' => $claimCode,
            'name'       => $data['name'],
            'phone'      => $data['phone'],
            'service'    => $data['service'],
            'weight'     => $data['weight'],
            'price'      => $data['price'],
            'paid'       => $isPaid ? 1 : 0,
            'stage'      => 'intake',
        ],
        'csrfHash' => csrf_hash(),
    ]);
}
}