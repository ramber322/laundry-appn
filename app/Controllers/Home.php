<?php

namespace App\Controllers;

use App\Models\UserModel;

class Home extends BaseController
{
    // ==========================================
    // PUBLIC ROUTES
    // ==========================================
    public function index()
    {
        // Send guest/customer to default tracking page
        return redirect()->to('/track');
    }

    // ==========================================
    // AUTHENTICATION
    // ==========================================
    public function showLogin()
    {
        // If they are already logged in, send them to the dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('admin/active-orders');
        }
        return view('login'); 
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Success - Set Session
            $session->set([
                'id'         => $user['id'],
                'name'       => $user['name'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ]);
            return redirect()->to('admin/active-orders');
        } else {
            // Fail
            $session->setFlashdata('error', 'Invalid email or password.');
            return redirect()->to('/login'); // Changed from /admin/login
        }
    }

    public function logout()
    {
        // Destroy the session so the auth filter blocks them
        session()->destroy();
        
        // Send them back to the login screen
        return redirect()->to('/login'); // Changed from /admin/login
    }
}