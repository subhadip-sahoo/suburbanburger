<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'SuburbanBurger');

/** MySQL database username */
define('DB_USER', 'SuburbanBurger');

/** MySQL database password */
define('DB_PASSWORD', 'Subua291$#!');

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
define('AUTH_KEY',         'GxAB,RX3J;i5tP)~!m(OFGt3y[|mbk1hCT y?W6JQ ~4g^0]jk8:P,!1G-!pc:fx');
define('SECURE_AUTH_KEY',  'u0f@{/wg$vBzRNa{+j>$Tolx]22$k57?7I]+8+F-A`7{`t_?ZXpRD&jT!j)jQUsv');
define('LOGGED_IN_KEY',    'e%)9~Q5+@m}HeSd?F42aV4I-1ovKQ:PxfBuMc+so:nleOpw/XpqA06OUT_|4MP|6');
define('NONCE_KEY',        'gj?3JfW#X*O|2`.|c+eJAd45w=J*6PZ1ONb?,B@T%K;i6 [N?=w$C&DnR.>2!<}H');
define('AUTH_SALT',        'bO0(r`1xC%RX~[C~wp%RyZI&@{Vx%($+E_Hy-:*Uc4yz@cI9.K(Y6+RU)Y)-?GC%');
define('SECURE_AUTH_SALT', '`#<LAQe8E[vkV1U;4bU):8nEaJRa!xmkN=kWx,vtQ+YYfC>wm8gS;i/x|)vhgoDQ');
define('LOGGED_IN_SALT',   'BwkKT@CgdHqKrUP|-W{&Ak yD}3J[i}i1m{EhB~|c~irYSPtxx29XsaU)$cs$etq');
define('NONCE_SALT',       '=*Zhcvww;[`oE-hRJO{u+O`x<Li~h {jgN2*NhpC+<{xDT&^BUHLtS>|XkhCSEL4');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', FALSE);

/* template pages */
define('MY_ACCOUNT', 31);
define('BOOK_NOW', 60);
define('CHECKOUT', 84);

/* Disable WP auto update */ 
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );

/* Date Time Format */
define('DATE_DISPLAY_FORMAT', 'jS M, Y');
define('DATE_DATABASE_FORMAT', 'Y-m-d');
define('DATETIME_DISPLAY_FORMAT', 'jS M, Y h:i a');
define('DATETIME_DATABASE_FORMAT', 'Y-m-d H:i:s');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
