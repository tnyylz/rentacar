<?php

namespace App\Controllers;
use App\BaseController;

class CarController extends BaseController   {

    /**
     * URL'den gelen ID'ye göre bir aracın detaylarını gösterir.
     */
    public function show() {
        // 1. URL'den araç ID'sini güvenli bir şekilde al (?id=5 gibi)
        $car_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($car_id === false || $car_id === null) {
            // Eğer geçerli bir ID gelmediyse veya ID bir sayı değilse, 404 sayfasına yönlendir.
            $this->showNotFound();
        }

        // 2. Veritabanından o ID'ye sahip aracı çek
        require_once __DIR__ . '/../../config/db.php';
        
        // SQL Injection'a karşı korunmak için Prepared Statements kullan
        $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Eğer veritabanında o ID'ye sahip araç yoksa, yine 404 göster.
            $stmt->close();
            $conn->close();
            $this->showNotFound();
        }

        $car = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        // 3. Araç bilgilerini 'car-detail' view'ine gönder
        $this->loadView('car_detail', ['car' => $car]);
    }

    /**
     * View dosyasını yüklemek için yardımcı bir fonksiyon
     */
   

    /**
     * 404 sayfasını göstermek için yardımcı fonksiyon
     */
    private function showNotFound() {
        http_response_code(404);
        require_once __DIR__ . '/../../views/404.php';
        exit();
    }
}