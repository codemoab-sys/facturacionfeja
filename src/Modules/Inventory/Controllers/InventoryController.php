<?php
declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Framework\Controller;

class InventoryController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('inventario/index', ['pageTitle' => 'Inventario']);
    }
}
