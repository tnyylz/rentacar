<?php

namespace App\Controllers;
use App\BaseController;

class HomeController extends BaseController {

    public function index() {
        require_once __DIR__ . '/../../config/db.php';
        
        // Formdaki dropdown menülerini doldurmak için kategorileri en başta çek
        $categories = $conn->query("SELECT * FROM categories WHERE status = 'Active'")->fetch_all(MYSQLI_ASSOC);

        // --- SAYFALAMA VE FİLTRELEME MANTIĞI ---

        // 1. Ayarlar
        $limit = 9; 
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $offset = ($page - 1) * $limit;

        // 2. Dinamik SQL Sorgusu Oluşturma
        $base_sql = " FROM cars WHERE status = 'Müsait'";
        $conditions = [];
        $params = [];
        $types = '';

        // Kategori filtresi
        if (!empty($_GET['category_id'])) {
            $conditions[] = "category_id = ?";
            $types .= 'i';
            $params[] = $_GET['category_id'];
        }

        // Tarih filtresi
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];
            $conditions[] = "car_id NOT IN (SELECT car_id FROM reservations WHERE status = 'Onaylandı' AND (? < end_date AND ? > start_date))";
            $types .= 'ss';
            array_push($params, $start_date, $end_date);
        }

        $where_clause = '';
        if (!empty($conditions)) {
            $where_clause = " AND " . implode(' AND ', $conditions);
        }
        
        // 3. Toplam Sayfa Sayısını Hesaplamak İçin Toplam Kayıt Sayısını Al
        $total_sql = "SELECT COUNT(*) as count" . $base_sql . $where_clause;
        $stmt_total = $conn->prepare($total_sql);
        if (!empty($params)) {
            $stmt_total->bind_param($types, ...$params);
        }
        $stmt_total->execute();
        $total_cars = $stmt_total->get_result()->fetch_assoc()['count'];
        $total_pages = ceil($total_cars / $limit);
        $stmt_total->close();


        // 4. Sadece İlgili Sayfadaki Araçları Çek
        $sql = "SELECT *" . $base_sql . $where_clause . " ORDER BY car_id DESC LIMIT ? OFFSET ?";
        $types .= 'ii'; // LIMIT ve OFFSET için iki integer
        array_push($params, $limit, $offset);
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        $conn->close();

        // --- DOĞRU YER ---
        // 5. Tüm verileri, hepsi tanımlandıktan sonra view'e gönder
        $this->loadView('home', [
            'cars' => $cars,
            'categories' => $categories,
            'total_pages' => $total_pages,
            'current_page' => $page
        ]);
    }

    
}