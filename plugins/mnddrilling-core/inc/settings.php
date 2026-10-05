<?php
/**
 * Nastavení → MND Drilling Core: zapnutí a vypnutí jednotlivých funkcí pluginu.
 *
 * Výchozí hodnoty odpovídají doporučenému nastavení (vše zapnuté), takže po instalaci
 * plugin funguje bez konfigurace. Uloženo v jedné volbě mnd_core_settings.
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

const MND_CORE_SETTINGS      = 'mnd_core_settings';
const MND_CORE_SETTINGS_PAGE = 'mnddrilling-core';

/**
 * Výchozí nastavení.
 *
 * @return array
 */
function mnd_core_settings_defaults() {
	return array(
		'login_protection' => 1,
		'login_attempts'   => 5,
		'login_lockout'    => 30,
		'login_log'        => 1,
		'xmlrpc_off'       => 1,
		'file_edit_off'    => 1,
		'hide_users'       => 1,
		'hide_version'     => 1,
		'security_headers' => 1,
		'hsts'             => 1,
		'sitemap_no_users' => 1,
		'sitemap_lastmod'  => 1,
		'llms_txt'         => 1,
		'webp'             => 1,
		'comments_off'     => 1,
		'core_updates'     => 'all',
		'plugin_updates'   => 'all',
		'admin_footer'     => 1,
		'health_widget'    => 1,
	);
}

/**
 * Aktuální nastavení (uložené hodnoty doplněné o výchozí).
 *
 * @return array
 */
function mnd_core_settings() {
	static $settings = null;
	if ( null === $settings ) {
		$saved    = get_option( MND_CORE_SETTINGS, array() );
		$settings = array_merge( mnd_core_settings_defaults(), is_array( $saved ) ? $saved : array() );
	}
	return $settings;
}

/**
 * Je funkce zapnutá?
 *
 * @param string $key Klíč nastavení.
 * @return bool
 */
function mnd_core_on( $key ) {
	$settings = mnd_core_settings();
	return ! empty( $settings[ $key ] );
}

/**
 * Hodnota nastavení.
 *
 * @param string $key Klíč nastavení.
 * @return mixed
 */
function mnd_core_get( $key ) {
	$settings = mnd_core_settings();
	return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
}

/**
 * Popis voleb: klíč => [typ, popisek, nápověda, (volby | [min, max, jednotka])].
 *
 * @return array Sekce => volby.
 */
function mnd_core_settings_fields() {
	return array(
		__( 'Přihlášení', 'mnddrilling-core' )    => array(
			'login_protection' => array( 'checkbox', __( 'Omezení pokusů o přihlášení', 'mnddrilling-core' ), __( 'Po opakovaných chybných pokusech z jedné IP adresy se přihlášení dočasně zablokuje. Chybové hlášky neprozradí, jestli uživatel existuje.', 'mnddrilling-core' ) ),
			'login_attempts'   => array( 'number', __( 'Počet pokusů', 'mnddrilling-core' ), __( 'Kolik chybných pokusů během 15 minut je povoleno.', 'mnddrilling-core' ), array( 3, 20, __( 'pokusů', 'mnddrilling-core' ) ) ),
			'login_lockout'    => array( 'number', __( 'Délka blokace', 'mnddrilling-core' ), __( 'Každá další blokace během 24 hodin je dvakrát delší (nejvýš 24 h). Zrušit ji jde v Nástroje → Údržba webu.', 'mnddrilling-core' ), array( 5, 1440, __( 'minut', 'mnddrilling-core' ) ) ),
			'login_log'        => array( 'checkbox', __( 'Log přihlášení', 'mnddrilling-core' ), __( 'Záznam přihlášení, neúspěšných pokusů a blokací (posledních 200 událostí, nejvýš 90 dní) v Nástroje → Log přihlášení a sloupec Poslední přihlášení v přehledu uživatelů.', 'mnddrilling-core' ) ),
		),
		__( 'Zabezpečení', 'mnddrilling-core' )   => array(
			'xmlrpc_off'       => array( 'checkbox', __( 'Vypnout XML-RPC a pingbacky', 'mnddrilling-core' ), __( 'Staré rozhraní pro vzdálené publikování, častý cíl útoků. ManageWP ho nepotřebuje.', 'mnddrilling-core' ) ),
			'file_edit_off'    => array( 'checkbox', __( 'Zakázat editor souborů', 'mnddrilling-core' ), __( 'Skryje úpravy kódu šablon a pluginů v administraci (Vzhled → Editor souborů).', 'mnddrilling-core' ) ),
			'hide_users'       => array( 'checkbox', __( 'Skrýt uživatelská jména', 'mnddrilling-core' ), __( 'Adresy ?author=… a archivy autorů přesměrují na úvodní stránku, seznam uživatelů v REST API jen pro přihlášené, oEmbed bez autora.', 'mnddrilling-core' ) ),
			'hide_version'     => array( 'checkbox', __( 'Skrýt verzi WordPressu', 'mnddrilling-core' ), __( 'Bez meta značky generator.', 'mnddrilling-core' ) ),
			'security_headers' => array( 'checkbox', __( 'Bezpečnostní HTTP hlavičky', 'mnddrilling-core' ), __( 'X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy.', 'mnddrilling-core' ) ),
			'hsts'             => array( 'checkbox', __( 'HSTS (vynutit HTTPS)', 'mnddrilling-core' ), __( 'Prohlížeč bude web rok otevírat jen přes HTTPS (bez subdomén, nikdy na lokálním vývoji). Zapínejte jen, pokud HTTPS funguje spolehlivě.', 'mnddrilling-core' ) ),
		),
		__( 'SEO', 'mnddrilling-core' )           => array(
			'sitemap_no_users' => array( 'checkbox', __( 'Sitemap bez uživatelů', 'mnddrilling-core' ), __( 'Z /wp-sitemap.xml zmizí seznam autorů (prozrazuje přihlašovací jména).', 'mnddrilling-core' ) ),
			'sitemap_lastmod'  => array( 'checkbox', __( 'Datum změny v sitemapě', 'mnddrilling-core' ), __( 'Vyhledávače poznají, které stránky se změnily.', 'mnddrilling-core' ) ),
			'llms_txt'         => array( 'checkbox', __( '/llms.txt', 'mnddrilling-core' ), __( 'Stručný textový přehled webu pro AI vyhledávače (llmstxt.org) na adrese /llms.txt.', 'mnddrilling-core' ) ),
		),
		__( 'Média', 'mnddrilling-core' )         => array(
			'webp'             => array( 'checkbox', __( 'Obrázky jako WebP', 'mnddrilling-core' ), __( 'Nahrané JPG a PNG se uloží rovnou jako WebP (fotky se otočí podle EXIF, originál se neukládá) a zmenšeniny se tvoří ve WebP. GIF a SVG beze změny. Starší obrázky převede Nástroje → Údržba webu.', 'mnddrilling-core' ) ),
		),
		__( 'Komentáře', 'mnddrilling-core' )     => array(
			'comments_off'     => array( 'checkbox', __( 'Vypnout komentáře', 'mnddrilling-core' ), __( 'Komentáře a pingbacky úplně vypnuté včetně administrace, kanálů a widgetu.', 'mnddrilling-core' ) ),
		),
		__( 'Aktualizace', 'mnddrilling-core' )   => array(
			'core_updates'     => array(
				'select',
				__( 'WordPress', 'mnddrilling-core' ),
				__( 'Automatické aktualizace jádra WordPressu.', 'mnddrilling-core' ),
				array(
					'all'   => __( 'Všechny verze automaticky', 'mnddrilling-core' ),
					'minor' => __( 'Jen opravné verze (výchozí WordPress)', 'mnddrilling-core' ),
					'off'   => __( 'Vypnuto', 'mnddrilling-core' ),
				),
			),
			'plugin_updates'   => array(
				'select',
				__( 'Pluginy a šablony', 'mnddrilling-core' ),
				__( 'Šablona a plugin MND se automaticky neaktualizují nikdy – nabídnou se v Nástěnka → Aktualizace a instalují se kliknutím.', 'mnddrilling-core' ),
				array(
					'all'    => __( 'Všechny automaticky', 'mnddrilling-core' ),
					'manual' => __( 'Podle nastavení u jednotlivých pluginů a šablon', 'mnddrilling-core' ),
				),
			),
		),
		__( 'Administrace', 'mnddrilling-core' )  => array(
			'admin_footer'     => array( 'checkbox', __( 'Info o serveru v patičce', 'mnddrilling-core' ), __( 'Verze PHP a databáze, IP serveru a využití paměti v patičce administrace (jen pro administrátory).', 'mnddrilling-core' ) ),
			'health_widget'    => array( 'checkbox', __( 'Widget Zdraví webu', 'mnddrilling-core' ), __( 'Přehled verzí, aktualizací, databáze a přihlášení na Nástěnce.', 'mnddrilling-core' ) ),
		),
	);
}

/**
 * Očištění hodnot z formuláře.
 *
 * @param mixed $input Odeslané hodnoty.
 * @return array
 */
function mnd_core_sanitize_settings( $input ) {
	$input  = is_array( $input ) ? $input : array();
	$clean  = array();
	foreach ( mnd_core_settings_fields() as $fields ) {
		foreach ( $fields as $key => $field ) {
			$value = isset( $input[ $key ] ) ? $input[ $key ] : null;
			switch ( $field[0] ) {
				case 'checkbox':
					$clean[ $key ] = empty( $value ) ? 0 : 1;
					break;
				case 'number':
					$default       = mnd_core_settings_defaults()[ $key ];
					$clean[ $key ] = null === $value || '' === $value ? $default : max( $field[3][0], min( $field[3][1], absint( $value ) ) );
					break;
				case 'select':
					$clean[ $key ] = array_key_exists( (string) $value, $field[3] ) ? (string) $value : mnd_core_settings_defaults()[ $key ];
					break;
			}
		}
	}
	return $clean;
}

/**
 * Registrace volby a stránky.
 */
function mnd_core_settings_init() {
	register_setting(
		'mnd_core',
		MND_CORE_SETTINGS,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'mnd_core_sanitize_settings',
			'default'           => mnd_core_settings_defaults(),
		)
	);
}
add_action( 'admin_init', 'mnd_core_settings_init' );

/**
 * Stránka v menu Nastavení.
 */
function mnd_core_settings_menu() {
	add_options_page( 'MND Drilling Core', 'MND Drilling Core', 'manage_options', MND_CORE_SETTINGS_PAGE, 'mnd_core_settings_page' );
}
add_action( 'admin_menu', 'mnd_core_settings_menu' );

/**
 * Odkaz „Nastavení“ v přehledu pluginů.
 *
 * @param array $links Odkazy.
 * @return array
 */
function mnd_core_settings_link( $links ) {
	array_unshift( $links, '<a href="' . esc_url( mnd_core_settings_url() ) . '">' . esc_html__( 'Nastavení', 'mnddrilling-core' ) . '</a>' );
	return $links;
}
add_filter( 'plugin_action_links_' . MND_CORE_BASENAME, 'mnd_core_settings_link' );

/**
 * URL stránky nastavení.
 *
 * @return string
 */
function mnd_core_settings_url() {
	return admin_url( 'options-general.php?page=' . MND_CORE_SETTINGS_PAGE );
}

/**
 * Vykreslení jednoho pole.
 *
 * @param string $key   Klíč.
 * @param array  $field Popis pole.
 */
function mnd_core_settings_field( $key, $field ) {
	$name  = MND_CORE_SETTINGS . '[' . $key . ']';
	$id    = 'mnd-core-' . str_replace( '_', '-', $key );
	$value = mnd_core_get( $key );

	switch ( $field[0] ) {
		case 'checkbox':
			printf(
				'<label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( ! empty( $value ), true, false ),
				esc_html__( 'Zapnuto', 'mnddrilling-core' )
			);
			break;
		case 'number':
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$d" min="%4$d" max="%5$d" step="1" class="small-text"> %6$s',
				esc_attr( $id ),
				esc_attr( $name ),
				(int) $value,
				(int) $field[3][0],
				(int) $field[3][1],
				esc_html( $field[3][2] )
			);
			break;
		case 'select':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field[3] as $option => $label ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $option ), selected( $value, $option, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
	}

	if ( 'file_edit_off' === $key && defined( 'DISALLOW_FILE_EDIT' ) && ! mnd_core_on( 'file_edit_off' ) ) {
		echo '<p class="description"><strong>' . esc_html__( 'Editor je zakázaný konstantou DISALLOW_FILE_EDIT ve wp-config.php – tam má přednost.', 'mnddrilling-core' ) . '</strong></p>';
	}
	echo '<p class="description">' . esc_html( $field[2] ) . '</p>';
}

/**
 * Stránka Nastavení → MND Drilling Core.
 */
function mnd_core_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'MND Drilling Core', 'mnddrilling-core' ); ?></h1>
		<p>
			<?php esc_html_e( 'Funkce webu nezávislé na šabloně. Výchozí stav je doporučený – vypínejte jen, pokud něco konkrétního potřebujete jinak.', 'mnddrilling-core' ); ?>
			<a href="<?php echo esc_url( mnd_core_maintenance_url() ); ?>"><?php esc_html_e( 'Údržba webu →', 'mnddrilling-core' ); ?></a>
		</p>
		<?php if ( 'local' === wp_get_environment_type() ) : ?>
			<div class="notice notice-info inline"><p><?php esc_html_e( 'Lokální vývoj: automatické aktualizace jsou vypnuté bez ohledu na nastavení.', 'mnddrilling-core' ); ?></p></div>
		<?php endif; ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'mnd_core' ); ?>
			<?php foreach ( mnd_core_settings_fields() as $section => $fields ) : ?>
				<h2 class="title"><?php echo esc_html( $section ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $fields as $key => $field ) : ?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( 'mnd-core-' . str_replace( '_', '-', $key ) ); ?>"><?php echo esc_html( $field[1] ); ?></label></th>
							<td><?php mnd_core_settings_field( $key, $field ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
