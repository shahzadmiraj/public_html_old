<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
 

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'eventap1_wp691' );

/** MySQL database username */
define( 'DB_USER', 'eventap1_wp691' );

/** MySQL database password */
define( 'DB_PASSWORD', 'pgiJ!@2S22' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'o8pmaxsou6ng9y7dw74wn2hpnodzglvdzs4puupzu24lnwuck7q2w8fcjc1slfyj' );
define( 'SECURE_AUTH_KEY',  'nbntvvpvdfn9o5jwriq8xsg2wljikbekolo2lmvivwz01arwaiys0y3ryamfap2o' );
define( 'LOGGED_IN_KEY',    'spm1q56jxcocldgqp8e6cdb64xgimskm7dqq2fejc2c8rv6n1v3fzeexxoasfkix' );
define( 'NONCE_KEY',        'mrw4lg57muhzyu8itlwqbdyywfdmkhm6kdvcvmvc6vwzunuf8pa0tes2kgy4abu3' );
define( 'AUTH_SALT',        'fjnq0mdlcgufxzjxqf2s8xe2nqxcjgup8safh9id02accpprxj5siuvua4edzmw6' );
define( 'SECURE_AUTH_SALT', 'xbwby5byuqe75zm5ijpo0myt5as57sum5q5ogsg4mheqvvyy9inyp67yhqrqojdy' );
define( 'LOGGED_IN_SALT',   'q0trwmec09mhfhkc0ebszg7ok9k8n2ghwhc6iqscaaaxuotteia7ltjkl7o9dcbq' );
define( 'NONCE_SALT',       '4tj38m3w7py9ocnjzycwjth6dfqe88um5p7lkf9t4fpgoixlhqpqzncuvdbayyh4' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpsh_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


