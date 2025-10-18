<?php

namespace App;

class BaseController {
    
    /**
     * View dosyasını değişkenlerle birlikte yükler.
     * Tüm diğer controller'lar bu metodu miras alacak.
     */
    protected function loadView($viewName, $data = []) {
        extract($data);
        
        // Bu dosya app/ dizininde olduğu için, views dizinine ulaşmak için yolu düzeltiyoruz.
        require_once __DIR__ . '/../views/' . $viewName . '.php';
    }
}