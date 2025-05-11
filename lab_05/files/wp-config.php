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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '@ukeozHquc(:`{4F^z3ug3v*1tcf5+>/@Prs$x+LmXqoHs+_RV4&%x&h{*r7qi&C' );
define( 'SECURE_AUTH_KEY',  '{@CE&yPh?|A%Q$ABa;}}|c(Y`;sO>-05lXw$FTOc#iGy_vxF!to|1mh(I0|mf^h5' );
define( 'LOGGED_IN_KEY',    '+Lck!Tx9$E1?)g!_-{ji-ZF}3@p)ZQR*rY_g.>eS[P~6oo+d6k}s;6Im9#Q#!+H,' );
define( 'NONCE_KEY',        'i+w!`fP.3;=ab?<YS*hk@5eA&)pHl,_HYw/s=b<:}^#g[,#qBUk=MWhJWHB71L}h' );
define( 'AUTH_SALT',        '@[?dh1b%8 42;cR.nu<}gQ2UR$y cn+!too>J+mC8zyi7O-L1|1s5{RhGLP4-X|S' );
define( 'SECURE_AUTH_SALT', '*`XIV&7;z!mx6Rx4FjqG%[k9^|mu9^6W[kort)%%8BEhg3xeGe9Evio6#kltxiNd' );
define( 'LOGGED_IN_SALT',   'pdu`<U=5{o7VIr>;oad5ToQt4g~wx@^&1ok^!SkJ]$lAA=?0#MN;K k6Br&~$d&)' );
define( 'NONCE_SALT',       '[]+& c~H3U^j1+B{cJE#}BfcKgi$uuR`],xR!1 :qn#x7/*65D.2y2X3]pwYZlZM' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
