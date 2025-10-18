<?php

namespace App\Controllers;

class ApiController {

    /**
     * Konumu belli olan, 'Müsait' durumdaki tüm araçları JSON formatında döndürür.
     */
    public function getAvailableCars() {
        require_once __DIR__ . '/../../config/db.php';

        $sql = "SELECT car_id, brand, model, daily_rate, latitude, longitude 
                FROM cars 
                WHERE status = 'Müsait' 
                AND latitude IS NOT NULL 
                AND longitude IS NOT NULL";

        $result = $conn->query($sql);
        $cars = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();

        // Tarayıcıya içeriğin JSON olduğunu söylüyoruz
        header('Content-Type: application/json');
        
        // PHP dizisini JSON metnine çevirip ekrana basıyoruz
        echo json_encode($cars);
        exit(); // Script'i sonlandır
    }
}