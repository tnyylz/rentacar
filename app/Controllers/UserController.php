<?php

namespace App\Controllers;
use DateTime;
use App\BaseController;

class UserController extends BaseController {

    /**
     * Giriş yapmış kullanıcının rezervasyonlarını listeler.
     */
    
    public function showMyReservations() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /rentacar/public/home");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        
        require_once __DIR__ . '/../../config/db.php';
        
        // Önceki sorgunun aynısıyla TÜM rezervasyonları çekiyoruz.
        $sql = "SELECT r.reservation_id, r.start_date, r.end_date, r.total_price, r.status, c.brand, c.model
                FROM reservations AS r
                JOIN cars AS c ON r.car_id = c.car_id
                WHERE r.user_id = ?
                ORDER BY r.start_date DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $all_reservations = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        $conn->close();

        // --- YENİ MANTIK: Rezervasyonları ikiye ayırma ---
        $active_reservations = [];
        $past_reservations = [];
        $now = new DateTime();

        foreach ($all_reservations as $reservation) {
            $end_date = new DateTime($reservation['end_date']);
            
            // Eğer rezervasyonun bitiş tarihi geçmişse veya durumu Tamamlandı/İptal ise, 'Geçmiş' listesine ekle
            if ($end_date < $now || in_array($reservation['status'], ['Tamamlandı', 'İptal Edildi'])) {
                $past_reservations[] = $reservation;
            } else {
                // Değilse, 'Aktif ve Gelecek' listesine ekle
                $active_reservations[] = $reservation;
            }
        }
        // --- YENİ MANTIK SONU ---

        // View'e iki ayrı dizi gönder
        $this->loadView('my_reservations', [
            'active_reservations' => $active_reservations,
            'past_reservations' => $past_reservations
        ]);
    }


    public function showProfile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /rentacar/public/home");
            exit();
        }
        // Sadece view'i yüklüyoruz, bilgiler session'dan okunacak.
        $this->loadView('profile');
    }

    /**
     * Kullanıcının şifresini günceller.
     */
    public function updatePassword() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /rentacar/public/home");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        // 1. Doğrulama
        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $_SESSION['message'] = "Lütfen tüm şifre alanlarını doldurun.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/profile");
            exit();
        }

        if ($new_password !== $confirm_new_password) {
            $_SESSION['message'] = "Yeni şifreler uyuşmuyor.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/profile");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        // 2. Mevcut şifreyi kontrol et
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($current_password, $user['password'])) {
            $_SESSION['message'] = "Mevcut şifreniz yanlış.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/profile");
            exit();
        }

        // 3. Yeni şifreyi güncelle
        $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Şifreniz başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Şifre güncellenirken bir hata oluştu.";
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();
        header("Location: /rentacar/public/profile");
        exit();
    }
    
    

}