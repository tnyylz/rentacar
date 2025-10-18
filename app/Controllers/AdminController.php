<?php

namespace App\Controllers;
use App\BaseController;

class AdminController extends BaseController {

    /**
     * Bu controller'daki herhangi bir metot çalışmadan önce bu metot çalışır.
     * Güvenlik kontrolünü burada yapmak, her metoda tek tek eklemekten daha verimlidir.
     */
    public function __construct() {
        // Session'da kullanıcı bilgisi var mı VE bu kullanıcının rolü 'Admin' mi?
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            // Eğer değilse, yetkisi yok demektir. Ana sayfaya yönlendir ve script'i durdur.
            $_SESSION['message'] = "Bu alana erişim yetkiniz yok.";
            $_SESSION['message_type'] = 'danger';
            header('Location: /rentacar/public/home');
            exit();
        }
    }

    /**
     * Admin paneli ana sayfasını (dashboard) gösterir.
     */
    public function dashboard() {
        require_once __DIR__ . '/../../config/db.php';

        // Kartlar için verileri çek
        $total_cars = $conn->query("SELECT COUNT(*) AS count FROM cars")->fetch_assoc()['count'];
        $pending_reservations = $conn->query("SELECT COUNT(*) AS count FROM reservations WHERE status = 'Beklemede'")->fetch_assoc()['count'];
        $total_customers = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'Customer'")->fetch_assoc()['count'];
        
        // Ana sayfada listelemek için son 5 rezervasyonu çek
        $sql_recent_reservations = "SELECT 
                                        r.reservation_id, 
                                        CONCAT(u.first_name, ' ', u.last_name) AS user_full_name,
                                        CONCAT(c.brand, ' ', c.model) AS car_name,
                                        r.status
                                    FROM 
                                        reservations AS r
                                    JOIN 
                                        users AS u ON r.user_id = u.user_id
                                    JOIN 
                                        cars AS c ON r.car_id = c.car_id
                                    ORDER BY 
                                        r.reservation_id DESC
                                    LIMIT 5";

        $recent_reservations = $conn->query($sql_recent_reservations)->fetch_all(MYSQLI_ASSOC);

        $conn->close();

        // Tüm verileri bir dizi içinde view'e gönder
        $data = [
            'total_cars' => $total_cars,
            'pending_reservations' => $pending_reservations,
            'total_customers' => $total_customers,
            'recent_reservations' => $recent_reservations
        ];
        
        $this->loadView('admin/dashboard', $data);
    }
    

    public function listCars() {
        require_once __DIR__ . '/../../config/db.php';

        // 1. Ayarları Belirle
        $limit = 10; // Sayfa başına gösterilecek araç sayısı
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1; // URL'den gelen sayfa numarası, yoksa 1. sayfa
        $offset = ($page - 1) * $limit; // SQL'in ne kadar veri atlayacağını hesapla

        // 2. Toplam araç sayısını al (toplam sayfa sayısını hesaplamak için)
        $total_cars_result = $conn->query("SELECT COUNT(*) as count FROM cars");
        $total_cars = $total_cars_result->fetch_assoc()['count'];
        $total_pages = ceil($total_cars / $limit); // Sayfa sayısını yukarı yuvarla

        // 3. Sadece ilgili sayfadaki araçları çekmek için LIMIT ve OFFSET kullan
        $sql = "SELECT c.*, cat.category_name, loc.location_name
                FROM cars AS c
                LEFT JOIN categories AS cat ON c.category_id = cat.category_id
                LEFT JOIN locations AS loc ON c.current_location_id = loc.location_id
                ORDER BY c.car_id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $cars = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        $conn->close();

        // 4. Tüm sayfalama verilerini view'e gönder
        $this->loadView('admin/cars_list', [
            'cars' => $cars,
            'total_pages' => $total_pages,
            'current_page' => $page
        ]);
    }


public function showCreateCarForm() {
    require_once __DIR__ . '/../../config/db.php';

    // Formdaki <select> menülerini doldurmak için kategorileri ve lokasyonları çek
    $categories = $conn->query("SELECT * FROM categories WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
    $locations = $conn->query("SELECT * FROM locations WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);

    $conn->close();

    // Verileri view'e gönder
    $this->loadView('admin/car_create_form', [
        'categories' => $categories,
        'locations' => $locations
    ]);
}

public function storeCar() {
        // Formdan gelen verileri al ve temizle
        $brand = trim($_POST['brand']);
        $model = trim($_POST['model']);
        $year = trim($_POST['year']);
        $license_plate = trim($_POST['license_plate']);
        $daily_rate = trim($_POST['daily_rate']);
        $category_id = $_POST['category_id'];
        $fuel_type = $_POST['fuel_type'];
        $transmission_type = $_POST['transmission_type'];
        $current_location_id = $_POST['current_location_id'];
        // Yeni eklenen araç varsayılan olarak 'Müsait' olsun
        $status = 'Müsait';

        // TODO: Sunucu tarafı doğrulama (validation) eklemek iyi bir pratiktir.
        
        require_once __DIR__ . '/../../config/db.php';

        $sql = "INSERT INTO cars (brand, model, year, license_plate, daily_rate, category_id, fuel_type, transmission_type, current_location_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        // 's' - string, 'i' - integer, 'd' - double (ondalıklı sayı)
        $stmt->bind_param("ssisdisiss", $brand, $model, $year, $license_plate, $daily_rate, $category_id, $fuel_type, $transmission_type, $current_location_id, $status);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Araç başarıyla eklendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Araç eklenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        // İşlem bittikten sonra araç listesi sayfasına geri yönlendir
        header("Location: /rentacar/public/admin/cars");
        exit();
    }

public function showEditCarForm() {
    $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$car_id) {
        // Geçersiz ID ise listeye geri yönlendir
        header("Location: /rentacar/public/admin/cars");
        exit();
    }

    require_once __DIR__ . '/../../config/db.php';

    // Düzenlenecek aracı çek
    $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $stmt->close();

    // Eğer araç bulunamazsa listeye geri yönlendir
    if (!$car) {
        header("Location: /rentacar/public/admin/cars");
        exit();
    }

    // Formdaki dropdown menüler için kategorileri ve lokasyonları çek
    $categories = $conn->query("SELECT * FROM categories WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);
    $locations = $conn->query("SELECT * FROM locations WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);

    $conn->close();

    $this->loadView('admin/car_edit_form', [
        'car' => $car,
        'categories' => $categories,
        'locations' => $locations
    ]);
}

/**
     * Bir aracın bilgilerini günceller.
     */
    /**
     * Bir aracın bilgilerini günceller.
     */
    public function updateCar() {
        // Formdan gelen verileri al
        $car_id = $_POST['car_id'];
        $brand = trim($_POST['brand']);
        $model = trim($_POST['model']);
        $year = trim($_POST['year']);
        $license_plate = trim($_POST['license_plate']);
        $daily_rate = trim($_POST['daily_rate']);
        $category_id = $_POST['category_id'];
        $current_location_id = $_POST['current_location_id'];
        $status = $_POST['status']; // YENİ EKLENDİ

        require_once __DIR__ . '/../../config/db.php';

        // SQL sorgusunu 'status'ü de içerecek şekilde güncelle
        $sql = "UPDATE cars SET 
                    brand = ?, 
                    model = ?, 
                    year = ?, 
                    license_plate = ?, 
                    daily_rate = ?, 
                    category_id = ?,
                    current_location_id = ?,
                    status = ?
                WHERE 
                    car_id = ?";
        
        $stmt = $conn->prepare($sql);
        // bind_param'a yeni değişkenleri ve tiplerini ekle (sondaki 's' string'i temsil eder)
        $stmt->bind_param("ssisdiisi", $brand, $model, $year, $license_plate, $daily_rate, $category_id, $current_location_id, $status, $car_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Araç başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Araç güncellenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/cars");
        exit();
    }


    public function deleteCar() {
        // URL'den gelen ID'yi al ve doğrula
        $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$car_id) {
            // Geçersiz ID ise hata mesajı ver ve listeye geri dön
            $_SESSION['message'] = "Geçersiz araç ID'si.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/admin/cars");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        $sql = "DELETE FROM cars WHERE car_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $car_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Araç (ID: {$car_id}) başarıyla silindi.";
            $_SESSION['message_type'] = 'success';
        } else {
            // Foreign key hatası olabilir, bu durumu da ele alalım.
            $_SESSION['message'] = "Hata: Araç silinemedi. Bu araca ait aktif bir rezervasyon olabilir.";
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        // İşlem bittikten sonra araç listesi sayfasına geri yönlendir
        header("Location: /rentacar/public/admin/cars");
        exit();
    }
public function listReservations() {
        require_once __DIR__ . '/../../config/db.php';

        // Birden fazla tablodan bilgi almak için JOIN'ler kullanıyoruz.
        // Hangi kullanıcının, hangi aracı, nereden nereye kiraladığını tek sorguda öğreniyoruz.
        $sql = "SELECT 
                    r.reservation_id, r.start_date, r.end_date, r.total_price, r.status, r.created_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS user_full_name,
                    CONCAT(c.brand, ' ', c.model) AS car_name,
                    c.license_plate,
                    pickup_loc.location_name AS pickup_location,
                    dropoff_loc.location_name AS dropoff_location
                FROM 
                    reservations AS r
                JOIN 
                    users AS u ON r.user_id = u.user_id
                JOIN 
                    cars AS c ON r.car_id = c.car_id
                JOIN 
                    locations AS pickup_loc ON r.pickup_location_id = pickup_loc.location_id
                JOIN 
                    locations AS dropoff_loc ON r.dropoff_location_id = dropoff_loc.location_id
                ORDER BY 
                    r.reservation_id DESC";

        $result = $conn->query($sql);
        $reservations = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();

        // Rezervasyon verilerini 'reservations_list' view'ine gönder
        $this->loadView('admin/reservations_list', ['reservations' => $reservations]);
    }




public function showEditReservationForm() {
    $reservation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$reservation_id) {
        header("Location: /rentacar/public/admin/reservations");
        exit();
    }

    require_once __DIR__ . '/../../config/db.php';

    // listReservations() metodundaki sorgunun aynısını tek bir ID için kullanıyoruz.
    $sql = "SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) AS user_full_name, CONCAT(c.brand, ' ', c.model) AS car_name
            FROM reservations AS r
            JOIN users AS u ON r.user_id = u.user_id
            JOIN cars AS c ON r.car_id = c.car_id
            WHERE r.reservation_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$reservation) {
        header("Location: /rentacar/public/admin/reservations");
        exit();
    }

    $this->loadView('admin/reservation_edit_form', ['reservation' => $reservation]);
}

public function updateReservation() {
        $reservation_id = $_POST['reservation_id'];
        $status = $_POST['status'];

        // Olası durumları bir dizide tutarak güvenliği artıralım
        $allowed_statuses = ['Beklemede', 'Onaylandı', 'Tamamlandı', 'İptal Edildi'];
        if (!in_array($status, $allowed_statuses)) {
            $_SESSION['message'] = "Geçersiz durum bilgisi gönderildi.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/admin/reservations");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        $sql = "UPDATE reservations SET status = ? WHERE reservation_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $reservation_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Rezervasyon durumu başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Güncelleme sırasında bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/reservations");
        exit();
    }

public function listUsers() {
        require_once __DIR__ . '/../../config/db.php';

        // Şifre hariç tüm kullanıcı bilgilerini çekiyoruz.
        $sql = "SELECT user_id, first_name, last_name, email, role, created_at, status 
                FROM users 
                ORDER BY user_id DESC";

        $result = $conn->query($sql);
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();

        // Kullanıcı verilerini 'users_list' view'ine gönder
        $this->loadView('admin/users_list', ['users' => $users]);
    }


public function showEditUserForm() {
    $user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$user_id) {
        header("Location: /rentacar/public/admin/users");
        exit();
    }

require_once __DIR__ . '/../../config/db.php';

    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, role, status FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$user) {
        header("Location: /rentacar/public/admin/users");
        exit();
    }

    $this->loadView('admin/ user_edit_form', ['user' => $user]);
}    



public function updateUser() {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        // Güvenlik için izin verilen değerleri kontrol et
        $allowed_roles = ['Customer', 'Admin'];
        $allowed_statuses = ['Active', 'Suspended', 'Pending'];

        if (!in_array($role, $allowed_roles) || !in_array($status, $allowed_statuses)) {
            $_SESSION['message'] = "Geçersiz rol veya durum bilgisi gönderildi.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/admin/users");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';
        $sql = "UPDATE users SET role = ?, status = ? WHERE user_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $role, $status, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Kullanıcı bilgileri başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Güncelleme sırasında bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/users");
        exit();
    }

public function listLocations() {
        require_once __DIR__ . '/../../config/db.php';

        $sql = "SELECT * FROM locations ORDER BY location_id DESC";

        $result = $conn->query($sql);
        $locations = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();

        $this->loadView('admin/locations_list', ['locations' => $locations]);
    }

public function showCreateLocationForm() {
    // Bu metot sadece view'i yükler.
    $this->loadView('admin/location_create_form');
}
public function storeLocation() {
        // Formdan gelen verileri al ve temizle
        $city = trim($_POST['city']);
        $location_name = trim($_POST['location_name']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $status = $_POST['status'];

        require_once __DIR__ . '/../../config/db.php';

        $sql = "INSERT INTO locations (city, location_name, address, phone, status) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $city, $location_name, $address, $phone, $status);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Lokasyon başarıyla eklendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Lokasyon eklenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        // İşlem bittikten sonra lokasyon listesi sayfasına geri yönlendir
        header("Location: /rentacar/public/admin/locations");
        exit();
    }
public function showEditLocationForm() {
    $location_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$location_id) {
        header("Location: /rentacar/public/admin/locations");
        exit();
    }

    require_once __DIR__ . '/../../config/db.php';

    $stmt = $conn->prepare("SELECT * FROM locations WHERE location_id = ?");
    $stmt->bind_param("i", $location_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $location = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$location) {
        header("Location: /rentacar/public/admin/locations");
        exit();
    }

    $this->loadView('admin/location_edit_form', ['location' => $location]);
}

public function updateLocation() {
        $location_id = $_POST['location_id'];
        $city = trim($_POST['city']);
        $location_name = trim($_POST['location_name']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $status = $_POST['status'];

        require_once __DIR__ . '/../../config/db.php';

        $sql = "UPDATE locations SET city = ?, location_name = ?, address = ?, phone = ?, status = ? WHERE location_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $city, $location_name, $address, $phone, $status, $location_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Lokasyon başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Lokasyon güncellenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/locations");
        exit();
    }

public function deleteLocation() {
        $location_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$location_id) {
            header("Location: /rentacar/public/admin/locations");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        $sql = "DELETE FROM locations WHERE location_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $location_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Lokasyon (ID: {$location_id}) başarıyla silindi.";
            $_SESSION['message_type'] = 'success';
        } else {
            // Yabancı anahtar (foreign key) hatası olabilir.
            $_SESSION['message'] = "Hata: Lokasyon silinemedi. Bu lokasyona kayıtlı bir araç veya rezervasyon olabilir.";
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/locations");
        exit();
    }
    public function listCategories() {
        require_once __DIR__ . '/../../config/db.php';

        $sql = "SELECT * FROM categories ORDER BY category_id DESC";

        $result = $conn->query($sql);
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();

        $this->loadView('admin/categories_list', ['categories' => $categories]);
    }


    public function showCreateCategoryForm() {
    $this->loadView('admin/category_create_form');
    }
    public function storeCategory() {
        $category_name = trim($_POST['category_name']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];

        require_once __DIR__ . '/../../config/db.php';

        $sql = "INSERT INTO categories (category_name, description, status) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $category_name, $description, $status);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Kategori başarıyla eklendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Kategori eklenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/categories");
        exit();
    }
    public function showEditCategoryForm() {
        $category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$category_id) {
            header("Location: /rentacar/public/admin/categories");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        if (!$category) {
            header("Location: /rentacar/public/admin/categories");
            exit();
        }

        $this->loadView('admin/category_edit_form', ['category' => $category]);
    }

    /**
     * Bir kategorinin bilgilerini günceller.
     */
    public function updateCategory() {
        $category_id = $_POST['category_id'];
        $category_name = trim($_POST['category_name']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];

        require_once __DIR__ . '/../../config/db.php';

        $sql = "UPDATE categories SET category_name = ?, description = ?, status = ? WHERE category_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $category_name, $description, $status, $category_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Kategori başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Kategori güncellenirken bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/categories");
        exit();
    }

    /**
     * Verilen ID'ye sahip kategoriyi veritabanından siler.
     */
    public function deleteCategory() {
        $category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$category_id) {
            header("Location: /rentacar/public/admin/categories");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        $sql = "DELETE FROM categories WHERE category_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Kategori (ID: {$category_id}) başarıyla silindi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Hata: Kategori silinemedi. Bu kategoriye kayıtlı araçlar olabilir.";
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();

        header("Location: /rentacar/public/admin/categories");
        exit();
    }    






public function showReports() {
        require_once __DIR__ . '/../../config/db.php';

        // 1. Rapor: En çok kiralanan 5 araç
        $sql_top_cars = "SELECT 
                            CONCAT(c.brand, ' ', c.model) AS car_name, 
                            COUNT(r.reservation_id) AS rental_count 
                        FROM 
                            reservations r 
                        JOIN 
                            cars c ON r.car_id = c.car_id 
                        GROUP BY 
                            r.car_id 
                        ORDER BY 
                            rental_count DESC 
                        LIMIT 5";
        $top_cars = $conn->query($sql_top_cars)->fetch_all(MYSQLI_ASSOC);

        // 2. Rapor: Aylık kazançlar (sadece 'Tamamlandı' durumundaki rezervasyonlardan)
        $sql_monthly_revenue = "SELECT 
                                    DATE_FORMAT(start_date, '%Y-%m') AS month, 
                                    SUM(total_price) AS total_revenue 
                                FROM 
                                    reservations 
                                WHERE 
                                    status = 'Tamamlandı' 
                                GROUP BY 
                                    month 
                                ORDER BY 
                                    month DESC";
        $monthly_revenue = $conn->query($sql_monthly_revenue)->fetch_all(MYSQLI_ASSOC);

        $conn->close();

        // Rapor verilerini view'e gönder
        $this->loadView('admin/reports', [
            'top_cars' => $top_cars,
            'monthly_revenue' => $monthly_revenue
        ]);
    }







  
}