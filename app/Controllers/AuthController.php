<?php

namespace App\Controllers;
use App\BaseController;
class AuthController extends BaseController {

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

        require __DIR__ . '/../../config/db.php';

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Bu e-posta adresi zaten başka bir kullanıcı tarafından kullanılıyor.";
        }
        $stmt->close();

        if (!empty($errors)) {
            $conn->close();
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
        $conn->close();
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
        
        $stmt = $conn->prepare("SELECT user_id, first_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $first_name, $hashed_password_from_db, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password_from_db)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['role'] = $role;
                if ($role === 'Admin') {
                        header("Location: /rentacar/public/admin/dashboard");
                    } 
                else {
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