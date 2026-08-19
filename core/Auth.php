<?php

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (APP_ENV === 'production') {
                session_set_cookie_params([
                    'secure'   => true,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            } else {
                session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
            }
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id']      = $user['id'];
        $_SESSION['user_name']    = $user['name'];
        $_SESSION['user_email']   = $user['email'];
        $_SESSION['user_role']    = $user['role'];
        $_SESSION['company_name'] = $user['company_name'];
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        self::start();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::start();
        if (!self::check()) {
            return null;
        }
        return [
            'id'           => $_SESSION['user_id'],
            'name'         => $_SESSION['user_name'],
            'email'        => $_SESSION['user_email'],
            'role'         => $_SESSION['user_role'],
            'company_name' => $_SESSION['company_name'],
        ];
    }

    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Silakan login terlebih dahulu.');
            redirect('login.php');
        }
    }

    public static function requireRole(array $roles): void
    {
        self::requireLogin();
        if (!in_array(self::role(), $roles, true)) {
            flash('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            redirect('dashboard.php');
        }
    }
}
