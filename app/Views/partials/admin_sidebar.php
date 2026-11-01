<?php 
$currentUrl = current_url();
$active = '';
if (strpos($currentUrl, 'active-orders') !== false) $active = 'active-orders';
if (strpos($currentUrl, 'completed-orders') !== false) $active = 'completed-orders';
if (strpos($currentUrl, 'analytics') !== false) $active = 'analytics';
if (strpos($currentUrl, 'payment-logs') !== false) $active = 'payment-logs';
if (strpos($currentUrl, 'settings') !== false) $active = 'settings';
?>

<div class="brand" style="margin-left: 10px;">
    <img 
        src="<?= base_url('images/laundry-icon.png') ?>" 
        alt="LMlogo" 
        style="width: 32px; height: 32px; object-fit: contain;"
    >
    <span style="font-family: 'Manrope', sans-serif; font-weight: 700; letter-spacing: -0.01em;">LaundryMate</span>
</div>

<div class="nav-menu">
    <div class="nav-label">Operations</div>

    <a href="<?= base_url('admin/active-orders') ?>" class="nav-link <?= ($active === 'active-orders') ? 'active' : '' ?>" style="font-size: 0.875rem;">
        <img src="<?= base_url('images/basket.svg') ?>" alt="" class="basket-icon">
        Active Orders
    </a>

    <a href="<?= base_url('admin/completed-orders') ?>" class="nav-link <?= ($active === 'completed-orders') ? 'active' : '' ?>" style="font-size: 0.875rem;">
        <img src="<?= base_url('images/bookmark-check.svg') ?>" alt="" class="bookmark-icon">
        Completed Orders
    </a>

    <div class="nav-label" style="margin-top: 1.5rem;">Management</div>

    <a href="<?= base_url('admin/analytics') ?>" class="nav-link <?= ($active === 'analytics') ? 'active' : '' ?>" style="font-size: 0.875rem;">
        <img src="<?= base_url('images/bar-chart.svg') ?>" alt="" class="barchart-icon">
        Analytics
    </a>

    <a href="<?= base_url('admin/payment-logs') ?>" class="nav-link <?= ($active === 'payment-logs') ? 'active' : '' ?>" style="font-size: 0.875rem;">
        <img src="<?= base_url('images/credit-card.svg') ?>" alt="" class="credit-icon">
        Payment Logs
    </a>

    <a href="<?= base_url('admin/settings') ?>" class="nav-link <?= ($active === 'settings') ? 'active' : '' ?>" style="font-size: 0.875rem;">
        <img src="<?= base_url('images/settings-icon.svg') ?>" alt="" class="setting-icon">
        Settings
    </a>
</div>

<div class="logout-wrap">
    <a href="<?= base_url('logout') ?>" class="nav-link nav-link-logout" style="font-size: 0.875rem;">
        <img src="<?= base_url('images/logout-exit.svg') ?>" alt="" class="setting-icon">
        Logout
    </a>
</div>