<?php
declare(strict_types=1);

namespace App\Framework\Middleware;

use App\Framework\Session;

class Auth
{
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        $id = Session::get('user_id');
        return $id !== null ? (int) $id : null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id'       => Session::get('user_id'),
            'usuario'  => Session::get('user_usuario'),
            'nombre'   => Session::get('user_nombre'),
            'email'    => Session::get('user_email'),
            'tipo_doc' => Session::get('user_tipo_doc'),
            'num_doc'  => Session::get('user_num_doc'),
        ];
    }

    public static function require(): void
    {
        if (!self::check()) {
            $isApi = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0;
            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No autenticado']);
                exit;
            }
            header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '') . '/login');
            exit;
        }
    }
}
