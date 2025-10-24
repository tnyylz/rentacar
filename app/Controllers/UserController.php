<?php

namespace App\Controllers;
use App\BaseController;
use DateTime;

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
        
        $conn = \App\Database::getInstance()->getConnection();
        
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

        // --- YENİ MANTIK: Rezervasyonları ikiye ayırma ---
        $active_reservations = [];
        $past_reservations = [];
        $now = new DateTime();

        foreach ($all_reservations as $reservation) {
            $end_date = new DateTime($reservation['end_date']);
            
            if ($end_date < $now || in_array($reservation['status'], ['Tamamlandı', 'İptal Edildi'])) {
                $past_reservations[] = $reservation;
            } else {
                $active_reservations[] = $reservation;
            }
        }
        // --- YENİ MANTIK SONU ---

        $this->loadView('my_reservations', [
            'active_reservations' => $active_reservations,
            'past_reservations' => $past_reservations
        ]);
    }


    /**
     * Müşterinin profil sayfasını gösterir.
     */
    public function showProfile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /rentacar/public/home");
            exit();
        }
        
        $conn = \App\Database::getInstance()->getConnection();
        
        // KULLANICININ TÜM BİLGİLERİNİ ÇEK (RESİM YOLU DAHİL)
        $stmt = $conn->prepare("SELECT first_name, last_name, email, profile_image_url FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $this->loadView('profile', ['user' => $user]);
    }

    /**
     * Müşterinin profil bilgilerini ve şifresini günceller.
     */
    public function updateProfile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /rentacar/public/home");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $conn = \App\Database::getInstance()->getConnection();

        // Önce mevcut kullanıcı verilerini çek
        $stmt_user = $conn->prepare("SELECT password, profile_image_url FROM users WHERE user_id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $user = $stmt_user->get_result()->fetch_assoc();
        $stmt_user->close();

        $profile_image_path = $user['profile_image_url'];

        // 1. Resim Yükleme Mantığı
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/rentacar/public/images/avatars/";
            
            $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
            $file_name = "user_" . $user_id . "_" . uniqid() . "." . $file_extension;
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
                $_SESSION['message'] = "Hata: Resim sunucuya yüklenirken bir sorun oluştu.";
                $_SESSION['message_type'] = 'danger';
            }
            
            if (isset($_SESSION['message_type']) && $_SESSION['message_type'] === 'danger') {
                header("Location: /rentacar/public/profile");
                exit();
            }
        }

        // 2. Temel Bilgileri Güncelle
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        
        $stmt_update = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image_url = ? WHERE user_id = ?");
        $stmt_update->bind_param("ssssi", $first_name, $last_name, $email, $profile_image_path, $user_id);
        
        if ($stmt_update->execute()) {
            $_SESSION['message'] = "Profil bilgilerin başarıyla güncellendi.";
            $_SESSION['message_type'] = 'success';
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['profile_image_url'] = $profile_image_path;
        } else {
            $_SESSION['message'] = "Profil güncellenirken bir hata oluştu: " . $stmt_update->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt_update->close();
        
        // 3. Şifre Güncelleme Mantığı
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
                $stmt_pass->bind_param("si", $new_hashed_password, $user_id);
                $stmt_pass->execute();
                $stmt_pass->close();
                
                $_SESSION['message'] = "Profiliniz ve şifreniz başarıyla güncellendi.";
                $_SESSION['message_type'] = 'success';
            }
        }

        header("Location: /rentacar/public/profile");
        exit();
    }
}

