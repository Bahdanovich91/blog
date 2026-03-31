<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

$root = dirname(__DIR__);
$scssFile = $root . '/scss/style.scss';
$cssFile = $root . '/public/css/style.css';

if (!file_exists($scssFile)) {
    fwrite(STDERR, "SCSS source not found: {$scssFile}\n");
    exit(1);
}

$compiler = new Compiler();
$compiler->setImportPaths([$root . '/scss']);
$compiler->setOutputStyle(OutputStyle::COMPRESSED);

try {
    $result = $compiler->compileString(file_get_contents($scssFile));
    file_put_contents($cssFile, $result->getCss());
    echo "CSS compiled successfully to: {$cssFile}\n";
} catch (\Exception $e) {
    fwrite(STDERR, "SCSS compilation failed: " . $e->getMessage() . "\n");
    exit(1);
}
