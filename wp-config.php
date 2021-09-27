<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
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
define( 'DB_NAME', 'eventap1_27sepc' );

/** MySQL database username */
define( 'DB_USER', 'eventap1_27sepc' );

/** MySQL database password */
define( 'DB_PASSWORD', '.)7p52SF83' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'cvejrxu26wz781xfvjkzaywux7kpb6ymectjghqmy1zlr0mjpkipmmt78puxo9wu' );
define( 'SECURE_AUTH_KEY',  'u3oeyq9qatfv3kb76j91myyzgirky2vaj6olv6vduhod7ka7lmab37xleo0dplin' );
define( 'LOGGED_IN_KEY',    'aw6morjsnclzrcpftwgzywobn4omudw18uzd10q4gfwsyaorfz3ia2sjfngqjai0' );
define( 'NONCE_KEY',        'niugroyqjiu3l7jjkznqfrbirf27p7h23g5m6x1jx1ddlpcxjtn40ig82ykjnxzc' );
define( 'AUTH_SALT',        'qnpnuqswzgymnof89m2sytjr5gjat9465bohj2vshzkopniamlxagrwejk6atnuh' );
define( 'SECURE_AUTH_SALT', 'iilyq1u8h8cpslzysmfczvkoltbcfmccbpasyzlg2ocqrionk06zkrg9q2ood2k6' );
define( 'LOGGED_IN_SALT',   '93hkoce9jezeoxbymdmllqdfmtt99iufigzcuonlt8ghdpt3toodkpxlyvruauzs' );
define( 'NONCE_SALT',       'obulafimrwpqpsbwywxzrev8zyg0zjxghqwtkcgvllaht3f3oeuzgcxesr7cza5l' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpmg_';

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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
