<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| T&T COMPUTER
| Front Controller
|--------------------------------------------------------------------------
|
| Mọi request của website đều đi qua file này.
|
*/

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/routes/web.php';