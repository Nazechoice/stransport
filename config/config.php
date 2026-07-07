<?php

declare(strict_types=1);

return array (
  'app' => 
  array (
    'name' => 'Public Bus Transport Ticketing System',
    'title' => 'Public Bus Transport Ticketing System',
    'base_url' => '/transport',
    'timezone' => 'Africa/Lagos',
    'session_name' => 'pbt_ticketing',
    'session_lifetime' => 3600,
    'upload_path' => 'C:\\xampp\\htdocs\\transport/storage/uploads',
  ),
  'db' => 
  array (
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3307',
    'database' => 'transport_ticketing_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
  ),
  'security' => 
  array (
    'password_algo' => '2y',
    'csrf_key' => '_csrf_token',
    'remember_cookie' => 'pbt_remember',
    'remember_days' => 30,
  ),
  'roles' => 
  array (
    'super_admin' => 'Super Administrator',
    'administrator' => 'Administrator',
    'ticket_officer' => 'Ticket Officer',
    'driver' => 'Driver',
    'passenger' => 'Passenger',
  ),
  'ticket' => 
  array (
    'prefix' => 'PBT',
  ),
);
