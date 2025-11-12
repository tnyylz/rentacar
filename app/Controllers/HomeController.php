<?php

namespace App\Controllers;
use App\BaseController;
use DateTime;
class HomeController extends BaseController {

   public function index() {
        $conn = \App\Database::getInstance()->getConnection();
        
        $featured_cars_sql = "SELECT car_id, brand, model, year, image_url, transmission_type FROM cars WHERE status = 'Müsait' AND image_url IS NOT NULL ORDER BY RAND() LIMIT 8";
        $featured_cars = $conn->query($featured_cars_sql)->fetch_all(MYSQLI_ASSOC);
        
        $testimonials_sql = "SELECT r.comment, u.first_name, u.last_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.show_on_homepage = 1 ORDER BY r.created_at DESC LIMIT 5";
        $testimonials = $conn->query($testimonials_sql)->fetch_all(MYSQLI_ASSOC);
        $isSearchSubmitted = !empty($_GET);

        
        $categories = $conn->query("SELECT * FROM categories WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);

        // --- SAYFALAMA VE FİLTRELEME MANTIĞI ---
        $limit = 9; 
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;
        
        $base_sql = "FROM cars c 
                     LEFT JOIN (
                         SELECT car_id, AVG(rating) as avg_rating, COUNT(*) as review_count
                         FROM reviews
                         GROUP BY car_id
                     ) as rev ON c.car_id = rev.car_id
                     WHERE c.status = 'Müsait'";

        $conditions = []; $params = []; $types = '';

        // === FİLTRELEME MANTIĞI GÜNCELLENDİ ===

        // Lokasyon Filtresi
        if (!empty($_GET['pickup_location_id'])) {
            $conditions[] = "c.current_location_id = ?";
            $types .= 'i';
            $params[] = $_GET['pickup_location_id'];
        }

        // Tarih Filtresi
        if (!empty($_GET['pickup_date']) && !empty($_GET['return_date']) && !empty($_GET['pickup_time']) && !empty($_GET['return_time'])) {
            try {
                // 'DD.MM.YYYY' formatını 'YYYY-MM-DD' formatına çevir
                $start_date_obj = DateTime::createFromFormat('d.m.Y', $_GET['pickup_date']);
                $end_date_obj = DateTime::createFromFormat('d.m.Y', $_GET['return_date']);
                
                if ($start_date_obj && $end_date_obj) {
                    $start_datetime_sql = $start_date_obj->format('Y-m-d') . ' ' . $_GET['pickup_time'] . ':00';
                    $end_datetime_sql = $end_date_obj->format('Y-m-d') . ' ' . $_GET['return_time'] . ':00';

                    // Çakışma kontrolü
                    $conditions[] = "c.car_id NOT IN (SELECT car_id FROM reservations WHERE status = 'Onaylandı' AND (? < end_date AND ? > start_date))";
                    $types .= 'ss';
                    array_push($params, $start_datetime_sql, $end_datetime_sql);
                }
            } catch (\Exception $e) {
                // Geçersiz tarih formatı gelirse görmezden gel
            }
        }
        
        // Kategori Filtresi
        if (!empty($_GET['category_id'])) {
            $conditions[] = "c.category_id = ?";
            $types .= 'i';
            $params[] = $_GET['category_id'];
        }
        
        // === GÜNCELLEME SONU ===


        $where_clause = ''; if (!empty($conditions)) { $where_clause = " AND " . implode(' AND ', $conditions); }
        
        $total_sql = "SELECT COUNT(c.car_id) as count " . $base_sql . $where_clause;
        $stmt_total = $conn->prepare($total_sql);
        if (!empty($params)) { $stmt_total->bind_param($types, ...$params); }
        $stmt_total->execute();
        $total_cars = $stmt_total->get_result()->fetch_assoc()['count'];
        $total_pages = ceil($total_cars / $limit);
        $stmt_total->close();

        $sql = "SELECT c.*, COALESCE(rev.avg_rating, 0) as avg_rating, COALESCE(rev.review_count, 0) as review_count " . $base_sql . $where_clause . " ORDER BY c.car_id DESC LIMIT ? OFFSET ?";
        $types .= 'ii'; array_push($params, $limit, $offset);
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        $stmt->execute();
        $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $this->loadView('home', [
            'cars' => $cars,
            'categories' => $categories,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'testimonials' => $testimonials,
            'featured_cars' => $featured_cars,
            'isSearchSubmitted' => $isSearchSubmitted
        ]);
    }

public function showAboutPage() {
        // Bu sayfa statik olduğu için özel bir veriye ihtiyacı yok.
        // Sadece view'i yüklüyoruz.
        $this->loadView('about');
    }
    
}