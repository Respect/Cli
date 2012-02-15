<?php

chdir(__DIR__.'/../library');

spl_autoload_register(function($className)
    {
        $fileParts = explode('\\', ltrim($className, '\\'));

        if (false !== strpos(end($fileParts), '_'))
            array_splice($fileParts, -1, 1, explode('_', current($fileParts)));

        $fileName = implode(DIRECTORY_SEPARATOR, $fileParts) . '.php';
        
        if (stream_resolve_include_path($fileName))
            require $fileName;
    });

new Respect\Cli\Runner;
