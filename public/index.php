<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$base_path = '/rentacar/public';
$request_uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($request_uri, '?')) {
    $request_uri = substr($request_uri, 0, $pos);
}
$route = rawurldecode($request_uri);
if (strpos($route, $base_path) === 0) {
    $route = substr($route, strlen($base_path));
}
$route = trim($route, '/');
if (empty($route)) {
    $route = 'home';
}

switch ($route) {
    case 'home':
        $controller = new App\Controllers\HomeController();
        $controller->index();
        break;

    // --- YENİ KULLANICI ROTALARI ---
    case 'register':
        $controller = new App\Controllers\AuthController();
        $controller->showRegisterForm();
        break;
    
    case 'register-submit':
        $controller = new App\Controllers\AuthController();
        $controller->register();
        break;

    case 'login-submit':
        $controller = new App\Controllers\AuthController();
        $controller->login();
        break;

    case 'logout':
        $controller = new App\Controllers\AuthController();
        $controller->logout();
        break;

    // --- YENİ ROTA ---
    case 'car-detail':
        $controller = new App\Controllers\CarController();
        $controller->show();
        break;
    // --- YENİ ROTA SONU ---
case 'create-reservation': // YENİ EKLENDİ
    $controller = new App\Controllers\ReservationController();
    $controller->create();
    break;
   

case 'my_reservations':
    $controller = new App\Controllers\UserController();
    $controller->showMyReservations();
    break;

case 'admin':

case 'admin/dashboard':
        $controller = new App\Controllers\AdminController();
        $controller->dashboard();
        break;

case 'admin/cars':
    $controller = new App\Controllers\AdminController();
    $controller->listCars();
    break;

case 'admin/cars/create':
    $controller = new App\Controllers\AdminController();
    $controller->showCreateCarForm();
    break;

// Yeni araç formundan gelen veriyi kaydeder (POST isteği)
case 'admin/cars/store':
    $controller = new App\Controllers\AdminController();
    $controller->storeCar();
    break;

case 'admin/cars/edit':
    $controller = new App\Controllers\AdminController();
    $controller->showEditCarForm();
    break;

// Düzenleme formundan gelen veriyi günceller (POST isteği)
case 'admin/cars/update':
    $controller = new App\Controllers\AdminController();
    $controller->updateCar();
    break;
// --- YENİ DÜZENLEME ROTALARI SONU --- 

case 'admin/cars/delete':
        $controller = new App\Controllers\AdminController();
        $controller->deleteCar();
        break;

case 'admin/reservations':
    $controller = new App\Controllers\AdminController();
    $controller->listReservations();
    break;

case 'admin/reservations/edit':
    $controller = new App\Controllers\AdminController();
    $controller->showEditReservationForm();
    break;

case 'admin/reservations/update':
    $controller = new App\Controllers\AdminController();
    $controller->updateReservation();
    break;

case 'admin/users':
    $controller = new App\Controllers\AdminController();
    $controller->listUsers();
    break;
case 'admin/users/edit':
    $controller = new App\Controllers\AdminController();
    $controller->showEditUserForm();
    break;

case 'admin/users/update':
    $controller = new App\Controllers\AdminController();
    $controller->updateUser();
    break;
case 'admin/locations':
    $controller = new App\Controllers\AdminController();
    $controller->listLocations();
    break;
case 'admin/locations/create':
    $controller = new App\Controllers\AdminController();
    $controller->showCreateLocationForm();
    break;

case 'admin/locations/store':
    $controller = new App\Controllers\AdminController();
    $controller->storeLocation();
    break;

case 'admin/locations/edit':
    $controller = new App\Controllers\AdminController();
    $controller->showEditLocationForm();
    break;

case 'admin/locations/update':
    $controller = new App\Controllers\AdminController();
    $controller->updateLocation();
    break;
case 'admin/locations/delete':
        $controller = new App\Controllers\AdminController();
        $controller->deleteLocation();
        break;
case 'admin/categories':
    $controller = new App\Controllers\AdminController();
    $controller->listCategories();
    break;
case 'admin/categories/create':
    $controller = new App\Controllers\AdminController();
    $controller->showCreateCategoryForm();
    break;

case 'admin/categories/store':
    $controller = new App\Controllers\AdminController();
    $controller->storeCategory();
    break;
case 'admin/categories/edit':
    $controller = new App\Controllers\AdminController();
    $controller->showEditCategoryForm();
    break;

case 'admin/categories/update':
    $controller = new App\Controllers\AdminController();
    $controller->updateCategory();
    break;

case 'admin/categories/delete':
    $controller = new App\Controllers\AdminController();
    $controller->deleteCategory();
    break;
case 'cancel-reservation':
    $controller = new App\Controllers\ReservationController();
    $controller->cancel();
    break;

case 'profile':
    $controller = new App\Controllers\UserController();
    $controller->showProfile();
    break;

case 'update-password':
    $controller = new App\Controllers\UserController();
    $controller->updatePassword();
    break;
case 'admin/reports':
    $controller = new App\Controllers\AdminController();
    $controller->showReports();
    break;








 default:
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
        break;








}
?>