<?php

namespace App\Controllers;

class ApiController {

    /**
     * Konumu belli olan, 'Müsait' durumdaki tüm araçları JSON formatında döndürür.
     */
    public function getAvailableCars() {
        require_once __DIR__ . '/../../config/db.php';

        $sql = "SELECT 
                    c.car_id, c.brand, c.model, c.daily_rate,
                    l.latitude, l.longitude
                FROM 
                    cars AS c
                JOIN 
                    locations AS l ON c.current_location_id = l.location_id
                WHERE 
                    c.status = 'Müsait' 
                    AND l.latitude IS NOT NULL 
                    AND l.longitude IS NOT NULL";

        $result = $conn->query($sql);
        $cars = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();

        header('Content-Type: application/json');
        echo json_encode($cars);
        exit();
    }
    public function getReservationsForCar() {
        $car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT);
        if (!$car_id) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Geçersiz araç IDsi']);
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        $stmt = $conn->prepare("SELECT start_date, end_date, status FROM reservations WHERE car_id = ? AND status IN ('Onaylandı', 'Beklemede')");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservations = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();

        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [
                'title' => 'Rezerve Edildi', // Takvimde görünecek başlık
                'start' => $reservation['start_date'], // Başlangıç tarihi
                'end' => $reservation['end_date'],     // Bitiş tarihi
                'backgroundColor' => '#dc3545', // Kırmızı renk
                'borderColor' => '#dc3545'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($events);
        exit();
    }

public function filterCars() {
    // Bu kodun tamamı HomeController'daki index() metoduyla neredeyse aynı.
    require_once __DIR__ . '/../../config/db.php';
    $limit = 9; $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1; $offset = ($page - 1) * $limit;
    $base_sql = " FROM cars WHERE status = 'Müsait'"; $conditions = []; $params = []; $types = '';
    if (!empty($_GET['category_id'])) { $conditions[] = "category_id = ?"; $types .= 'i'; $params[] = $_GET['category_id']; }
    if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) { $start_date = $_GET['start_date']; $end_date = $_GET['end_date']; $conditions[] = "car_id NOT IN (SELECT car_id FROM reservations WHERE status = 'Onaylandı' AND (? < end_date AND ? > start_date))"; $types .= 'ss'; array_push($params, $start_date, $end_date); }
    $where_clause = ''; if (!empty($conditions)) { $where_clause = " AND " . implode(' AND ', $conditions); }
    $total_sql = "SELECT COUNT(*) as count" . $base_sql . $where_clause; $stmt_total = $conn->prepare($total_sql);
    if (!empty($params)) { $stmt_total->bind_param($types, ...$params); }
    $stmt_total->execute(); $total_cars = $stmt_total->get_result()->fetch_assoc()['count']; $total_pages = ceil($total_cars / $limit); $stmt_total->close();
    $sql = "SELECT *" . $base_sql . $where_clause . " ORDER BY car_id DESC LIMIT ? OFFSET ?"; $types .= 'ii'; array_push($params, $limit, $offset);
    $stmt = $conn->prepare($sql); if (!empty($params)) { $stmt->bind_param($types, ...$params); }
    $stmt->execute(); $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close(); $conn->close();

    // Değişkenleri "extract" ile kullanılabilir hale getir
    extract([
        'cars' => $cars,
        'total_pages' => $total_pages,
        'current_page' => $page
    ]);

    // Tam sayfa yerine sadece "partials/car_list.php" dosyasının içeriğini döndür
    require_once __DIR__ . '/../../views/partials/car_list.php';
    exit();
}
public function calculatePrice() {
        // Gerekli sınıfları dahil et
        require_once __DIR__ . '/../BaseController.php';
        $baseController = new \App\BaseController(); // DateTime'ı kullanabilmek için

        $start_date_str = $_GET['start_date'];
        $end_date_str = $_GET['end_date'];
        $daily_rate = $_GET['daily_rate'];

        $response = [
            'success' => false,
            'total_price' => 0,
            'days' => 0,
            'message' => ''
        ];

        // Tarihlerin geçerli olup olmadığını kontrol et
        if (empty($start_date_str) || empty($end_date_str) || empty($daily_rate)) {
            $response['message'] = 'Eksik parametre.';
            echo json_encode($response);
            exit();
        }

        try {
            $start_date = new \DateTime($start_date_str);
            $end_date = new \DateTime($end_date_str);
            $now = new \DateTime();

            if ($start_date < $now || $start_date >= $end_date) {
                $response['message'] = 'Geçersiz tarih aralığı.';
            } else {
                // Fiyatı hesapla
                $interval = $start_date->diff($end_date);
                $days = $interval->days;
                if ($interval->h > 0 || $interval->i > 0 || $interval->s > 0) {
                    $days++;
                }
                $total_price = $days * $daily_rate;

                $response['success'] = true;
                $response['total_price'] = number_format($total_price, 2, ',', '.');
                $response['days'] = $days;
                $response['message'] = "$days günlük kiralama için toplam tutar.";
            }
        } catch (\Exception $e) {
            $response['message'] = 'Tarih formatı geçersiz.';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }




















    
}