<?php
declare(strict_types=1);

namespace App\Modules\Settings\Controllers;

use App\Framework\Controller;
use App\Framework\Session;

class SettingsController extends Controller
{
    public function index(array $params = []): void
    {
        $cfg = Session::get('api_config', []);
        $this->render('settings', [
            'pageTitle' => 'Configuración',
            'cfg' => $cfg,
        ]);
    }
}
