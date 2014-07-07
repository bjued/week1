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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'VoeU4p7JrPoJxqfWU7');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'flLO1,W.P+bu@;lpKN}H,2b)fP[:y^]ERu|Cb]r/2wv.M/(wLAKV(Iu.?-T6=16M');
define('SECURE_AUTH_KEY',  '/VniuoA:2+KL]]c@Y(<{=]!g[P>:+#iENupRUNUB|qM$c0+K[PO(xx_NTyAh<*kj');
define('LOGGED_IN_KEY',    '1ul,HaN^mqbz94eR4dZk~2N|B(A/2@7/uL!$];s8]-8R!}[FxHWxWph2vh@*{y,t');
define('NONCE_KEY',        'a[M0!hqbJQiEE WP?S034 *X+J|W,asnpR=@VBr9O-]6b+5N Q422WWMPpCn!/3E');
define('AUTH_SALT',        '&Tz$}SG*={yatK{*`iAo/5]KnKEbFz}gg-)![OQo#d.O%nCJ=r-oy7|W5Fn,4CzB');
define('SECURE_AUTH_SALT', 'ac=or5opI05+(]l_,3IrP`n7OO`8ZCD2YjEx0@zV(tn7._l:G-BTT[-WvM5UqFzE');
define('LOGGED_IN_SALT',   'iJ T3dIZS|R_p+J3Do;RL+scjq`D%+0V!`2tb%ecIWUK])qChO=zn_G><i4?ADRu');
define('NONCE_SALT',       '&Zpw%=DH#(kGk-W6P^:}^Qj|PIB/Fj*kG2A&tBOpfP)F50RvlQ}Ga^T9R%HE6Efb');

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
