<?php

use Nymfonya\Component\Config;
use Tests\Request;

return [
    Config::class => [Config::ENV_CLI, __DIR__ . '/../'],
    Request::class => []
];
