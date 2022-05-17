<?php

declare(strict_types=1);

//If the host forced the entire source code to be public the redirect rules from my .htaccess files will
// eventually end up here instead. So we just require the correct index files.
echo '<pre>', basename(__DIR__) . DIRECTORY_SEPARATOR . basename(__FILE__), '</pre>';
require_once __DIR__ . '/public/index.php';
//Note that the working directory will still be /public due to the rewrite base rule in .htaccess