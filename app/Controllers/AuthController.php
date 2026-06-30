<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;
    private const BCRYPT_COST = 12;

    public function showLogin($params = [])
    {
        if (Session::has('user_id')) {
            $this->redirect('/');
        }
        $this->view('layouts/login');
    }

    public function login($params = [])
    {
        $request = \App\Core\App::getInstance()->getRequest();
        $usuario = trim($request->post('usuario', ''));
        $password = $request->post('password', '');

        $errors = [];
        if ($usuario === '') {
            $errors[] = 'El usuario es obligatorio.';
        }
        if ($password === '') {
            $errors[] = 'La contraseña es obligatoria.';
        }
        if ($errors) {
            $this->log('LOGIN_VALIDATION', $usuario, implode(', ', $errors));
            return $this->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors'  => $errors,
            ], 422);
        }

        if ($this->isLockedOut($usuario)) {
            $this->log('LOGIN_LOCKED', $usuario, 'Cuenta bloqueada por intentos fallidos');
            return $this->json([
                'success' => false,
                'message' => 'Demasiados intentos. Intente de nuevo en ' . self::LOCKOUT_MINUTES . ' minutos.',
            ], 429);
        }

        $user = new User();
        $found = $user->findOneBy('usuario', $usuario);

        if ($found && password_verify($password, $found['password'])) {
            $this->clearAttempts($usuario);
            Session::regenerate();

            Session::set('user_id', $found['id']);
            Session::set('user_nombre', $found['nombre']);
            Session::set('user_usuario', $found['usuario']);

            $configModel = new \App\Models\UserConfig();
            $cfg = $configModel->findByUserId($found['id']);
            if ($cfg) {
                Session::set('api_config', [
                    'base_url'   => $cfg['base_url'],
                    'api_key'    => $cfg['api_key'],
                    'api_secret' => $cfg['api_secret'],
                ]);
            }

            $this->log('LOGIN_OK', $usuario, 'Login exitoso desde ' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
            $this->json(['success' => true, 'message' => 'Login exitoso']);
        } else {
            $this->registerAttempt($usuario);
            $this->log('LOGIN_FAIL', $usuario, 'Contraseña incorrecta');
            $this->json([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos.',
            ], 401);
        }
    }

    public function logout($params = [])
    {
        $userId = Session::get('user_id');
        $this->log('LOGOUT', 'user#' . $userId, 'Cierre de sesión');
        Session::destroy();
        $this->redirect('/login');
    }

    // ── Rate limiting ──

    private function isLockedOut(string $usuario): bool
    {
        $key = 'login_attempts_' . $usuario;
        $attempts = Session::get($key, []);
        if (empty($attempts)) return false;

        $count = 0;
        $since = time() - (self::LOCKOUT_MINUTES * 60);
        foreach ($attempts as $t) {
            if ($t > $since) $count++;
        }

        if ($count >= self::MAX_ATTEMPTS) {
            $last = max($attempts);
            $wait = ($last + self::LOCKOUT_MINUTES * 60) - time();
            if ($wait > 0) return true;
            $this->clearAttempts($usuario);
        }

        return false;
    }

    private function registerAttempt(string $usuario): void
    {
        $key = 'login_attempts_' . $usuario;
        $attempts = Session::get($key, []);
        $attempts[] = time();
        Session::set($key, $attempts);
    }

    private function clearAttempts(string $usuario): void
    {
        Session::remove('login_attempts_' . $usuario);
    }

    // ── Logging ──

    private function log(string $event, string $subject, string $detail): void
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $line = sprintf(
            "[%s] %s | %s | %s | %s\n",
            date('Y-m-d H:i:s'),
            $event,
            $subject,
            $detail,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        );
        @file_put_contents($logDir . '/auth.log', $line, FILE_APPEND | LOCK_EX);
    }
}
