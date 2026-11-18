<?php

$localConfig = __DIR__ . '/config.local.php';

if (is_file($localConfig)) {
    require_once $localConfig;
} else {
    foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $name) {
        $value = getenv($name);
        if ($value === false || $value === '') {
            throw new RuntimeException("Missing database configuration: {$name}");
        }

        define($name, $value);
    }
}
