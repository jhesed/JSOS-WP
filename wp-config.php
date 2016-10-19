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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'jsos');

/** MySQL database username */
define('DB_USER', 'jsos_wp');

/** MySQL database password */
define('DB_PASSWORD', '12345jhesed');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'y&a5lm2AC8}aZoU}^ FJIb.pYXx)?kTW&,cKyvXc#Gvd9aFN[1VUu22DCMwi<UaR');
define('SECURE_AUTH_KEY',  'zZdg5t[?Ip0,i4U| zW9^gLx%HsM^wtnNO/CSr|EgRJ%&BslU{P0lo@8H$.%v.pT');
define('LOGGED_IN_KEY',    ':%buTSRA&Kvqo&^b[[HxLrY8H&UsiwTd|oA?`EUCm$W@O}J|wWELKJo!+{M`<h4`');
define('NONCE_KEY',        'rjUz4;pXXm(&_HQh7 hdsG?E}#ye* xB!X~^</obNufM7+Ad*KX#CH<Y*%z)0oj~');
define('AUTH_SALT',        'Vf$;X,.h/by*E5A!>lv6f|bi!!A)KetT+>cQR>0gyYjmu@T23bA^v7m &EQIBLt^');
define('SECURE_AUTH_SALT', '(}Z4bl)7(n5SA*?7cdIkqjyc,A4Pcal06o|JSgI17in3:M^) UT /{n.0RKG2)!3');
define('LOGGED_IN_SALT',   'z$O2|gAIgTo+vIE91lQVfO XI,>ZE#zl%dEYTEkHBaBb:_MU6qpUdk0V6p.|;[Z%');
define('NONCE_SALT',       ' wVBO|L#CnE#`E{T8cc(#i3zOb?kHJHOSi~dE{A(eH`:7}.aMP8@Pf}<dW1{%=>^');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
