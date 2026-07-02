<?php
declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Framework\Session;
use App\DTOs\ResultadoDTO;
use App\Modules\Auth\Repositories\UserRepository;
use App\Modules\Settings\Services\ConfigService;

class AuthService
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    private UserRepository $userRepository;
    private ConfigService $configService;

    public function __construct(
        ?UserRepository $userRepository = null,
        ?ConfigService $configService = null
    ) {
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->configService = $configService ?? new ConfigService();
    }

    public function login(string $usuario, string $password): ResultadoDTO
    {
        if ($usuario === '') {
            return ResultadoDTO::error('El usuario es obligatorio.', ['usuario' => 'Requerido']);
        }
        if ($password === '') {
            return ResultadoDTO::error('La contraseña es obligatoria.', ['password' => 'Requerido']);
        }

        if ($this->isLockedOut($usuario)) {
            $this->log('LOGIN_LOCKED', $usuario, 'Cuenta bloqueada');
            return ResultadoDTO::error(
                'Demasiados intentos. Intente de nuevo en ' . self::LOCKOUT_MINUTES . ' minutos.',
                null,
                429
            );
        }

        try {
            $found = $this->userRepository->findByUsername($usuario);
        } catch (\Exception $e) {
            $this->log('LOGIN_DB_ERROR', $usuario, $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return ResultadoDTO::error('Error de conexión con la base de datos.', null, 500);
        }

        if ($found && password_verify($password, $found['password'])) {
            $this->clearAttempts($usuario);
            Session::regenerate();

            Session::set('user_id', $found['id']);
            Session::set('user_nombre', $found['nombre']);
            Session::set('user_usuario', $found['usuario']);

            try {
                $cfg = $found['api_config'] ?? $this->userRepository->findConfigByUserId((int)$found['id']);
                if ($cfg) {
                    Session::set('api_config', $cfg);
                }
            } catch (\Exception $e) {}

            $this->log('LOGIN_OK', $usuario, 'Login exitoso');
            return ResultadoDTO::success('Login exitoso');
        }

        $this->registerAttempt($usuario);
        $this->log('LOGIN_FAIL', $usuario, 'Contraseña incorrecta');
        return ResultadoDTO::error('Usuario o contraseña incorrectos.', null, 401);
    }

    public function logout(): void
    {
        Session::destroy();
    }

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

    private function log(string $event, string $subject, string $detail): void
    {
        $logDir = __DIR__ . '/../../../../storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        $line = sprintf("[%s] %s | %s | %s | %s\n", date('Y-m-d H:i:s'), $event, $subject, $detail, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        @file_put_contents($logDir . '/auth.log', $line, FILE_APPEND | LOCK_EX);
    }
}
