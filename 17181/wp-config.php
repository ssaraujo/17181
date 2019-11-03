<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress17181');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '/rx,93*Uu*s||A1EhQ<%UY&@<f)frI$*z%Y6p}ob{oW,+G%NAo!iNq0O!9vBt+VL');
define('SECURE_AUTH_KEY',  'r+=of-~3+wVF@!q1J25Ji0b+2{bGZtbxhcR>RA<8R.EZ5Q6Q+:uFf)bItFA HrjJ');
define('LOGGED_IN_KEY',    'ICtLFPohx@5A(<x.wsRMeo!01WoHA^8iCPZ(dsxKU=~5pg6d:_ZLV26#{p*caFp7');
define('NONCE_KEY',        'K<M~k*}Ze3WQE;[kr{_W1f$.HSO4tD>h}g+iN%YYig%*6z[u uoHY$}$y3&!~Of{');
define('AUTH_SALT',        'yrtoebI=P{[*&yGZ+3t,|%^q~3rZ$5GkRa 6}W#bVS=5mbN4_bpA Y=]2S<o]ZDb');
define('SECURE_AUTH_SALT', '3}WY!^iQ0@*2XVZgy`uf&`IUhq`#D%cr$V,R93(*brd>`=Yd3K8j1E[BuwF)fF^#');
define('LOGGED_IN_SALT',   '(G<10<?/ n_sz]8R-G%(W$GQlD+mkz=Je~UVT/?IoROq<W70WwP]$22v%0pdXNoJ');
define('NONCE_SALT',       '3IoZ}y[ggMW:5!Y{^ A:)pXu7I%8qmz3XI^KAzEHkB;BuewE%?pFU= y;}4+3aN*');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysqli_select_db($connection, DB_NAME);
