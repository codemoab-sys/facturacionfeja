<?php
declare(strict_types=1);

namespace App\Modules\Clients\Controllers;

use App\Framework\Controller;

class ClientController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('clientes/index', ['pageTitle' => 'Clientes']);
    }
}
