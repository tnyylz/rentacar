<?php

namespace App\Controllers;

// Gerekli ana sınıfları 'use' ile çağır
use App\BaseController;
use App\Database;

class AdminController extends BaseController {

  
    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            $_SESSION['message'] = "Bu alana erişim yetkiniz yok.";
            $_SESSION['message_type'] = 'danger';
            header('Location: /rentacar/public/home');
            exit();
        }
    }

    /**
     * BaseController'daki loadView metodunu eziyoruz (override).
     * Bu, tüm view dosyalarını 'views/admin/' klasöründe aramamızı sağlar.
     */
    protected function loadView($viewName, $data = []) {
        extract($data);
        require_once __DIR__ . '/../../views/admin/' . $viewName . '.php';
    }

    /**
     * Verilen bir adres metnini koordinatlara çevirir.
     */
    private function getCoordinatesForAddress(string $address): ?array {
        $address_encoded = urlencode($address);
        $url = "https://nominatim.openstreetmap.org/search?q={$address_encoded}&format=json&limit=1";
        $options = [ 'http' => [ 'header' => "User-Agent: RentACarProject/1.0 (info@example.com)\r\n" ] ];
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) { return null; }
        $data = json_decode($response, true);
        if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
            return [ 'lat' => $data[0]['lat'], 'lon' => $data[0]['lon'] ];
        }
        return null;
    }

    /**
     * Admin paneli ana sayfasını (dashboard) gösterir.
     */
    public function dashboard() {
        $conn = Database::getInstance()->getConnection();
        $total_cars = $conn->query("SELECT COUNT(*) AS count FROM cars")->fetch_assoc()['count'];
        $pending_reservations = $conn->query("SELECT COUNT(*) AS count FROM reservations WHERE status = 'Beklemede'")->fetch_assoc()['count'];
        $total_customers = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'Customer'")->fetch_assoc()['count'];
        
        $sql_recent_reservations = "SELECT 
                                    r.reservation_id, 
                                    CONCAT(u.first_name, ' ', u.last_name) AS user_full_name,
                                    CONCAT(c.brand, ' ', c.model) AS car_name,
                                    r.status, r.total_price
                                  FROM reservations AS r
                                  JOIN users AS u ON r.user_id = u.user_id
                                  JOIN cars AS c ON r.car_id = c.car_id
                                  ORDER BY r.reservation_id DESC LIMIT 5";
        $recent_reservations = $conn->query($sql_recent_reservations)->fetch_all(MYSQLI_ASSOC);

        $data = [
            'total_cars' => $total_cars,
            'pending_reservations' => $pending_reservations,
            'total_customers' => $total_customers,
            'recent_reservations' => $recent_reservations
        ];
        $data['page_title'] = 'Ana Sayfa';
        $data['breadcrumbs'] = [['name' => 'Dashboard', 'link' => null]];
        $this->loadView('dashboard', $data);
    }

    // --- ARAÇ YÖNETİMİ ---
    public function listCars() {
        $conn = Database::getInstance()->getConnection();
        $limit = 10;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;
        $total_cars = $conn->query("SELECT COUNT(*) as count FROM cars")->fetch_assoc()['count'];
        $total_pages = ceil($total_cars / $limit);
        
        $sql = "SELECT c.*, cat.category_name, loc.location_name
                FROM cars AS c
                LEFT JOIN categories AS cat ON c.category_id = cat.category_id
                LEFT JOIN locations AS loc ON c.current_location_id = loc.location_id
                ORDER BY c.car_id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        $data = [
            'cars' => $cars,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
        $data['page_title'] = 'Araç Yönetimi';
        $data['breadcrumbs'] = [['name' => 'Araçlar', 'link' => null]];
        $this->loadView('cars_list', $data);
    }

    public function showCreateCarForm() {
        $conn = Database::getInstance()->getConnection();
        $categories = $conn->query("SELECT * FROM categories WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
        $locations = $conn->query("SELECT * FROM locations WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
        
        $data = [
            'categories' => $categories,
            'locations' => $locations
        ];
        $data['page_title'] = 'Yeni Araç Ekle';
        $data['breadcrumbs'] = [['name' => 'Araçlar', 'link' => '/rentacar/public/admin/cars'], ['name' => 'Yeni Ekle', 'link' => null]];
        $this->loadView('car_create_form', $data);
    }

    public function storeCar() {
        $conn = Database::getInstance()->getConnection();
        $sql = "INSERT INTO cars (brand, model, year, license_plate, daily_rate, category_id, fuel_type, transmission_type, current_location_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisdissis", 
            $_POST['brand'], $_POST['model'], $_POST['year'], $_POST['license_plate'], $_POST['daily_rate'], 
            $_POST['category_id'], $_POST['fuel_type'], $_POST['transmission_type'], $_POST['current_location_id'], 'Müsait'
        );
        if ($stmt->execute()) {
            $_SESSION['message'] = "Araç başarıyla eklendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Araç eklenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/cars");
        exit();
    }

    public function showEditCarForm() {
        $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$car_id) { header("Location: /rentacar/public/admin/cars"); exit(); }

        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $car = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$car) { header("Location: /rentacar/public/admin/cars"); exit(); }

        $categories = $conn->query("SELECT * FROM categories WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
        $locations = $conn->query("SELECT * FROM locations WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
        
        $data = [
            'car' => $car,
            'categories' => $categories,
            'locations' => $locations
        ];
        $data['page_title'] = 'Aracı Düzenle';
        $data['breadcrumbs'] = [['name' => 'Araçlar', 'link' => '/rentacar/public/admin/cars'], ['name' => 'Düzenle', 'link' => null]];
        $this->loadView('car_edit_form', $data);
    }

    public function updateCar() {
        $conn = Database::getInstance()->getConnection();
        $sql = "UPDATE cars SET brand = ?, model = ?, year = ?, fuel_type = ?, transmission_type = ?, license_plate = ?, daily_rate = ?, category_id = ?, current_location_id = ?, status = ?
                WHERE car_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssdiisi", 
            $_POST['brand'], $_POST['model'], $_POST['year'], $_POST['fuel_type'], $_POST['transmission_type'], 
            $_POST['license_plate'], $_POST['daily_rate'], $_POST['category_id'], $_POST['current_location_id'], $_POST['status'], $_POST['car_id']
        );
        if ($stmt->execute()) {
            $_SESSION['message'] = "Araç başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Araç güncellenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/cars");
        exit();
    }

    public function deleteCar() {
        $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$car_id) { header("Location: /rentacar/public/admin/cars"); exit(); }

        $conn = Database::getInstance()->getConnection();
        $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE car_id = ?");
        $stmt_check->bind_param("i", $car_id);
        $stmt_check->execute();
        $reservation_count = $stmt_check->get_result()->fetch_assoc()['count'];
        $stmt_check->close();

        if ($reservation_count > 0) {
            $stmt_update = $conn->prepare("UPDATE cars SET status = 'Pasif' WHERE car_id = ?");
            $stmt_update->bind_param("i", $car_id);
            $stmt_update->execute();
            $_SESSION['message'] = "Aracın rezervasyon kayıtları bulunduğu için 'Pasif' olarak güncellendi.";
            $_SESSION['message_type'] = 'warning';
        } else {
            $stmt_delete = $conn->prepare("DELETE FROM cars WHERE car_id = ?");
            $stmt_delete->execute();
            $_SESSION['message'] = "Araç (ID: {$car_id}) başarıyla kalıcı olarak silindi.";
            $_SESSION['message_type'] = 'success';
        }
        header("Location: /rentacar/public/admin/cars");
        exit();
    }

    // --- REZERVASYON YÖNETİMİ ---
    public function listReservations() {
        $conn = Database::getInstance()->getConnection();
        $limit = 10;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;
        $total_pages = ceil($conn->query("SELECT COUNT(*) as count FROM reservations")->fetch_assoc()['count'] / $limit);
        
        $sql = "SELECT r.reservation_id, r.start_date, r.end_date, r.total_price, r.status,
                       CONCAT(u.first_name, ' ', u.last_name) AS user_full_name,
                       CONCAT(c.brand, ' ', c.model) AS car_name, c.license_plate
                FROM reservations AS r
                JOIN users AS u ON r.user_id = u.user_id
                JOIN cars AS c ON r.car_id = c.car_id
                ORDER BY r.reservation_id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $data = [
            'reservations' => $reservations,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
        $data['page_title'] = 'Rezervasyon Yönetimi';
        $data['breadcrumbs'] = [['name' => 'Rezervasyonlar', 'link' => null]];
        $this->loadView('reservations_list', $data);
    }

    public function showEditReservationForm() {
        $reservation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$reservation_id) { header("Location: /rentacar/public/admin/reservations"); exit(); }
        
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) AS user_full_name, CONCAT(c.brand, ' ', c.model) AS car_name
                FROM reservations AS r
                JOIN users AS u ON r.user_id = u.user_id
                JOIN cars AS c ON r.car_id = c.car_id
                WHERE r.reservation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $reservation = $stmt->get_result()->fetch_assoc();
        if (!$reservation) { header("Location: /rentacar/public/admin/reservations"); exit(); }
        
        $data = ['reservation' => $reservation];
        $data['page_title'] = 'Rezervasyon Düzenle';
        $data['breadcrumbs'] = [['name' => 'Rezervasyonlar', 'link' => '/rentacar/public/admin/reservations'], ['name' => 'Düzenle', 'link' => null]];
        $this->loadView('reservation_edit_form', $data);
    }

    public function updateReservation() {
        $reservation_id = $_POST['reservation_id'];
        $status = $_POST['status'];
        $allowed_statuses = ['Beklemede', 'Onaylandı', 'Tamamlandı', 'İptal Edildi'];

        if (in_array($status, $allowed_statuses)) {
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ?");
            $stmt->bind_param("si", $status, $reservation_id);
            $stmt->execute();
            $_SESSION['message'] = "Rezervasyon durumu başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        }
        header("Location: /rentacar/public/admin/reservations");
        exit();
    }

    public function deleteReservation() {
        $reservation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$reservation_id) { header("Location: /rentacar/public/admin/reservations"); exit(); }

        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Rezervasyon (ID: {$reservation_id}) başarıyla silindi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Hata: Rezervasyon silinemedi (Ödemesi olabilir).";
            $_SESSION['message_type'] = 'danger';
        }
        header("Location: /rentacar/public/admin/reservations");
        exit();
    }
    
    // --- KULLANICI YÖNETİMİ ---
    public function listUsers() {
        $conn = Database::getInstance()->getConnection();
        $limit = 10;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;
        $total_pages = ceil($conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'] / $limit);
        
        $sql = "SELECT user_id, first_name, last_name, email, role, created_at, status 
                FROM users ORDER BY user_id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $data = [
            'users' => $users,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
        $data['page_title'] = 'Kullanıcı Yönetimi';
        $data['breadcrumbs'] = [['name' => 'Kullanıcılar', 'link' => null]];
        $this->loadView('users_list', $data);
    }

    public function showEditUserForm() {
        $user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$user_id) { header("Location: /rentacar/public/admin/users"); exit(); }
        
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, role, status FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!$user) { header("Location: /rentacar/public/admin/users"); exit(); }
        
        $data = ['user' => $user];
        $data['page_title'] = 'Kullanıcı Düzenle';
        $data['breadcrumbs'] = [['name' => 'Kullanıcılar', 'link' => '/rentacar/public/admin/users'], ['name' => 'Düzenle', 'link' => null]];
        $this->loadView('user_edit_form', $data);
    }

    public function updateUser() {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        $allowed_roles = ['Customer', 'Admin'];
        $allowed_statuses = ['Active', 'Suspended', 'Pending'];
        
        if (in_array($role, $allowed_roles) && in_array($status, $allowed_statuses)) {
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare("UPDATE users SET role = ?, status = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $role, $status, $user_id);
            $stmt->execute();
            $_SESSION['message'] = "Kullanıcı bilgileri başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        }
        header("Location: /rentacar/public/admin/users");
        exit();
    }
    
    public function deleteUser() {
        $user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$user_id || $user_id == $_SESSION['user_id']) {
             header("Location: /rentacar/public/admin/users"); exit(); 
        }

        $conn = Database::getInstance()->getConnection();
        $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE user_id = ?");
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $reservation_count = $stmt_check->get_result()->fetch_assoc()['count'];
        $stmt_check->close();

        if ($reservation_count > 0) {
            $stmt_update = $conn->prepare("UPDATE users SET status = 'Suspended' WHERE user_id = ?");
            $stmt_update->bind_param("i", $user_id);
            $stmt_update->execute();
            $_SESSION['message'] = "Kullanıcının rezervasyon kayıtları bulunduğu için hesap askıya alındı.";
            $_SESSION['message_type'] = 'warning';
        } else {
            $stmt_delete = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt_delete->execute();
            $_SESSION['message'] = "Kullanıcı (ID: {$user_id}) başarıyla kalıcı olarak silindi.";
            $_SESSION['message_type'] = 'success';
        }
        header("Location: /rentacar/public/admin/users");
        exit();
    }
    
    // --- LOKASYON YÖNETİMİ ---
    public function listLocations() {
        $conn = Database::getInstance()->getConnection();
        $limit = 10;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;
        $total_pages = ceil($conn->query("SELECT COUNT(*) as count FROM locations")->fetch_assoc()['count'] / $limit);
        
        $stmt = $conn->prepare("SELECT * FROM locations ORDER BY location_id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $locations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $data = [
            'locations' => $locations,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
        $data['page_title'] = 'Lokasyon Yönetimi';
        $data['breadcrumbs'] = [['name' => 'Lokasyonlar', 'link' => null]];
        $this->loadView('locations_list', $data);
    }

    public function showCreateLocationForm() {
        $data['page_title'] = 'Yeni Lokasyon Ekle';
        $data['breadcrumbs'] = [['name' => 'Lokasyonlar', 'link' => '/rentacar/public/admin/locations'], ['name' => 'Yeni Ekle', 'link' => null]];
        $this->loadView('location_create_form', $data);
    }
    
    public function storeLocation() {
        $city = trim($_POST['city']);
        $location_name = trim($_POST['location_name']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $status = $_POST['status'];
        $full_address_for_search = $address;
        
        $coordinates = $this->getCoordinatesForAddress($full_address_for_search);
        $latitude = $coordinates['lat'] ?? null;
        $longitude = $coordinates['lon'] ?? null;

        $conn = Database::getInstance()->getConnection();
        $sql = "INSERT INTO locations (city, location_name, address, phone, status, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdd", $city, $location_name, $address, $phone, $status, $latitude, $longitude);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Lokasyon başarıyla eklendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Lokasyon eklenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/locations");
        exit();
    }
    
    public function showEditLocationForm() {
        $location_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$location_id) { header("Location: /rentacar/public/admin/locations"); exit(); }
        
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM locations WHERE location_id = ?");
        $stmt->bind_param("i", $location_id);
        $stmt->execute();
        $location = $stmt->get_result()->fetch_assoc();
        if (!$location) { header("Location: /rentacar/public/admin/locations"); exit(); }
        
        $data = ['location' => $location];
        $data['page_title'] = 'Lokasyon Düzenle';
        $data['breadcrumbs'] = [['name' => 'Lokasyonlar', 'link' => '/rentacar/public/admin/locations'], ['name' => 'Düzenle', 'link' => null]];
        $this->loadView('location_edit_form', $data);
    }

    public function updateLocation() {
        $location_id = $_POST['location_id'];
        $city = trim($_POST['city']);
        $location_name = trim($_POST['location_name']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $status = $_POST['status'];
        $full_address_for_search = $address;
        
        $coordinates = $this->getCoordinatesForAddress($full_address_for_search);
        $latitude = $coordinates['lat'] ?? null;
        $longitude = $coordinates['lon'] ?? null;

        $conn = Database::getInstance()->getConnection();
        $sql = "UPDATE locations SET city = ?, location_name = ?, address = ?, phone = ?, status = ?, latitude = ?, longitude = ? WHERE location_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssddi", $city, $location_name, $address, $phone, $status, $latitude, $longitude, $location_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Lokasyon başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Lokasyon güncellenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/locations");
        exit();
    }
    
    public function deleteLocation() {
        $location_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$location_id) { header("Location: /rentacar/public/admin/locations"); exit(); }
        
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("DELETE FROM locations WHERE location_id = ?");
        $stmt->bind_param("i", $location_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Lokasyon (ID: {$location_id}) başarıyla silindi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Hata: Lokasyon silinemedi. Bu lokasyona kayıtlı bir araç veya rezervasyon olabilir.";
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/locations");
        exit();
    }

    // --- KATEGORİ YÖNETİMİ ---
    public function listCategories() {
        $conn = Database::getInstance()->getConnection();
        $limit = 10;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;
        $total_pages = ceil($conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'] / $limit);
        
        $stmt = $conn->prepare("SELECT * FROM categories ORDER BY category_id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $data = [
            'categories' => $categories,
            'total_pages' => $total_pages,
            'current_page' => $page
        ];
        $data['page_title'] = 'Kategori Yönetimi';
        $data['breadcrumbs'] = [['name' => 'Kategoriler', 'link' => null]];
        $this->loadView('categories_list', $data);
    }

    public function showCreateCategoryForm() {
        $data['page_title'] = 'Yeni Kategori Ekle';
        $data['breadcrumbs'] = [['name' => 'Kategoriler', 'link' => '/rentacar/public/admin/categories'], ['name' => 'Yeni Ekle', 'link' => null]];
        $this->loadView('category_create_form', $data);
    }
    
    public function storeCategory() {
        $conn = Database::getInstance()->getConnection();
        $sql = "INSERT INTO categories (category_name, description, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $_POST['category_name'], $_POST['description'], $_POST['status']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Kategori başarıyla eklendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Kategori eklenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/categories");
        exit();
    }
    
    public function showEditCategoryForm() {
        $category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$category_id) { header("Location: /rentacar/public/admin/categories"); exit(); }
        
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $category = $stmt->get_result()->fetch_assoc();
        if (!$category) { header("Location: /rentacar/public/admin/categories"); exit(); }

        $data = ['category' => $category];
        $data['page_title'] = 'Kategori Düzenle';
        $data['breadcrumbs'] = [['name' => 'Kategoriler', 'link' => '/rentacar/public/admin/categories'], ['name' => 'Düzenle', 'link' => null]];
        $this->loadView('category_edit_form', $data);
    }

    public function updateCategory() {
        $conn = Database::getInstance()->getConnection();
        $sql = "UPDATE categories SET category_name = ?, description = ?, status = ? WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $_POST['category_name'], $_POST['description'], $_POST['status'], $_POST['category_id']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Kategori başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Kategori güncellenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/categories");
        exit();
    }

    public function deleteCategory() {
        $category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$category_id) { header("Location: /rentacar/public/admin/categories"); exit(); }
        
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Kategori (ID: {$category_id}) başarıyla silindi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Hata: Kategori silinemedi. Bu kategoriye kayıtlı araçlar olabilir.";
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        header("Location: /rentacar/public/admin/categories");
        exit();
    }
    
    // --- RAPORLAR ---
    public function showReports() {
        $conn = Database::getInstance()->getConnection();

        $avg_duration = $conn->query("SELECT AVG(TIMESTAMPDIFF(HOUR, start_date, end_date)) / 24 AS avg_days FROM reservations WHERE status = 'Tamamlandı'")->fetch_assoc()['avg_days'];
        $avg_rating = $conn->query("SELECT AVG(rating) AS avg_rating FROM reviews")->fetch_assoc()['avg_rating'];
        $total_customers = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'Customer'")->fetch_assoc()['count'];
        $total_cars = $conn->query("SELECT COUNT(*) AS count FROM cars")->fetch_assoc()['count'];

        $sql_top_cars = "SELECT CONCAT(c.brand, ' ', c.model) AS car_name, COUNT(r.reservation_id) AS rental_count 
                         FROM reservations r 
                         JOIN cars c ON r.car_id = c.car_id 
                         GROUP BY r.car_id 
                         ORDER BY rental_count DESC 
                         LIMIT 5";
        $top_cars_chart = $conn->query($sql_top_cars)->fetch_all(MYSQLI_ASSOC);

        $sql_monthly_revenue = "SELECT DATE_FORMAT(start_date, '%Y-%m') AS month, SUM(total_price) AS total_revenue 
                                FROM reservations 
                                WHERE status = 'Tamamlandı' 
                                GROUP BY month 
                                ORDER BY month DESC";
        $monthly_revenue_chart = $conn->query($sql_monthly_revenue)->fetch_all(MYSQLI_ASSOC);

        $most_profitable_cars_query = "SELECT CONCAT(c.brand, ' ', c.model) AS car_name, SUM(r.total_price) AS total_revenue 
                                       FROM reservations r 
                                       JOIN cars c ON r.car_id = c.car_id 
                                       WHERE r.status = 'Tamamlandı' 
                                       GROUP BY r.car_id 
                                       ORDER BY total_revenue DESC 
                                       LIMIT 5";
        $most_profitable_cars = $conn->query($most_profitable_cars_query)->fetch_all(MYSQLI_ASSOC);

        $least_profitable_cars_query = "SELECT CONCAT(c.brand, ' ', c.model) AS car_name, SUM(r.total_price) AS total_revenue 
                                        FROM reservations r 
                                        JOIN cars c ON r.car_id = c.car_id 
                                        WHERE r.status = 'Tamamlandı' 
                                        GROUP BY r.car_id 
                                        ORDER BY total_revenue ASC 
                                        LIMIT 5";
        $least_profitable_cars = $conn->query($least_profitable_cars_query)->fetch_all(MYSQLI_ASSOC);
        
        $data = [
            'avg_duration' => $avg_duration,
            'overall_avg_rating' => $avg_rating,
            'total_customers' => $total_customers,
            'total_cars' => $total_cars,
            'top_cars_chart' => $top_cars_chart,
            'monthly_revenue_chart' => $monthly_revenue_chart,
            'most_profitable_cars' => $most_profitable_cars,
            'least_profitable_cars' => $least_profitable_cars
        ];
        $data['page_title'] = 'Raporlar ve İstatistikler';
        $data['breadcrumbs'] = [['name' => 'Raporlar', 'link' => null]];
        $this->loadView('reports', $data);
    }
    
    // --- ADMİN PROFİLİ ---
    public function showAdminProfile() {
        $admin_user_id = $_SESSION['user_id'];
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT first_name, last_name, email, profile_image_url FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $admin_user_id);
        $stmt->execute();
        $admin_user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $data = [
            'user' => $admin_user,
            'page_title' => 'Admin Profili',
            'breadcrumbs' => [
                ['name' => 'Profilim', 'link' => null]
            ]
        ];
        $this->loadView('admin_profile', $data);
    }
    
    public function updateAdminProfile() {
        $admin_user_id = $_SESSION['user_id'];
        $conn = Database::getInstance()->getConnection();
        $stmt_user = $conn->prepare("SELECT password, profile_image_url FROM users WHERE user_id = ?");
        $stmt_user->bind_param("i", $admin_user_id);
        $stmt_user->execute();
        $user = $stmt_user->get_result()->fetch_assoc();
        $stmt_user->close();
        $profile_image_path = $user['profile_image_url'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/rentacar/public/images/avatars/";
            $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
            $file_name = "user_" . $admin_user_id . "_" . uniqid() . "." . $file_extension;
            $target_file_path = $target_dir . $file_name;
            $web_path = "/rentacar/public/images/avatars/" . $file_name; 
            $allowed_types = ['jpg', 'jpeg', 'png'];
            if ($_FILES["profile_image"]["size"] > 2097152) {
                $_SESSION['message'] = "Hata: Dosya boyutu çok büyük (En fazla 2MB).";
                $_SESSION['message_type'] = 'danger';
            } else if (!in_array($file_extension, $allowed_types)) {
                $_SESSION['message'] = "Hata: Sadece JPG, JPEG ve PNG dosyalarına izin verilir.";
                $_SESSION['message_type'] = 'danger';
            } else if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file_path)) {
                $profile_image_path = $web_path;
            } else {
                $_SESSION['message'] = "Hata: Resim sunucuya yüklenirken bir sorun oluştu. Klasör izinlerini kontrol edin.";
                $_SESSION['message_type'] = 'danger';
            }
            if (isset($_SESSION['message_type']) && $_SESSION['message_type'] === 'danger') {
                header("Location: /rentacar/public/admin/profile");
                exit();
            }
        }
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image_url = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $profile_image_path, $admin_user_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Profil bilgilerin başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['profile_image_url'] = $profile_image_path;
        } else {
            $_SESSION['message'] = "Profil güncellenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];
        if (!empty($current_password) && !empty($new_password) && !empty($confirm_new_password)) {
            if ($new_password !== $confirm_new_password) {
                $_SESSION['message'] = "Yeni şifreleriniz birbiriyle uyuşmuyor.";
                $_SESSION['message_type'] = 'danger';
            } else if (!password_verify($current_password, $user['password'])) {
                $_SESSION['message'] = "Girdiğiniz mevcut şifre yanlış.";
                $_SESSION['message_type'] = 'danger';
            } else {
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt_pass = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt_pass->bind_param("si", $new_hashed_password, $admin_user_id);
                $stmt_pass->execute();
                $stmt_pass->close();
                $_SESSION['message'] = "Profiliniz ve şifreniz başarıyla güncellendi.";
                $_SESSION['message_type'] = 'success';
            }
        }
        header("Location: /rentacar/public/admin/profile");
        exit();
    }
    
}

