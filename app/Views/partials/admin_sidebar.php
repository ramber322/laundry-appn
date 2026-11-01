<?php 
$currentUrl = current_url();
$active = '';
if (strpos($currentUrl, 'active-orders') !== false) $active = 'active-orders';
if (strpos($currentUrl, 'completed-orders') !== false) $active = 'completed-orders';
if (strpos($currentUrl, 'analytics') !== false) $active = 'analytics';
if (strpos($currentUrl, 'payment-logs') !== false) $active = 'payment-logs';
if (strpos($currentUrl, 'settings') !== false) $active = 'settings';
?>

<div class="brand">
    <img 
        src="<?= base_url('images/laundry-icon.png') ?>" 
        alt="LMlogo" 
        style="width: 32px; height: 32px; object-fit: contain;"
    >
    <span>LaundryMate</span>
</div>

<div class="nav-menu">
    <div class="nav-label">Operations</div>
    <a href="<?= base_url('admin/active-orders') ?>" class="nav-link <?= ($active === 'active-orders') ? 'active' : '' ?>">
         Active Orders
    </a>
    <a href="<?= base_url('admin/completed-orders') ?>" class="nav-link <?= ($active === 'completed-orders') ? 'active' : '' ?>">
         Completed Orders
    </a>

    <div class="nav-label" style="margin-top: 1.5rem;">Management</div>
    <a href="<?= base_url('admin/analytics') ?>" class="nav-link <?= ($active === 'analytics') ? 'active' : '' ?>">
         Analytics
    </a>
    <a href="<?= base_url('admin/payment-logs') ?>" class="nav-link <?= ($active === 'payment-logs') ? 'active' : '' ?>">
         Payment Logs
    </a>
    <a href="<?= base_url('admin/settings') ?>" class="nav-link <?= ($active === 'settings') ? 'active' : '' ?>">
        ⚙️ Settings
    </a>
</div>

<div class="logout-wrap">
    <a href="<?= base_url('logout') ?>" class="nav-link nav-link-logout">
        🚪 Logout
    </a>
</div>