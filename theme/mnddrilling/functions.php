<?php
/**
 * MND Drilling — šablona pro www.mnd-drilling.eu
 *
 * Vzhled odpovídá původní šabloně „Ultimate for MND Drilling & Services“ (2022),
 * kód je napsaný znovu: bez jQuery, bez build kroku, responzivní.
 * Typy obsahu, pole, bezpečnost a údržba jsou v pluginu MND Drilling Core (plugins/mnddrilling-core).
 *
 * Každý modul v inc/ jde vypnout zakomentováním řádku níže.
 */

defined( 'ABSPATH' ) || exit;

define( 'MND_VERSION', (string) wp_get_theme( get_template() )->get( 'Version' ) );
define( 'MND_DIR', get_template_directory() );
define( 'MND_URI', get_template_directory_uri() );

$mnd_modules = array(
	'setup',             // podpora šablony, menu, widgety, velikosti obrázků
	'assets',            // CSS/JS, verze podle data změny souboru
	'cleanup',           // emoji, zbytečné odkazy v <head>
	'template-tags',     // texty v češtině/angličtině, ikony, logo, podmenu sekce, stránkování
	'polylang',          // přepínač jazyků, přeložitelné texty, převzetí menu po aktivaci
	'navigation',        // dlaždice divizí (hlavní menu)
	'forms',             // třídy formulářů Formidable a předvyplnění pozice
	'typography',        // české pevné mezery za předložkami a mezi číslem a jednotkou
	'seo',               // meta description, Open Graph, JSON-LD (vypne se, když je aktivní SEO plugin)
	'customizer',        // Vzhled → Přizpůsobit → MND Drilling (GA4 ID, interval slideru)
	'analytics-consent', // GA4 s cookie lištou (nahrazuje MonsterInsights)
	'updater',           // aktualizace z GitHub Releases
	'plugin-check',      // upozornění, když chybí plugin MND Drilling Core
);

foreach ( $mnd_modules as $mnd_module ) {
	require MND_DIR . '/inc/' . $mnd_module . '.php';
}
