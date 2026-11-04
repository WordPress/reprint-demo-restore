<?php
/**
 * Import the public Field Notes content into an empty staging WordPress site.
 * Run with wp eval-file restore.php <author-id> from the WordPress directory.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

global $wpdb;

if ( is_multisite() || ! in_array( wp_get_environment_type(), array( 'local', 'staging' ), true )
	|| wp_parse_url( home_url(), PHP_URL_HOST ) === 'reprint-demo.wordpress.net' ) {
	WP_CLI::error( 'Restore into a fresh single-site local or staging install. Do not run this on the live demo.' );
}

$author_id = filter_var( $args[0] ?? '', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
if ( ! $author_id || ! get_user_by( 'id', $author_id ) ) {
	WP_CLI::error( 'Pass the ID of the local Field Notes author account.' );
}

$site = json_decode( file_get_contents( __DIR__ . '/site.json' ), true, 512, JSON_THROW_ON_ERROR );
$tables = array( 'terms', 'term_taxonomy', 'posts', 'postmeta', 'term_relationships' );
foreach ( $tables as $table ) {
	// The names come from the list above, not from the import file.
	$count = $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->$table}`" );
	if ( '0' === (string) $count ) {
		continue;
	}
	// wp site empty recreates Uncategorized. Keep it if it matches the snapshot.
	if ( '1' === (string) $count && in_array( $table, array( 'terms', 'term_taxonomy' ), true ) ) {
		$row = $wpdb->get_row( "SELECT * FROM `{$wpdb->$table}`", ARRAY_A );
		if ( is_array( $row ) && array_map( 'strval', $row ) == array_map( 'strval', $site[ $table ][0] ) ) {
			array_shift( $site[ $table ] );
			continue;
		}
	}
	WP_CLI::error( "Unexpected existing rows in {$wpdb->$table}. Nothing was imported." );
}

foreach ( $tables as $table ) {
	foreach ( $site[ $table ] as $row ) {
		if ( 'posts' === $table ) {
			// Keep IDs for block references, but use a user created on this site.
			$row['post_author']           = $author_id;
			$row['post_password']         = '';
			$row['post_content_filtered'] = '';
			$row['to_ping']               = '';
			$row['pinged']                = '';
			$row['post_content']          = str_replace( '{{site_url}}', untrailingslashit( home_url() ), $row['post_content'] );
		}

		if ( false === $wpdb->insert( $wpdb->$table, $row ) ) {
			WP_CLI::error( "Could not insert a row into {$wpdb->$table}: {$wpdb->last_error}. Recreate the staging site before retrying." );
		}
	}
}

foreach ( $site['options'] as $name => $value ) {
	update_option( $name, $value );
}
wp_cache_flush();
WP_CLI::success( 'Field Notes content imported.' );
