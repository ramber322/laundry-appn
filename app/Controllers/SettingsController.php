<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SettingsController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Fetch all settings and convert to an easy-to-use array: ['shop_name' => 'SS Laundry', ...]
        $results = $db->table('settings')->get()->getResultArray();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return view('adminViews/settings', ['settings' => $settings]);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        
        // Data from the form
        $data = [
            'shop_name'      => $this->request->getPost('shop_name'),
            'shop_address'   => $this->request->getPost('shop_address'),
            'shop_phone'     => $this->request->getPost('shop_phone'),
            'rate_wash_fold' => $this->request->getPost('rate_wash_fold'),
            'rate_wash_dry'  => $this->request->getPost('rate_wash_dry'),
            'sms_intake'     => $this->request->getPost('sms_intake'),
            'sms_ready'      => $this->request->getPost('sms_ready'),
        ];

        // Update each setting in the database
        foreach ($data as $key => $value) {
            // Check if it exists first
            $exists = $db->table('settings')->where('setting_key', $key)->countAllResults();
            if ($exists) {
                $db->table('settings')->where('setting_key', $key)->update(['setting_value' => $value]);
            } else {
                $db->table('settings')->insert(['setting_key' => $key, 'setting_value' => $value]);
            }
        }

        return redirect()->to('/admin/settings')->with('success', 'Settings saved successfully!');
    }
}