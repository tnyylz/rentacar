<?php
namespace App\Controllers;
use App\BaseController;
use League\OAuth2\Client\Provider\Google;




class AuthController extends BaseController {
    private $googleProvider;
     public function __construct() {
        // Google Provider'ı (Sağlayıcı) Client ID ve Secret ile başlat
        // BU BİLGİLERİ Adım 1'de aldıklarınla değiştir.
        $this->googleProvider = new Google([
            'clientId'     => '495685289285-o3hlaffesmo5r3osqoe5pgrarbqrvf12.apps.googleusercontent.com', // Adım 1'de aldığın ID
            'clientSecret' => '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', // Adım 1'de aldığın Secret
            'redirectUri'  => 'http://localhost/rentacar/public/auth/google/callback',
        ]);
    }

public function redirectToGoogle() {
        // Gerekli izinleri (scope) istiyoruz: e-posta, profil bilgileri
        $authUrl = $this->googleProvider->getAuthorizationUrl([
            'scope' => ['email', 'profile']
        ]);
        
        // Kullanıcının oturum durumunu (state) kaydet
        $_SESSION['oauth2state'] = $this->googleProvider->getState();
        
        // Kullanıcıyı Google'ın giriş sayfasına yönlendir
        header('Location: ' . $authUrl);
        exit();
    }

    // --- YENİ METOT 2: Google'dan Gelen Cevabı İşleme ---
    public function handleGoogleCallback() {
        // Güvenlik kontrolü: state'ler eşleşiyor mu?
        if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            $_SESSION['message'] = "Geçersiz oturum durumu.";
            $_SESSION['message_type'] = 'danger';
            header('Location: /rentacar/public/home');
            exit();
        }

        try {
            // Gelen 'code'u kullanarak Google'dan 'access token' (erişim anahtarı) al
            $token = $this->googleProvider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            // Bu 'token' ile kullanıcı bilgilerini Google'dan çek
            $googleUser = $this->googleProvider->getResourceOwner($token);

            $conn = \App\Database::getInstance()->getConnection();

            // 1. Bu kullanıcı veritabanımızda zaten var mı? (E-postaya göre kontrol et)
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $email = $googleUser->getEmail();
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                // KULLANICI VARSA: Giriş yap ve Google ID'sini güncelle (eğer boşsa)
                $user_id = $user['user_id'];
                if (empty($user['google_id'])) {
                    $stmt_update = $conn->prepare("UPDATE users SET google_id = ? WHERE user_id = ?");
                    $google_id = $googleUser->getId();
                    $stmt_update->bind_param("si", $google_id, $user_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                }
            } else {
                // KULLANICI YOKSA: Yeni bir kullanıcı oluştur
                $stmt_insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, google_id) VALUES (?, ?, ?, ?)");
                $google_id = $googleUser->getId();
                $first_name = $googleUser->getFirstName();
                $last_name = $googleUser->getLastName();
                $stmt_insert->bind_param("ssss", $first_name, $last_name, $email, $google_id);

                $stmt_insert->execute();
                $user_id = $stmt_insert->insert_id;
                $stmt_insert->close();
                
                // Yeni oluşturulan kullanıcıyı tekrar çek
                $user = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
                $user->bind_param("i", $user_id);
                $user->execute();
                $user = $user->get_result()->fetch_assoc();
            }

            // KULLANICIYI GİRİŞ YAPTIR (SESSION'LARI AYARLA)
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];

            header('Location: /rentacar/public/home');
            exit();

        } catch (\Exception $e) {
            // Hata oluşursa
            $_SESSION['message'] = "Google ile giriş yaparken bir hata oluştu: " . $e->getMessage();
            $_SESSION['message_type'] = 'danger';
            header('Location: /rentacar/public/home');
            exit();
        }
    }









    public function showRegisterForm() {
        $this->loadView('register');
    }

      public function register() {
        $errors = [];
        $old_input = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'email' => $_POST['email'] ?? ''
        ];

        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $password_confirm = trim($_POST['password_confirm']);

        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $errors[] = "Lütfen tüm zorunlu alanları doldurun.";
        }
        if ($password !== $password_confirm) {
            $errors[] = "Girdiğiniz şifreler uyuşmuyor.";
        }

        if (!empty($errors)) {
            $this->redirectBackToRegisterForm($errors, $old_input);
        }

        $conn = \App\Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Bu e-posta adresi zaten başka bir kullanıcı tarafından kullanılıyor.";
        }
        $stmt->close();

        if (!empty($errors)) {
            $this->redirectBackToRegisterForm($errors, $old_input);
        }
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'Customer')");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Başarıyla kayıt oldunuz! Şimdi giriş yapabilirsiniz.";
            $_SESSION['message_type'] = 'success';
            header("Location: /rentacar/public/home");
        } else {
            $errors[] = "Kayıt sırasında beklenmedik bir veritabanı hatası oluştu.";
            $this->redirectBackToRegisterForm($errors, $old_input);
        }
        $stmt->close();
        exit();
    }

    public function login() {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        if (empty($email) || empty($password)) {
            $_SESSION['message'] = "E-posta ve şifre alanları zorunludur.";
            $_SESSION['message_type'] = 'danger';
            header("Location: /rentacar/public/home");
            exit();
        }

        require_once __DIR__ . '/../../config/db.php';
        
        // --- SORGULAMAYA last_name ve profile_image_url EKLENDİ ---
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, password, role, profile_image_url FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // --- DEĞİŞKENLERE last_name ve profile_image_url EKLENDİ ---
            $stmt->bind_result($user_id, $first_name, $last_name, $hashed_password_from_db, $role, $profile_image_url);
            $stmt->fetch();

            if (password_verify($password, $hashed_password_from_db)) {
                // --- SESSION'A last_name ve profile_image_url EKLENDİ ---
                $_SESSION['user_id'] = $user_id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name; 
                $_SESSION['profile_image_url'] = $profile_image_url;
                $_SESSION['role'] = $role;
                
                if ($role === 'Admin') {
                    header("Location: /rentacar/public/admin/dashboard");
                } else {
                    header("Location: /rentacar/public/home");
                }
                
                $stmt->close();
                $conn->close();
                exit();
            }
        }
        
        $_SESSION['message'] = "Hatalı e-posta adresi veya şifre girdiniz.";
        $_SESSION['message_type'] = 'danger';
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
        header("Location: /rentacar/public/home");
        exit();
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /rentacar/public/home");
        exit();
    }
    
    private function redirectBackToRegisterForm($errors, $old_input) {
        $error_html = '<strong>Lütfen aşağıdaki hataları düzeltin:</strong><ul>';
        foreach ($errors as $error) {
            $error_html .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $error_html .= '</ul>';
        $_SESSION['message'] = $error_html;
        $_SESSION['message_type'] = 'danger';
        $_SESSION['old_input'] = $old_input;
        header("Location: /rentacar/public/register");
        exit();
    }
    
   
}