<?php

    namespace App\Controllers;

    class ReviewController {

        public function create() {
            if (!isset($_SESSION['user_id'])) {
                // Giriş yapmamışsa ana sayfaya at
                header("Location: /rentacar/public/home");
                exit();
            }

            $user_id = $_SESSION['user_id'];
            $car_id = $_POST['car_id'];
            $reservation_id = $_POST['reservation_id'];
            $rating = $_POST['rating'];
            $comment = trim($_POST['comment']);

            // Güvenlik: Yorum yapma hakkı var mı diye sunucu tarafında tekrar kontrol et.
            require_once __DIR__ . '/../../config/db.php';
            $check_sql = "SELECT r.reservation_id FROM reservations r LEFT JOIN reviews rev ON r.reservation_id = rev.reservation_id WHERE r.user_id = ? AND r.car_id = ? AND r.reservation_id = ? AND r.status = 'Tamamlandı' AND rev.review_id IS NULL";
            $stmt_check = $conn->prepare($check_sql);
            $stmt_check->bind_param("iii", $user_id, $car_id, $reservation_id);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows === 0) {
                // Eğer yetkisi yoksa veya zaten yorum yapmışsa işlemi reddet.
                $_SESSION['message'] = "Bu kiralama için yorum yapma yetkiniz yok.";
                $_SESSION['message_type'] = 'danger';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
            $stmt_check->close();

            // Yorumu veritabanına ekle
            $insert_sql = "INSERT INTO reviews (car_id, user_id, reservation_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("iiiis", $car_id, $user_id, $reservation_id, $rating, $comment);
            
            if ($stmt_insert->execute()) {
                $_SESSION['message'] = "Değerlendirmeniz için teşekkür ederiz!";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Yorum eklenirken bir hata oluştu.";
                $_SESSION['message_type'] = 'danger';
            }

            $stmt_insert->close();
            $conn->close();

            // Kullanıcıyı yorum yaptığı aracın detay sayfasına geri yönlendir.
            header("Location: /rentacar/public/car_detail?id=" . $car_id);
            exit();
        }
    }