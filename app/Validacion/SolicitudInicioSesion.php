<?php
declare(strict_types=1);

namespace App\Validacion;

class SolicitudInicioSesion
{
    public static function rules(): array
    {
        return [
            'usuario'  => 'required|min:3|max:50',
            'password' => 'required|min:4',
        ];
    }

    public static function validate(array $data): ?array
    {
        $validator = new Validador();
        return $validator->validate($data, self::rules());
    }
}
