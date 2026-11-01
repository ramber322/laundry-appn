<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class CreateAdmin extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'admin:create';
    protected $description = 'Creates the first admin/owner account';

    public function run(array $params)
    {
        $email = CLI::prompt('Admin Email', null, 'required|valid_email');
        $password = CLI::prompt('Password', null, 'required|min_length[8]');
        $name = CLI::prompt('Full Name', null, 'required');

        $userModel = new UserModel();

        // Check if user exists
        if ($userModel->where('email', $email)->first()) {
            CLI::error("User with email {$email} already exists.");
            return;
        }

        $userModel->insert([
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'name'          => $name,
            'role'          => 'owner',
        ]);

        CLI::write(" Admin created successfully!", 'green');
        CLI::write("Email: {$email}", 'yellow');
    }
}