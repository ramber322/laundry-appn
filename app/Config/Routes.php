<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */


// PUBLIC ROUTES
$routes->get('/', function() { return redirect()->to('/track'); });
$routes->get('track', 'TrackingController::index');
$routes->get('track/(:segment)', 'TrackingController::view/$1');

// AUTH ROUTES
$routes->get('login', 'Home::showLogin');
$routes->post('login', 'Home::attemptLogin'); 
$routes->get('logout', 'Home::logout');


// PROTECTED ADMIN ROUTES
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    
    // Active Orders
    $routes->get('active-orders', 'ActiveOrderController::index');
    $routes->post('save-intake', 'ActiveOrderController::saveIntake');
    $routes->post('ajax-advance-stage', 'ActiveOrderController::ajaxAdvanceStage');
    $routes->post('ajax-mark-paid', 'ActiveOrderController::ajaxMarkPaid');
    $routes->post('ajax-complete-order', 'ActiveOrderController::ajaxCompleteOrder');
    $routes->post('ajax-cancel-order', 'ActiveOrderController::ajaxCancelOrder');
    $routes->post('ajax-save-intake', 'ActiveOrderController::ajaxSaveIntake');

    // Completed Orders
    $routes->get('completed-orders', 'CompletedOrderController::index');
    
    // Analytics
    $routes->get('analytics', 'AnalyticsController::index');
    
    // Payment Logs
    $routes->get('payment-logs', 'PaymentLogsController::index');
    
    // Settings
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings/save', 'SettingsController::save');
});