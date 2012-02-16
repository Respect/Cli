<?php

chdir(__DIR__ . '/../library');

if (!@include __DIR__ . '/../vendor/.composer/autoload.php') {

    /* Include path */
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(__DIR__ . '/../library'),
        trim(`pear config-get php_dir`),
        get_include_path(),
    )));

    /* Autoloader */
    spl_autoload_register(
        function($className) {
            $fileParts = explode('\\', ltrim($className, '\\'));

            if (false !== strpos(end($fileParts), '_')) {
                array_splice($fileParts, -1, 1, explode('_', current($fileParts)));
            }

            $file = implode(DIRECTORY_SEPARATOR, $fileParts) . '.php';

            foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
                if (file_exists($path = $path . DIRECTORY_SEPARATOR . $file)) {
                    return require $path;
                }
            }
        }
    );    
}

new Respect\Cli\Runner;