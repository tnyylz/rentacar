<?php

namespace App\Controllers;
use App\Database;
use DateTime; // DateTime sınıfını kullan

class ApiController {

    public function getAvailableCars() {
        // Bu metot harita için, dokunmuyoruz.
        require_once __DIR__ . '/../../config/db.php';
        $sql = "SELECT c.car_id, c.brand, c.model, c.daily_rate, l.latitude, l.longitude
                FROM cars AS c
                JOIN locations AS l ON c.current_location_id = l.location_id
                WHERE c.status = 'Müsait' AND l.latitude IS NOT NULL AND l.longitude IS NOT NULL";
        $result = $conn->query($sql);
        $cars = $result->fetch_all(MYSQLI_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($cars);
        exit();
    }

    public function getReservationsForCar() {
        // Bu metot takvim için, dokunmuyoruz.
        $car_id = filter_input(INPUT_GET, 'car_id', FILTER_VALIDATE_INT);
        if (!$car_id) { http_response_code(400); echo json_encode(['error' => 'Geçersiz araç IDsi']); exit(); }
        require_once __DIR__ . '/../../config/db.php';
        $stmt = $conn->prepare("SELECT start_date, end_date, status FROM reservations WHERE car_id = ? AND status IN ('Onaylandı', 'Beklemede')");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [ 'title' => 'Rezerve Edildi', 'start' => $reservation['start_date'], 'end' => $reservation['end_date'], 'backgroundColor' => '#dc3545', 'borderColor' => '#dc3545' ];
        }
        header('Content-Type: application/json');
        echo json_encode($events);
        exit();
    }

   public function calculatePrice() {
        // Yeni form alanlarını al
        $start_date_str = $_GET['pickup_date'];
        $start_time_str = $_GET['pickup_time'];
        $end_date_str = $_GET['return_date'];
        $end_time_str = $_GET['return_time'];
        $daily_rate = $_GET['daily_rate'];

        $response = [
            'success' => false,
            'total_price' => 0,
            'days' => 0,
            'message' => ''
        ];

        if (empty($start_date_str) || empty($end_date_str) || empty($start_time_str) || empty($end_time_str) || empty($daily_rate)) {
            $response['message'] = 'Tüm tarih ve saat alanları zorunludur.';
            echo json_encode($response);
            exit();
        }

        try {
            // Tarihleri SQL formatına çevir
            $start_date_obj = DateTime::createFromFormat('d.m.Y', $start_date_str);
            $end_date_obj = DateTime::createFromFormat('d.m.Y', $end_date_str);

            if (!$start_date_obj || !$end_date_obj) {
                throw new \Exception("Geçersiz tarih formatı.");
            }

            $start_datetime_sql = $start_date_obj->format('Y-m-d') . ' ' . $start_time_str . ':00';
            $end_datetime_sql = $end_date_obj->format('Y-m-d') . ' ' . $end_time_str . ':00';
            
            $start_date = new DateTime($start_datetime_sql);
            $end_date = new DateTime($end_datetime_sql);
            $now = new DateTime();

            if ($start_date < $now || $start_date >= $end_date) {
                $response['message'] = 'Geçersiz tarih aralığı.';
            } else {
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
            $response['message'] = 'Tarih formatı geçersiz: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    /**
     * AJAX ile araçları filtreler ve SADECE HTML parçasını döndürür.
     * BU, SENİN SORUNUNU ÇÖZECEK OLAN GÜNCELLENMİŞ KOD.
     */
    public function filterCars() {
        $conn = Database::getInstance()->getConnection();
        
        $cars = [];
        $total_pages = 0;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $isSearchSubmitted = true; // AJAX her zaman bir arama sonucudur
        $limit = 9; 
        $offset = ($page - 1) * $limit;
        
        $base_sql = "FROM cars c 
                     LEFT JOIN (
                         SELECT car_id, AVG(rating) as avg_rating, COUNT(*) as review_count
                         FROM reviews
                         GROUP BY car_id
                     ) as rev ON c.car_id = rev.car_id
                     WHERE c.status = 'Müsait'";

        $conditions = []; $params = []; $types = '';

        // --- FİLTRE 1: Lokasyon (YENİ FORM ALANINA GÖRE GÜNCELLENDİ) ---
        if (!empty($_GET['pickup_location_id'])) {
            $conditions[] = "c.current_location_id = ?";
            $types .= 'i';
            $params[] = $_GET['pickup_location_id'];
        }

        // --- FİLTRE 2: Tarih Aralığı (YENİ FORM ALANLARINA GÖRE GÜNCELLENDİ) ---
        if (!empty($_GET['pickup_date']) && !empty($_GET['return_date']) && !empty($_GET['pickup_time']) && !empty($_GET['return_time'])) {
            try {
                $start_date_obj = DateTime::createFromFormat('d.m.Y', $_GET['pickup_date']);
                $end_date_obj = DateTime::createFromFormat('d.m.Y', $_GET['return_date']);
                
                if ($start_date_obj && $end_date_obj) {
                    $start_datetime_sql = $start_date_obj->format('Y-m-d') . ' ' . $_GET['pickup_time'] . ':00';
                    $end_datetime_sql = $end_date_obj->format('Y-m-d') . ' ' . $_GET['return_time'] . ':00';
                    $conditions[] = "c.car_id NOT IN (SELECT car_id FROM reservations WHERE status = 'Onaylandı' AND (? < end_date AND ? > start_date))";
                    $types .= 'ss';
                    array_push($params, $start_datetime_sql, $end_datetime_sql);
                }
            } catch (\Exception $e) { /* Geçersiz tarih formatı */ }
        }
        
        // --- FİLTRE 3: Araç Tipi (Kategori) (Formdan kaldırıldı ama altyapısı duruyor) ---
        if (!empty($_GET['category_id'])) {
            $conditions[] = "c.category_id = ?";
            $types .= 'i';
            $params[] = $_GET['category_id'];
        }

        $where_clause = ''; if (!empty($conditions)) { $where_clause = " AND " . implode(' AND ', $conditions); }
        
        // Toplam Sayfayı Hesapla
        $total_sql = "SELECT COUNT(c.car_id) as count " . $base_sql . $where_clause;
        $stmt_total = $conn->prepare($total_sql);
        if (!empty($params)) { $stmt_total->bind_param($types, ...$params); }
        $stmt_total->execute();
        $total_cars = $stmt_total->get_result()->fetch_assoc()['count'];
        $total_pages = ceil($total_cars / $limit);
        $stmt_total->close();

        // Araçları Çek
        $sql = "SELECT c.*, COALESCE(rev.avg_rating, 0) as avg_rating, COALESCE(rev.review_count, 0) as review_count " . $base_sql . $where_clause . " ORDER BY c.car_id DESC LIMIT ? OFFSET ?";
        $types .= 'ii'; array_push($params, $limit, $offset);
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        $stmt->execute();
        $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        extract([
            'cars' => $cars,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'isSearchSubmitted' => $isSearchSubmitted
        ]);

        // Sadece HTML parçasını döndür
        require_once __DIR__ . '/../../views/partials/car_list.php';
        exit();
    }
}

