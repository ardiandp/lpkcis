<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'lpkcis' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'wtTMjgtnn0DltKqLS5Fv851KV7QLW9JW2E92TiNZ4i8aJwbqn9nC7eHw7yv2IEIf' );
define( 'SECURE_AUTH_KEY',  'rcKf5rSCYzkFwUIUHDdb60RDZCvgIHZiJA2CucX1UDoXKG79FJ4WpplPv271AebT' );
define( 'LOGGED_IN_KEY',    '4Dc2Q9QK3p63TTFO18d0BvPXoLmYWdzojFS39GcPJpr4BcfDAPgvAY6Asav6BAMf' );
define( 'NONCE_KEY',        'IIjWp8uicyDBMj5YdpkSqBYZ9bMQ60vWIJ9vA5PTzTxhhe0nnCL2Agd4Tb0LZoaR' );
define( 'AUTH_SALT',        'uis5b4GtwGmXZnbZMV6HFjSAn1xrAGqFmf1wcg3jprcddzGtYYcYjDoA5uENrnHm' );
define( 'SECURE_AUTH_SALT', 'AZmYk89Yr2pRFRSTjVWAwEgBrlSq5sGnx68dbMm4hVArJkQTTEM96Za1WyXwriuM' );
define( 'LOGGED_IN_SALT',   'TGjiS3EJGjOlYEXwwm8HgdbLwuURhrXqdRsZwWQx0y77KFg3rrf0jx4zidZZyyAX' );
define( 'NONCE_SALT',       'ZkQKgycxVJuGGxXVGrHhKcHExpx03R2QepOUIMXlvdBWn9wXB2bJHhqmDS8NQS0t' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
