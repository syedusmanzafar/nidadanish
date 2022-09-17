<?php

// If you need to work on a live store, you can make the settings below apply to a certain IP only, so that the customers won't be affected. Specify your IP address instead of 127.0.0.1.
if ($_SERVER['REMOTE_ADDR'] == '178.121.47.86') {

    // Turn on the Debug mode for the admin panel and the storefront
    // define('DEBUG_MODE', true);

    // Use the Development mode to display errors
    define('DEVELOPMENT', true);

    // Display SMARTY and PHP errors on the screen.
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
    ini_set('display_startup_errors', true);

    // Disable PHP block caching
    $config['tweaks']['disable_block_cache'] = true;

}

// You can change configuration without changing config.local.php.

/*
$config['db_host'] = '%DB_HOST%';
$config['db_name'] = '%DB_NAME%';
$config['db_user'] = '%DB_USER%';
$config['db_password'] = '%DB_PASSWORD%';

$config['http_host'] = '%HTTP_HOST%';
$config['http_path'] = '%HOST_DIR%';

$config['https_host'] = '%HTTPS_HOST%';
$config['https_path'] = '%HOST_DIR%';
*/

// You can also configure cache and storage backend

/*
// Cache backend
// Available backends: file, sqlite, database, redis, xcache, apc
// To use sqlite cache the "sqlite3" PHP module should be installed
// To use xcache cache the "xcache" PHP module should be installed
// To use apc cache the "apc" PHP module should be installed
$config['cache_backend'] = 'file';
$config['cache_redis_server'] = 'localhost';
$config['cache_redis_global_ttl'] = 0; // set this if your cache size reaches Redis server memory size

// Storage backend for sessions. Available backends: database, redis
$config['session_backend'] = 'database';
$config['session_redis_server'] = 'localhost';
$config['cache_apc_global_ttl'] = 0;
$config['cache_xcache_global_ttl'] = 0;
*/
