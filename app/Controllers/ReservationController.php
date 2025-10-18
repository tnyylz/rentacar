<?php

namespace App\Controllers;
use App\BaseController;

use DateTime;

class ReservationController extends BaseController {

    public function create() {
        // 1. Kullanıcı giriş yapmış mı?
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = "Rezervasyon yapmak için giriş yapmalısınız.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']); // Bir önceki sayfaya geri yolla
            exit();
        }

        // 2. Form verilerini al
        $car_id = $_POST['car_id'];
        $user_id = $_SESSION['user_id'];
        $start_date_str = $_POST['start_date'];
        $end_date_str = $_POST['end_date'];
        $pickup_location_id = $_POST['pickup_location_id'];
        $dropoff_location_id = $_POST['dropoff_location_id'];
        $daily_rate = $_POST['daily_rate'];

        // 3. Tarihleri doğrula
        $start_date = new DateTime($start_date_str);
        $end_date = new DateTime($end_date_str);
        $now = new DateTime();

        if ($start_date < $now || $start_date >= $end_date) {
            $_SESSION['message'] = "Geçersiz tarih aralığı seçtiniz.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // 4. Aracın müsait olup olmadığını kontrol et (EN ÖNEMLİ KISIM)
        require __DIR__ . '/../../config/db.php';
        $stmt = $conn->prepare("SELECT reservation_id FROM reservations 
                                WHERE car_id = ? 
                                AND status IN ('Onaylandı', 'Beklemede')
                                AND NOT (end_date <= ? OR start_date >= ?)");
        $stmt->bind_param("iss", $car_id, $start_date_str, $end_date_str);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Eğer bu sorgu bir sonuç döndürürse, o tarihler arasında çakışan bir rezervasyon var demektir.
            $stmt->close();
            $conn->close();
            $_SESSION['message'] = "Maalesef bu araç seçtiğiniz tarihler arasında rezerve edilmiş.";
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        $stmt->close();

        // 5. Fiyatı hesapla
        $interval = $start_date->diff($end_date);
        $days = $interval->days;
        if ($interval->h > 0 || $interval->i > 0 || $interval->s > 0) {
            $days++; // Gün farkına ek olarak saat/dakika farkı varsa, bir gün daha ekle
        }
        $total_price = $days * $daily_rate;

        // 6. Rezervasyonu veritabanına ekle
        $stmt = $conn->prepare("INSERT INTO reservations (user_id, car_id, start_date, end_date, pickup_location_id, dropoff_location_id, total_price, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'Onaylandı')");
        $stmt->bind_param("iissiid", $user_id, $car_id, $start_date_str, $end_date_str, $pickup_location_id, $dropoff_location_id, $total_price);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Rezervasyonunuz başarıyla oluşturuldu! Toplam Tutar: " . $total_price . " TL";
            $_SESSION['message_type'] = 'success';
            header("Location: /rentacar/public/home"); // Başarı durumunda ana sayfaya yolla
        } else {
            $_SESSION['message'] = "Rezervasyon sırasında bir hata oluştu: " . $stmt->error;
            $_SESSION['message_type'] = 'danger';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

        $stmt->close();
        $conn->close();
        exit();
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