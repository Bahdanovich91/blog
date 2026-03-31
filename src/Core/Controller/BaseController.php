<?php

declare(strict_types=1);

namespace App\Core\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Smarty\Exception;
use Smarty\Smarty;

abstract class BaseController
{
    protected Smarty $smarty;

    public function __construct()
    {
        $this->smarty = new Smarty();

        $this->smarty->setTemplateDir(__DIR__ . '/../../../templates/');
        $this->smarty->setCompileDir(__DIR__ . '/../../../var/compile/');
        $this->smarty->setCacheDir(__DIR__ . '/../../../var/cache/');
    }

    /**
     * @throws Exception
     */
    protected function render(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        return $this->smarty->fetch($template);
    }
}
