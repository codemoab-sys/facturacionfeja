<?php
declare(strict_types=1);

namespace App\Servicios;

use App\Nucleo\Sesion;
use App\DTOs\ResultadoDTO;
use App\DTOs\UsuarioDTO;
use App\Repositorios\RepositorioUsuario;
use App\Repositorios\RepositorioConfiguracionUsuario;

class ServicioAutenticacion
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    private RepositorioUsuario $userRepository;
    private RepositorioConfiguracionUsuario $userConfigRepository;
    private ServicioConfiguracion $configService;

    public function __construct(
        ?RepositorioUsuario $userRepository = null,
        ?RepositorioConfiguracionUsuario $userConfigRepository = null,
        ?ServicioConfiguracion $configService = null
    ) {
        $this->userRepository = $userRepository ?? new RepositorioUsuario();
        $this->userConfigRepository = $userConfigRepository ?? new RepositorioConfiguracionUsuario();
        $this->configService = $configService ?? new ServicioConfiguracion();
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
            $this->log('LOGIN_LOCKED', $usuario, 'Cuenta bloqueada por intentos fallidos');
            return ResultadoDTO::error(
                'Demasiados intentos. Intente de nuevo en ' . self::LOCKOUT_MINUTES . ' minutos.',
                null,
                429
            );
        }

        try {
            $found = $this->userRepository->findByUsername($usuario);
        } catch (\Exception $e) {
            return ResultadoDTO::error('Error de conexión con la base de datos.', null, 500);
        }

        if ($found && password_verify($password, $found['password'])) {
            $this->clearAttempts($usuario);
            Sesion::regenerate();

            Sesion::set('user_id', $found['id']);
            Sesion::set('user_nombre', $found['nombre']);
            Sesion::set('user_usuario', $found['usuario']);

            try {
                $cfg = $this->userConfigRepository->findByUserId((int)$found['id']);
                if ($cfg) {
                    $this->configService->setConfig([
                        'base_url'   => $cfg['base_url'],
                        'api_key'    => $cfg['api_key'],
                        'api_secret' => $cfg['api_secret'],
                    ]);
                }
            } catch (\Exception $e) {}

            $this->log('LOGIN_OK', $usuario, 'Login exitoso desde ' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
            return ResultadoDTO::success('Login exitoso');
        }

        $this->registerAttempt($usuario);
        $this->log('LOGIN_FAIL', $usuario, 'Contraseña incorrecta');
        return ResultadoDTO::error('Usuario o contraseña incorrectos.', null, 401);
    }

    public function logout(): void
    {
        $userId = Sesion::get('user_id');
        $this->log('LOGOUT', 'user#' . $userId, 'Cierre de sesión');
        Sesion::destroy();
    }

    public function isAuthenticated(): bool
    {
        return Sesion::has('user_id');
    }

    public function getUser(): ?UsuarioDTO
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        return new UsuarioDTO(
            (int)Sesion::get('user_id'),
            Sesion::get('user_usuario', ''),
            Sesion::get('user_nombre', '')
        );
    }

    public function getUserId(): ?int
    {
        return Sesion::has('user_id') ? (int)Sesion::get('user_id') : null;
    }

    private function isLockedOut(string $usuario): bool
    {
        $key = 'login_attempts_' . $usuario;
        $attempts = Sesion::get($key, []);
        if (empty($attempts)) {
            return false;
        }

        $count = 0;
        $since = time() - (self::LOCKOUT_MINUTES * 60);
        foreach ($attempts as $t) {
            if ($t > $since) {
                $count++;
            }
        }

        if ($count >= self::MAX_ATTEMPTS) {
            $last = max($attempts);
            $wait = ($last + self::LOCKOUT_MINUTES * 60) - time();
            if ($wait > 0) {
                return true;
            }
            $this->clearAttempts($usuario);
        }

        return false;
    }

    private function registerAttempt(string $usuario): void
    {
        $key = 'login_attempts_' . $usuario;
        $attempts = Sesion::get($key, []);
        $attempts[] = time();
        Sesion::set($key, $attempts);
    }

    private function clearAttempts(string $usuario): void
    {
        Sesion::remove('login_attempts_' . $usuario);
    }

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
