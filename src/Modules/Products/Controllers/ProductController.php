<?php
declare(strict_types=1);

namespace App\Modules\Products\Controllers;

use App\Framework\Controller;

class ProductController extends Controller
{
    public function index(array $params = []): void
    {
        $this->render('productos/index', ['pageTitle' => 'Productos']);
    }
}
