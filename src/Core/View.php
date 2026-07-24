<?php

declare(strict_types=1);

namespace App\Core;

use Smarty\Smarty;

final class View
{
    private Smarty $smarty;

    public function __construct(array $config)
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($config['paths']['templates']);
        $this->smarty->setCompileDir($config['paths']['smarty']['compile']);
        $this->smarty->setCacheDir($config['paths']['smarty']['cache']);
        $this->smarty->assign('app', [
            'name' => $config['name'],
            'url' => $config['url'],
        ]);
        $this->smarty->assign('viteTags', Vite::tags('resources/js/app.js', $config['paths']['base']));
    }

    public function render(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        return $this->smarty->fetch($template);
    }
}
