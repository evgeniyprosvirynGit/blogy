<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class HomeController extends Controller
{
    public function index(): string
    {
        return $this->render('home/index.tpl', [
            'pageTitle' => 'Simple PHP Blog',
            'categories' => [],
        ]);
    }
}
