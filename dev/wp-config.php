<?php
// Lokální vývoj mnd-drilling.eu — NENASAZOVAT na produkci.
define('DB_NAME', 'mnddrilling');
define('DB_USER', 'wp');
define('DB_PASSWORD', 'wp');
define('DB_HOST', 'db');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
$table_prefix = 'wp_';

define('AUTH_KEY', '?!18{oJY:!w)I,|?!5vUUR.0#<f3vpwZNV,Vhwtm6(MG{MpSgnbXfNH8cwk{*6LP');
define('SECURE_AUTH_KEY', 'Q,wSG}6N6g|[N7]ghw^#B|!}18Ol%DKS4.)W%[<3:qJYJ(n41yZJ!D>Rm9UE^At.');
define('LOGGED_IN_KEY', 'P,cD(g|#WjR,S~:BGFI>)KK4nL|F:/REt,nPxPEF9jARH3&FXHPc_KY})gg29,&M');
define('NONCE_KEY', 'PBVNJnd&meDF?+mE1dLDWgk6rOAon9:NHn[Z>=^%6iJBLz*%KB3:QTe|te@B@o@l');
define('AUTH_SALT', 'I9l|6&MU<x8XaJ57-6Mm:[B_/51ipB(!8-x2:8YBP!S@wv65LRkjMW^vK|#?,Yfj');
define('SECURE_AUTH_SALT', 'WU]<^d7,QEPrJfo}C_rcX%FYgMI-0^Gg2+^grXglxWQ[,;.ap:W|E%Th?!]F?|/]');
define('LOGGED_IN_SALT', ')^]EeUx)SX.j@[]d<c5JJ:aL*U|(gfP+ZIqzOV0zjI5q3D@loPCBaNE%Fu!}LruP');
define('NONCE_SALT', '?(]zT]adC:AFA&A<l@9Jwhh{WdY<6Rr&Q4,jd,}ITBW18!]Tw^o>LvkHLWH6dR.K');

define('WP_HOME', 'http://localhost:8322');
define('WP_SITEURL', 'http://localhost:8322');
define('WP_ENVIRONMENT_TYPE', 'local');
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);
define('FS_METHOD', 'direct');

if ( ! defined('ABSPATH') ) {
	define('ABSPATH', __DIR__ . '/');
}
require_once ABSPATH . 'wp-settings.php';
