<?php

namespace App\Controllers;
use App\BaseController;

use DateTime;

class ReservationController extends BaseController {

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = "Rezervasyon yapmak için giriş yapmalısınız.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // --- YENİ FORM ALANLARI ALINIYOR ---
        $car_id = $_POST['car_id'];
        $user_id = $_SESSION['user_id'];
        $pickup_date_str = $_POST['pickup_date']; // örn: 12.11.2025
        $pickup_time_str = $_POST['pickup_time']; // örn: 10:00
        $return_date_str = $_POST['return_date']; // örn: 14.11.2025
        $return_time_str = $_POST['return_time']; // örn: 10:00
        $pickup_location_id = $_POST['pickup_location_id'];
        $dropoff_location_id = $_POST['dropoff_location_id'];
        $daily_rate = $_POST['daily_rate'];
        // --- GÜNCELLEME SONU ---

        try {
            // Tarihleri SQL formatına çevir (YYYY-MM-DD HH:MM:SS)
            $start_date_obj = DateTime::createFromFormat('d.m.Y', $pickup_date_str);
            $end_date_obj = DateTime::createFromFormat('d.m.Y', $return_date_str);
            
            if (!$start_date_obj || !$end_date_obj) {
                throw new \Exception("Geçersiz tarih formatı.");
            }

            $start_datetime_sql = $start_date_obj->format('Y-m-d') . ' ' . $pickup_time_str . ':00';
            $end_datetime_sql = $end_date_obj->format('Y-m-d') . ' ' . $return_time_str . ':00';

            // Tarihleri doğrula
            $start_date = new DateTime($start_datetime_sql);
            $end_date = new DateTime($end_datetime_sql);
            $now = new DateTime();

            if ($start_date < $now || $start_date >= $end_date) {
                throw new \Exception("Geçersiz tarih aralığı seçtiniz.");
            }
        
            $conn = \App\Database::getInstance()->getConnection();
            
            // Aracın müsait olup olmadığını kontrol et
            $stmt = $conn->prepare("SELECT reservation_id FROM reservations WHERE car_id = ? AND status IN ('Onaylandı', 'Beklemede') AND NOT (end_date <= ? OR start_date >= ?)");
            $stmt->bind_param("iss", $car_id, $start_datetime_sql, $end_datetime_sql); // SQL formatlı tarihleri kullan
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();
                throw new \Exception("Maalesef bu araç seçtiğiniz tarihler arasında rezerve edilmiş.");
            }
            $stmt->close();

            // Fiyatı hesapla
            $interval = $start_date->diff($end_date);
            $days = $interval->days;
            if ($interval->h > 0 || $interval->i > 0 || $interval->s > 0) {
                $days++;
            }
            $total_price = $days * $daily_rate;

            // Rezervasyonu veritabanına ekle
            $stmt = $conn->prepare("INSERT INTO reservations (user_id, car_id, start_date, end_date, pickup_location_id, dropoff_location_id, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Onaylandı')");
            $stmt->bind_param("iissiid", $user_id, $car_id, $start_datetime_sql, $end_datetime_sql, $pickup_location_id, $dropoff_location_id, $total_price);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Rezervasyonunuz başarıyla oluşturuldu! Toplam Tutar: " . $total_price . " TL";
                $_SESSION['message_type'] = 'success';
                header("Location: /rentacar/public/home");
            } else {
                throw new \Exception("Rezervasyon sırasında bir veritabanı hatası oluştu: " . $stmt->error);
            }
            $stmt->close();
            exit();

        } catch (\Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']); // Hata durumunda formu doldurduğu sayfaya geri dön
            exit();
        }
    }

public function cancel() {
        // 1. Kullanıcı giriş yapmış mı?
        if (!isset($_SESSION['user_id'])) {
            header("Location: /rentacar/public/home");
            exit();
        }

        // 2. URL'den gelen rezervasyon ID'sini al
        $reservation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $user_id = $_SESSION['user_id'];

        if (!$reservation_id) {
            header("Location: /rentacar/public/my_reservations");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';

        // 3. GÜVENLİK KONTROLÜ: Bu rezervasyon gerçekten bu kullanıcıya mı ait?
        $stmt = $conn->prepare("SELECT user_id FROM reservations WHERE reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Rezervasyon bulunamadı.
            $stmt->close();
            $conn->close();
            header("Location: /rentacar/public/my_reservations");
            exit();
        }

        $reservation = $result->fetch_assoc();
        
        // Eğer rezervasyonun sahibi, giriş yapan kullanıcı değilse, işlemi reddet.
        if ($reservation['user_id'] != $user_id) {
            $stmt->close();
            $conn->close();
            $_SESSION['message'] = "Bu işlemi yapma yetkiniz yok.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/my_reservations");
            exit();
        }
        $stmt->close();

        // 4. Rezervasyon durumunu 'İptal Edildi' olarak güncelle
        $stmt = $conn->prepare("UPDATE reservations SET status = 'İptal Edildi' WHERE reservation_id = ?");
        $stmt->bind_param("i", $reservation_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Rezervasyonunuz başarıyla iptal edildi.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "İptal işlemi sırasında bir hata oluştu.";
            $_SESSION['message_type'] = 'danger';
        }

        $stmt->close();
        $conn->close();
        header("Location: /rentacar/public/my_reservations");
        exit();
    }




















}