<?php
// Local config for the Field Notes demo. Reprint supplies the target database.
if ( ! defined( 'ABSPATH' ) || ! defined( 'DB_NAME' ) ) {
	exit;
}

if ( ! defined( 'DB_CHARSET' ) ) {
	define( 'DB_CHARSET', 'utf8mb4' );
}
if ( ! defined( 'DB_COLLATE' ) ) {
	define( 'DB_COLLATE', '' );
}

$table_prefix = 'wp_';
require_once ABSPATH . 'wp-settings.php';
