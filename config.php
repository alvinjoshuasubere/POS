<?php
declare(strict_types=1);
// Copy to config.local.php and set values for your host. Never commit credentials.
return [
 'db_host' => getenv('DB_HOST') ?: '127.0.0.1',
 'db_port' => getenv('DB_PORT') ?: '3306',
 'db_name' => getenv('DB_NAME') ?: 'pos',
 'db_user' => getenv('DB_USER') ?: 'pos_user',
 'db_pass' => getenv('DB_PASS') ?: '',
];
