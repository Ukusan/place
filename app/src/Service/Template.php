<?php

declare(strict_types=1);

namespace Place\Service;

use Smarty;

class Template
{
    private const TEMPLATE_DIR = __DIR__ . DIRECTORY_SEPARATOR . '../../template';

    /**
     * @var Smarty
     */
    private $templateEngine;

    public function __construct(){
        $this->templateEngine = new Smarty();
        $this->templateEngine->setTemplateDir(self::TEMPLATE_DIR);
//        $smarty->setConfigDir('/some/config/dir');
//        $smarty->setCompileDir('/some/compile/dir');
//        $smarty->setCacheDir('/some/cache/dir');
    }

    public function render(string $templatePath, array $params = []): string
    {
        $this->templateEngine->assign($params);
        return $this->templateEngine->fetch($templatePath);
    }
}
