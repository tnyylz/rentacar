<?php

namespace App\Controllers;
use App\BaseController;

class CarController extends BaseController   {

    /**
     * URL'den gelen ID'ye göre bir aracın detaylarını gösterir.
     */
   public function show() {
        $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$car_id) {
            $this->showNotFound();
        }

        require_once __DIR__ . '/../../config/db.php';
        
        // Aracı çek
        $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $car = $result->fetch_assoc();
        $stmt->close();

        if (!$car) {
            $conn->close();
            $this->showNotFound();
        }

        // Ortalama Puanı ve Yorum Sayısını Çek
        $rating_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE car_id = ?";
        $stmt_rating = $conn->prepare($rating_sql);
        $stmt_rating->bind_param("i", $car_id);
        $stmt_rating->execute();
        $rating_info = $stmt_rating->get_result()->fetch_assoc();
        $stmt_rating->close();

        // Yorumları Çek
        $reviews_sql = "SELECT r.rating, r.comment, r.created_at, u.first_name, u.last_name 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.user_id 
                        WHERE r.car_id = ? 
                        ORDER BY r.created_at DESC";
        $stmt_reviews = $conn->prepare($reviews_sql);
        $stmt_reviews->bind_param("i", $car_id);
        $stmt_reviews->execute();
        $reviews = $stmt_reviews->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_reviews->close();
        
        // --- YENİ EKLENEN KOD: YORUM YAPMA HAKKI KONTROLÜ ---
        $eligible_reservation_id = null;
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            
            // Bu kullanıcının, bu araç için, 'Tamamlandı' durumunda olan VE HENÜZ YORUM YAPILMAMIŞ bir rezervasyonu var mı?
            $check_sql = "SELECT r.reservation_id 
                          FROM reservations r
                          LEFT JOIN reviews rev ON r.reservation_id = rev.reservation_id
                          WHERE r.user_id = ? 
                          AND r.car_id = ? 
                          AND r.status = 'Tamamlandı' 
                          AND rev.review_id IS NULL 
                          LIMIT 1";

            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("ii", $user_id, $car_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                // Eğer varsa, yorum yapabileceği rezervasyonun ID'sini al
                $eligible_reservation_id = $result_check->fetch_assoc()['reservation_id'];
            }
            $stmt_check->close();
        }
        // --- YENİ KOD SONU ---

        $conn->close();

        // Tüm verileri view'e gönder
        $this->loadView('car_detail', [
            'car' => $car,
            'rating_info' => $rating_info,
            'reviews' => $reviews,
            'eligible_reservation_id' => $eligible_reservation_id // Değişken artık burada tanımlı ve gönderiliyor
        ]);
    }

 
    private function showNotFound() {
        http_response_code(404);
        require_once __DIR__ . '/../../views/404.php';
        exit();
    }
}