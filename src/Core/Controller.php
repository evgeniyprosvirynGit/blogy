<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected View $view)
    {
    }

    protected function render(string $template, array $data = []): string
    {
        return $this->view->render($template, $data);
    }
}
