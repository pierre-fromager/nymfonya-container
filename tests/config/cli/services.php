<?php

use Nymfonya\Component\Config;
use Nymfonya\Component\Container\Tests\Request;

return [
    Config::class => [Config::ENV_CLI, __DIR__ . '/../'],
    Request::class => []
];
