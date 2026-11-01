<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'claim_code', 
        'name', 
        'phone', 
        'service', 
        'weight', 
        'price', 
        'paid', 
        'status', 
        'stage', 
        'completed_at',
        'created_at' 
    ];
    
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    // ADDED 'array' HERE 👇
    protected array $casts = [
        'paid' => 'boolean',
    ];
}