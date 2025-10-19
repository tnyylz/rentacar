<?php

namespace App\Controllers;
use App\BaseController;

class HomeController extends BaseController {

   public function index() {
        require_once __DIR__ . '/../../config/db.php';
        
        $testimonials_sql = "SELECT r.comment, u.first_name, u.last_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.show_on_homepage = 1 ORDER BY r.created_at DESC LIMIT 5";
        $testimonials = $conn->query($testimonials_sql)->fetch_all(MYSQLI_ASSOC);
        
        $categories = $conn->query("SELECT * FROM categories WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);

        $limit = 9; 
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;

        // --- DİNAMİK SQL SORGUSU BAŞLANGICI ---
        
        // Temel sorgu parçasını tanımlıyoruz
        $base_sql = "FROM cars c 
                     LEFT JOIN (
                         SELECT car_id, AVG(rating) as avg_rating, COUNT(*) as review_count
                         FROM reviews
                         GROUP BY car_id
                     ) as rev ON c.car_id = rev.car_id
                     WHERE c.status = 'Müsait'";

        $conditions = [];
        $params = [];
        $types = '';

        if (!empty($_GET['category_id'])) {
            $conditions[] = "c.category_id = ?";
            $types .= 'i';
            $params[] = $_GET['category_id'];
        }

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];
            $conditions[] = "c.car_id NOT IN (SELECT car_id FROM reservations WHERE status = 'Onaylandı' AND (? < end_date AND ? > start_date))";
            $types .= 'ss';
            array_push($params, $start_date, $end_date);
        }

        $where_clause = '';
        if (!empty($conditions)) {
            $where_clause = " AND " . implode(' AND ', $conditions);
        }
        
        // Toplam Sayfa Sayısını Hesapla
        $total_sql = "SELECT COUNT(c.car_id) as count " . $base_sql . $where_clause;
        $stmt_total = $conn->prepare($total_sql);
        if (!empty($params)) {
            $stmt_total->bind_param($types, ...$params);
        }
        $stmt_total->execute();
        $total_cars = $stmt_total->get_result()->fetch_assoc()['count'];
        $total_pages = ceil($total_cars / $limit);
        $stmt_total->close();

        // İlgili Sayfadaki Araçları Çek
        // SELECT kısmına ihtiyacımız olan yeni sütunları ekliyoruz
        $sql = "SELECT c.*, COALESCE(rev.avg_rating, 0) as avg_rating, COALESCE(rev.review_count, 0) as review_count "
               . $base_sql . $where_clause . " ORDER BY c.car_id DESC LIMIT ? OFFSET ?";
        
        $types .= 'ii';
        array_push($params, $limit, $offset);
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        $conn->close();

        $this->loadView('home', [
            'cars' => $cars,
            'categories' => $categories,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'testimonials' => $testimonials
        ]);
    }

    
}