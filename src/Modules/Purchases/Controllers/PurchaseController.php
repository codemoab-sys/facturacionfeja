<?php
declare(strict_types=1);

namespace App\Modules\Purchases\Controllers;

use App\Framework\Controller;

class PurchaseController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('compras/index', ['pageTitle' => 'Compras']);
    }
}
