<?php

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('dashboard.php');
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            flash('error', 'Email dan password wajib diisi.');
            redirect('login.php');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Email atau password salah.');
            redirect('login.php');
        }

        Auth::login($user);
        flash('success', 'Selamat datang kembali, ' . $user['name'] . '.');
        redirect('dashboard.php');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('dashboard.php');
        }
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void
    {
        $name        = trim($_POST['name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $password    = $_POST['password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';
        $role        = $_POST['role'] ?? '';
        $companyName = trim($_POST['company_name'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $address     = trim($_POST['address'] ?? '');

        $allowedRoles = ['producer', 'distributor', 'retailer'];

        $errors = [];
        if ($name === '') $errors[] = 'Nama wajib diisi.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
        if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
        if ($password !== $confirm) $errors[] = 'Konfirmasi password tidak cocok.';
        if (!in_array($role, $allowedRoles, true)) $errors[] = 'Role tidak valid.';
        if ($email !== '' && $this->userModel->emailExists($email)) $errors[] = 'Email sudah terdaftar.';

        if (!empty($errors)) {
            flash('error', implode(' ', $errors));
            redirect('register.php');
        }

        $userId = $this->userModel->create([
            'name'         => $name,
            'email'        => $email,
            'password'     => $password,
            'role'         => $role,
            'company_name' => $companyName,
            'phone'        => $phone,
            'address'      => $address,
        ]);

        $user = $this->userModel->findById($userId);
        Auth::login($user);
        flash('success', 'Registrasi berhasil. Selamat datang di LokalTrust!');
        redirect('dashboard.php');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('index.php');
    }
}
