<?php
/**
 * EKC module: maint-filenames
 * Version: 1.0.0
 * Requires: common
 * Replaces: –
 * Summary: Opraví názvy souborů s diakritikou, které mají na disku jiný tvar Unicode (NFD z Macu) než v databázi.
 */

defined( 'ABSPATH' ) || exit;

/*
 * Soubory nahrané z Macu mají někdy na disku název v rozloženém tvaru Unicode (NFD – písmeno
 * a diakritické znaménko zvlášť), databáze a odkazy ale složený tvar (NFC). Linux je bere jako
 * dva různé názvy → obrázek nebo PDF vrací 404. Oprava v Údržbě přejmenuje soubor na tvar
 * z databáze (odkazy se tím rozjedou samy). Funguje i bez rozšíření intl (vlastní mapa pro
 * latinku), s ním navíc přes Normalizer. Modul webp volá ekc_filenames_fix_form() před převodem.
 */

/**
 * Složené znaky latinky (NFC) → rozložené (NFD), U+00C0–U+0233.
 *
 * @return array<string,string>
 */
function ekc_filenames_nfd_map(): array {
	static $map = null;
	if ( null === $map ) {
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing, WordPress.Arrays.MultipleStatementAlignment
		$map = array(
			"À"=>"\u{0041}\u{0300}", "Á"=>"\u{0041}\u{0301}", "Â"=>"\u{0041}\u{0302}", "Ã"=>"\u{0041}\u{0303}", "Ä"=>"\u{0041}\u{0308}", "Å"=>"\u{0041}\u{030a}",
			"Ç"=>"\u{0043}\u{0327}", "È"=>"\u{0045}\u{0300}", "É"=>"\u{0045}\u{0301}", "Ê"=>"\u{0045}\u{0302}", "Ë"=>"\u{0045}\u{0308}", "Ì"=>"\u{0049}\u{0300}",
			"Í"=>"\u{0049}\u{0301}", "Î"=>"\u{0049}\u{0302}", "Ï"=>"\u{0049}\u{0308}", "Ñ"=>"\u{004e}\u{0303}", "Ò"=>"\u{004f}\u{0300}", "Ó"=>"\u{004f}\u{0301}",
			"Ô"=>"\u{004f}\u{0302}", "Õ"=>"\u{004f}\u{0303}", "Ö"=>"\u{004f}\u{0308}", "Ù"=>"\u{0055}\u{0300}", "Ú"=>"\u{0055}\u{0301}", "Û"=>"\u{0055}\u{0302}",
			"Ü"=>"\u{0055}\u{0308}", "Ý"=>"\u{0059}\u{0301}", "à"=>"\u{0061}\u{0300}", "á"=>"\u{0061}\u{0301}", "â"=>"\u{0061}\u{0302}", "ã"=>"\u{0061}\u{0303}",
			"ä"=>"\u{0061}\u{0308}", "å"=>"\u{0061}\u{030a}", "ç"=>"\u{0063}\u{0327}", "è"=>"\u{0065}\u{0300}", "é"=>"\u{0065}\u{0301}", "ê"=>"\u{0065}\u{0302}",
			"ë"=>"\u{0065}\u{0308}", "ì"=>"\u{0069}\u{0300}", "í"=>"\u{0069}\u{0301}", "î"=>"\u{0069}\u{0302}", "ï"=>"\u{0069}\u{0308}", "ñ"=>"\u{006e}\u{0303}",
			"ò"=>"\u{006f}\u{0300}", "ó"=>"\u{006f}\u{0301}", "ô"=>"\u{006f}\u{0302}", "õ"=>"\u{006f}\u{0303}", "ö"=>"\u{006f}\u{0308}", "ù"=>"\u{0075}\u{0300}",
			"ú"=>"\u{0075}\u{0301}", "û"=>"\u{0075}\u{0302}", "ü"=>"\u{0075}\u{0308}", "ý"=>"\u{0079}\u{0301}", "ÿ"=>"\u{0079}\u{0308}", "Ā"=>"\u{0041}\u{0304}",
			"ā"=>"\u{0061}\u{0304}", "Ă"=>"\u{0041}\u{0306}", "ă"=>"\u{0061}\u{0306}", "Ą"=>"\u{0041}\u{0328}", "ą"=>"\u{0061}\u{0328}", "Ć"=>"\u{0043}\u{0301}",
			"ć"=>"\u{0063}\u{0301}", "Ĉ"=>"\u{0043}\u{0302}", "ĉ"=>"\u{0063}\u{0302}", "Ċ"=>"\u{0043}\u{0307}", "ċ"=>"\u{0063}\u{0307}", "Č"=>"\u{0043}\u{030c}",
			"č"=>"\u{0063}\u{030c}", "Ď"=>"\u{0044}\u{030c}", "ď"=>"\u{0064}\u{030c}", "Ē"=>"\u{0045}\u{0304}", "ē"=>"\u{0065}\u{0304}", "Ĕ"=>"\u{0045}\u{0306}",
			"ĕ"=>"\u{0065}\u{0306}", "Ė"=>"\u{0045}\u{0307}", "ė"=>"\u{0065}\u{0307}", "Ę"=>"\u{0045}\u{0328}", "ę"=>"\u{0065}\u{0328}", "Ě"=>"\u{0045}\u{030c}",
			"ě"=>"\u{0065}\u{030c}", "Ĝ"=>"\u{0047}\u{0302}", "ĝ"=>"\u{0067}\u{0302}", "Ğ"=>"\u{0047}\u{0306}", "ğ"=>"\u{0067}\u{0306}", "Ġ"=>"\u{0047}\u{0307}",
			"ġ"=>"\u{0067}\u{0307}", "Ģ"=>"\u{0047}\u{0327}", "ģ"=>"\u{0067}\u{0327}", "Ĥ"=>"\u{0048}\u{0302}", "ĥ"=>"\u{0068}\u{0302}", "Ĩ"=>"\u{0049}\u{0303}",
			"ĩ"=>"\u{0069}\u{0303}", "Ī"=>"\u{0049}\u{0304}", "ī"=>"\u{0069}\u{0304}", "Ĭ"=>"\u{0049}\u{0306}", "ĭ"=>"\u{0069}\u{0306}", "Į"=>"\u{0049}\u{0328}",
			"į"=>"\u{0069}\u{0328}", "İ"=>"\u{0049}\u{0307}", "Ĵ"=>"\u{004a}\u{0302}", "ĵ"=>"\u{006a}\u{0302}", "Ķ"=>"\u{004b}\u{0327}", "ķ"=>"\u{006b}\u{0327}",
			"Ĺ"=>"\u{004c}\u{0301}", "ĺ"=>"\u{006c}\u{0301}", "Ļ"=>"\u{004c}\u{0327}", "ļ"=>"\u{006c}\u{0327}", "Ľ"=>"\u{004c}\u{030c}", "ľ"=>"\u{006c}\u{030c}",
			"Ń"=>"\u{004e}\u{0301}", "ń"=>"\u{006e}\u{0301}", "Ņ"=>"\u{004e}\u{0327}", "ņ"=>"\u{006e}\u{0327}", "Ň"=>"\u{004e}\u{030c}", "ň"=>"\u{006e}\u{030c}",
			"Ō"=>"\u{004f}\u{0304}", "ō"=>"\u{006f}\u{0304}", "Ŏ"=>"\u{004f}\u{0306}", "ŏ"=>"\u{006f}\u{0306}", "Ő"=>"\u{004f}\u{030b}", "ő"=>"\u{006f}\u{030b}",
			"Ŕ"=>"\u{0052}\u{0301}", "ŕ"=>"\u{0072}\u{0301}", "Ŗ"=>"\u{0052}\u{0327}", "ŗ"=>"\u{0072}\u{0327}", "Ř"=>"\u{0052}\u{030c}", "ř"=>"\u{0072}\u{030c}",
			"Ś"=>"\u{0053}\u{0301}", "ś"=>"\u{0073}\u{0301}", "Ŝ"=>"\u{0053}\u{0302}", "ŝ"=>"\u{0073}\u{0302}", "Ş"=>"\u{0053}\u{0327}", "ş"=>"\u{0073}\u{0327}",
			"Š"=>"\u{0053}\u{030c}", "š"=>"\u{0073}\u{030c}", "Ţ"=>"\u{0054}\u{0327}", "ţ"=>"\u{0074}\u{0327}", "Ť"=>"\u{0054}\u{030c}", "ť"=>"\u{0074}\u{030c}",
			"Ũ"=>"\u{0055}\u{0303}", "ũ"=>"\u{0075}\u{0303}", "Ū"=>"\u{0055}\u{0304}", "ū"=>"\u{0075}\u{0304}", "Ŭ"=>"\u{0055}\u{0306}", "ŭ"=>"\u{0075}\u{0306}",
			"Ů"=>"\u{0055}\u{030a}", "ů"=>"\u{0075}\u{030a}", "Ű"=>"\u{0055}\u{030b}", "ű"=>"\u{0075}\u{030b}", "Ų"=>"\u{0055}\u{0328}", "ų"=>"\u{0075}\u{0328}",
			"Ŵ"=>"\u{0057}\u{0302}", "ŵ"=>"\u{0077}\u{0302}", "Ŷ"=>"\u{0059}\u{0302}", "ŷ"=>"\u{0079}\u{0302}", "Ÿ"=>"\u{0059}\u{0308}", "Ź"=>"\u{005a}\u{0301}",
			"ź"=>"\u{007a}\u{0301}", "Ż"=>"\u{005a}\u{0307}", "ż"=>"\u{007a}\u{0307}", "Ž"=>"\u{005a}\u{030c}", "ž"=>"\u{007a}\u{030c}", "Ơ"=>"\u{004f}\u{031b}",
			"ơ"=>"\u{006f}\u{031b}", "Ư"=>"\u{0055}\u{031b}", "ư"=>"\u{0075}\u{031b}", "Ǎ"=>"\u{0041}\u{030c}", "ǎ"=>"\u{0061}\u{030c}", "Ǐ"=>"\u{0049}\u{030c}",
			"ǐ"=>"\u{0069}\u{030c}", "Ǒ"=>"\u{004f}\u{030c}", "ǒ"=>"\u{006f}\u{030c}", "Ǔ"=>"\u{0055}\u{030c}", "ǔ"=>"\u{0075}\u{030c}", "Ǖ"=>"\u{0055}\u{0308}\u{0304}",
			"ǖ"=>"\u{0075}\u{0308}\u{0304}", "Ǘ"=>"\u{0055}\u{0308}\u{0301}", "ǘ"=>"\u{0075}\u{0308}\u{0301}", "Ǚ"=>"\u{0055}\u{0308}\u{030c}", "ǚ"=>"\u{0075}\u{0308}\u{030c}", "Ǜ"=>"\u{0055}\u{0308}\u{0300}",
			"ǜ"=>"\u{0075}\u{0308}\u{0300}", "Ǟ"=>"\u{0041}\u{0308}\u{0304}", "ǟ"=>"\u{0061}\u{0308}\u{0304}", "Ǡ"=>"\u{0041}\u{0307}\u{0304}", "ǡ"=>"\u{0061}\u{0307}\u{0304}", "Ǣ"=>"\u{00c6}\u{0304}",
			"ǣ"=>"\u{00e6}\u{0304}", "Ǧ"=>"\u{0047}\u{030c}", "ǧ"=>"\u{0067}\u{030c}", "Ǩ"=>"\u{004b}\u{030c}", "ǩ"=>"\u{006b}\u{030c}", "Ǫ"=>"\u{004f}\u{0328}",
			"ǫ"=>"\u{006f}\u{0328}", "Ǭ"=>"\u{004f}\u{0328}\u{0304}", "ǭ"=>"\u{006f}\u{0328}\u{0304}", "Ǯ"=>"\u{01b7}\u{030c}", "ǯ"=>"\u{0292}\u{030c}", "ǰ"=>"\u{006a}\u{030c}",
			"Ǵ"=>"\u{0047}\u{0301}", "ǵ"=>"\u{0067}\u{0301}", "Ǹ"=>"\u{004e}\u{0300}", "ǹ"=>"\u{006e}\u{0300}", "Ǻ"=>"\u{0041}\u{030a}\u{0301}", "ǻ"=>"\u{0061}\u{030a}\u{0301}",
			"Ǽ"=>"\u{00c6}\u{0301}", "ǽ"=>"\u{00e6}\u{0301}", "Ǿ"=>"\u{00d8}\u{0301}", "ǿ"=>"\u{00f8}\u{0301}", "Ȁ"=>"\u{0041}\u{030f}", "ȁ"=>"\u{0061}\u{030f}",
			"Ȃ"=>"\u{0041}\u{0311}", "ȃ"=>"\u{0061}\u{0311}", "Ȅ"=>"\u{0045}\u{030f}", "ȅ"=>"\u{0065}\u{030f}", "Ȇ"=>"\u{0045}\u{0311}", "ȇ"=>"\u{0065}\u{0311}",
			"Ȉ"=>"\u{0049}\u{030f}", "ȉ"=>"\u{0069}\u{030f}", "Ȋ"=>"\u{0049}\u{0311}", "ȋ"=>"\u{0069}\u{0311}", "Ȍ"=>"\u{004f}\u{030f}", "ȍ"=>"\u{006f}\u{030f}",
			"Ȏ"=>"\u{004f}\u{0311}", "ȏ"=>"\u{006f}\u{0311}", "Ȑ"=>"\u{0052}\u{030f}", "ȑ"=>"\u{0072}\u{030f}", "Ȓ"=>"\u{0052}\u{0311}", "ȓ"=>"\u{0072}\u{0311}",
			"Ȕ"=>"\u{0055}\u{030f}", "ȕ"=>"\u{0075}\u{030f}", "Ȗ"=>"\u{0055}\u{0311}", "ȗ"=>"\u{0075}\u{0311}", "Ș"=>"\u{0053}\u{0326}", "ș"=>"\u{0073}\u{0326}",
			"Ț"=>"\u{0054}\u{0326}", "ț"=>"\u{0074}\u{0326}", "Ȟ"=>"\u{0048}\u{030c}", "ȟ"=>"\u{0068}\u{030c}", "Ȧ"=>"\u{0041}\u{0307}", "ȧ"=>"\u{0061}\u{0307}",
			"Ȩ"=>"\u{0045}\u{0327}", "ȩ"=>"\u{0065}\u{0327}", "Ȫ"=>"\u{004f}\u{0308}\u{0304}", "ȫ"=>"\u{006f}\u{0308}\u{0304}", "Ȭ"=>"\u{004f}\u{0303}\u{0304}", "ȭ"=>"\u{006f}\u{0303}\u{0304}",
			"Ȯ"=>"\u{004f}\u{0307}", "ȯ"=>"\u{006f}\u{0307}", "Ȱ"=>"\u{004f}\u{0307}\u{0304}", "ȱ"=>"\u{006f}\u{0307}\u{0304}", "Ȳ"=>"\u{0059}\u{0304}", "ȳ"=>"\u{0079}\u{0304}",
		);
		// phpcs:enable
	}
	return $map;
}

/**
 * Jiné tvary téhož názvu (rozložený a složený).
 *
 * @param string $name Název souboru.
 * @return string[]
 */
function ekc_filenames_variants( string $name ): array {
	$variants = array(
		strtr( $name, ekc_filenames_nfd_map() ),
		strtr( $name, array_flip( ekc_filenames_nfd_map() ) ),
	);
	if ( class_exists( 'Normalizer' ) ) {
		$variants[] = (string) Normalizer::normalize( $name, Normalizer::FORM_D );
		$variants[] = (string) Normalizer::normalize( $name, Normalizer::FORM_C );
	}
	return array_values( array_diff( array_unique( array_filter( $variants ) ), array( $name ) ) );
}

/**
 * Existující soubor, který se od zadané cesty liší jen tvarem diakritiky.
 *
 * @param string $path Očekávaná cesta.
 * @return string Nalezená cesta, '' = není.
 */
function ekc_filenames_find_variant( string $path ): string {
	if ( is_file( $path ) || ! preg_match( '/[^\x20-\x7e]/', basename( $path ) ) ) {
		return '';
	}
	foreach ( ekc_filenames_variants( basename( $path ) ) as $variant ) {
		$candidate = dirname( $path ) . '/' . $variant;
		if ( is_file( $candidate ) ) {
			return $candidate;
		}
	}
	return '';
}

/**
 * Přejmenuje soubor s jiným tvarem diakritiky na očekávaný název.
 *
 * @param string $path Očekávaná cesta (tvar z databáze).
 * @return bool Soubor teď existuje pod očekávaným názvem.
 */
function ekc_filenames_fix_form( string $path ): bool {
	if ( is_file( $path ) ) {
		return true;
	}
	$found = ekc_filenames_find_variant( $path );
	return '' !== $found && @rename( $found, $path ) && is_file( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
}

/**
 * Soubory příloh (všech typů), které na disku chybí, ale existují s jiným tvarem diakritiky.
 *
 * @return array<int,array{0:int,1:string,2:string}> [ID, cesta podle databáze, cesta na disku]
 */
function ekc_filenames_issues(): array {
	global $wpdb;
	$base   = trailingslashit( (string) wp_get_upload_dir()['basedir'] );
	$issues = array();
	$last   = 0;
	do {
		$rows = (array) $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('_wp_attached_file','_wp_attachment_metadata') AND post_id > %d AND post_id <= %d ORDER BY post_id",
				$last,
				$last + 1000
			)
		);
		$more  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MIN(post_id) FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND post_id > %d", $last + 1000 ) );
		$files = array();
		foreach ( $rows as $row ) {
			if ( '_wp_attached_file' === $row->meta_key ) {
				$files[ (int) $row->post_id ][] = (string) $row->meta_value;
				continue;
			}
			$meta = maybe_unserialize( $row->meta_value );
			if ( ! is_array( $meta ) || empty( $meta['file'] ) ) {
				continue;
			}
			$dir = dirname( (string) $meta['file'] );
			$dir = '.' === $dir ? '' : $dir . '/';
			foreach ( is_array( $meta['sizes'] ?? null ) ? $meta['sizes'] : array() as $size ) {
				if ( is_array( $size ) && ! empty( $size['file'] ) ) {
					$files[ (int) $row->post_id ][] = $dir . $size['file'];
				}
			}
			if ( ! empty( $meta['original_image'] ) ) {
				$files[ (int) $row->post_id ][] = $dir . $meta['original_image'];
			}
		}
		foreach ( $files as $id => $list ) {
			foreach ( array_unique( $list ) as $rel ) {
				if ( ! preg_match( '/[^\x20-\x7e]/', $rel ) ) {
					continue;
				}
				$found = ekc_filenames_find_variant( $base . $rel );
				if ( '' !== $found ) {
					$issues[] = array( (int) $id, $base . $rel, $found );
				}
			}
		}
		$last = $more ? $more - 1 : 0;
	} while ( $more );
	return $issues;
}

/**
 * Úloha Údržby: přejmenovat soubory na tvar z databáze (seznam se zjistí znovu na serveru).
 *
 * @return array
 */
function ekc_filenames_task_fix(): array {
	$done   = 0;
	$failed = 0;
	foreach ( ekc_filenames_issues() as $issue ) {
		if ( ! is_file( $issue[1] ) && @rename( $issue[2], $issue[1] ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
			++$done;
			delete_post_meta( $issue[0], '_ekc_webp_fail' ); // převod na WebP mohl selhat kvůli „chybějícímu“ souboru
		} else {
			++$failed;
		}
	}
	return array(
		'message' => sprintf( 'Přejmenováno: %s. Soubory s diakritikou v názvu jsou zase dostupné.', ekc_plural( $done, '%d soubor', '%d soubory', '%d souborů' ) ) . ( $failed ? sprintf( ' Nepodařilo se: %d.', $failed ) : '' ),
		'type'    => $failed ? 'warning' : 'success',
	);
}

/**
 * Sekce Údržby: Názvy souborů.
 *
 * @param string $id ID sekce.
 */
function ekc_filenames_render( string $id ): void {
	echo '<p>Soubory s diakritikou v názvu, které web nenajde (adresa vrací 404), protože je mají na disku v jiném tvaru Unicode než v databázi (typicky nahrání z Macu). Oprava je přejmenuje – obsah ani odkazy se nemění.</p>';
	if ( ! ekc_maintenance_showing( 'filenames' ) ) {
		ekc_maintenance_show_link( $id, 'filenames', 'Zkontrolovat názvy souborů' );
		return;
	}
	$issues = ekc_filenames_issues();
	if ( ! $issues ) {
		echo '<p>' . ekc_status_dot( 'ok' ) . ' Všechny názvy souborů odpovídají databázi.</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
		return;
	}
	$base = trailingslashit( (string) wp_get_upload_dir()['basedir'] );
	echo '<details style="margin-bottom:1em"><summary>' . esc_html( sprintf( 'Souborů s jiným tvarem názvu: %d', count( $issues ) ) ) . '</summary><ul style="list-style:disc;margin-left:20px">';
	foreach ( array_slice( $issues, 0, 200 ) as $issue ) {
		echo '<li><code>' . esc_html( str_replace( $base, '', $issue[1] ) ) . '</code></li>';
	}
	echo '</ul></details>';
	ekc_maintenance_button( $id, 'fix', 'Opravit názvy souborů', sprintf( 'Přejmenovat %d souborů na tvar názvu z databáze?', count( $issues ) ), true );
}

ekc_maintenance_section(
	'filenames',
	array(
		'title'  => 'Názvy souborů s diakritikou',
		'order'  => 12,
		'render' => 'ekc_filenames_render',
		'tasks'  => array(
			'fix' => 'ekc_filenames_task_fix',
		),
	)
);
