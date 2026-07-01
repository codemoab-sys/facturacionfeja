<?php
declare(strict_types=1);

namespace App\Validacion;

class SolicitudConfiguracion
{
    public static function rules(): array
    {
        return [
            'base_url'   => 'required|url|max:255',
            'api_key'    => 'required|min:8|max:255',
            'api_secret' => 'max:255',
        ];
    }

    public static function validate(array $data): ?array
    {
        $validator = new Validador();
        return $validator->validate($data, self::rules());
    }
}
