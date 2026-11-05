<?php
/**
 * Nastavení kanonických modulů pro web MND Drilling (filtry, kanon se neupravuje).
 *
 *  - Sociální sítě: výchozí odkazy YouTube a LinkedIn z původní patičky (změna v Přizpůsobit →
 *    MND Drilling → Sociální sítě; prázdná hodnota ikonu skryje).
 *  - Cookie lišta: texty z dřívější verze šablony v češtině i angličtině.
 *  - SEO: logo vydavatele v JSON-LD (logo je v šabloně, ne v Přizpůsobit → Identita webu);
 *    popis titulky a stránek bez textu z popisu firmy na stránce „O nás“ v jazyce stránky
 *    (jako do 0.4.x – titulka je jen výpis divizí, popis webu v Nastavení → Obecné je prázdný).
 *  - Prohlížeč obrázků: texty v angličtině na anglickém webu.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Výchozí hodnoty Přizpůsobit (filtr ekt_defaults).
 *
 * @param array $defaults Výchozí hodnoty (klíč bez prefixu ekt_).
 * @return array
 */
function mnd_site_defaults( array $defaults ): array {
	return array_merge(
		$defaults,
		array(
			'social_youtube'  => 'https://www.youtube.com/@mnddrillingservices7650/videos',
			'social_linkedin' => 'https://www.linkedin.com/company/mnd-drilling-and-services',
		)
	);
}
add_filter( 'ekt_defaults', 'mnd_site_defaults' );

/**
 * Texty cookie lišty (filtr ekt_consent_texts).
 *
 * @param array $texts Texty.
 * @return array
 */
function mnd_site_consent_texts( array $texts ): array {
	return array_merge(
		$texts,
		array(
			'label'    => ekt_t( 'Souhlas s cookies', 'Cookie consent' ),
			'text'     => ekt_t( 'Pomocí Google Analytics měříme, jak návštěvníci web používají. Analytické cookies ukládáme jen s vaším souhlasem.', 'We use Google Analytics to measure how visitors use this website. Analytics cookies are stored only with your consent.' ),
			'privacy'  => ekt_t( 'Zásady ochrany osobních údajů', 'Privacy policy' ),
			'decline'  => ekt_t( 'Odmítnout', 'Decline' ),
			'accept'   => ekt_t( 'Přijmout', 'Accept' ),
			'settings' => ekt_t( 'Nastavení cookies', 'Cookie settings' ),
		)
	);
}
add_filter( 'ekt_consent_texts', 'mnd_site_consent_texts' );

/**
 * Logo vydavatele v JSON-LD (filtr ekt_seo_publisher).
 *
 * @param array $entity Organization.
 * @return array
 */
function mnd_site_seo_publisher( array $entity ): array {
	if ( empty( $entity['logo'] ) ) {
		$entity['logo'] = array(
			'@type'  => 'ImageObject',
			'url'    => EKT_URI . '/assets/img/logo.png',
			'width'  => 150,
			'height' => 44,
		);
	}
	return $entity;
}
add_filter( 'ekt_seo_publisher', 'mnd_site_seo_publisher' );

/**
 * Texty prohlížeče obrázků v angličtině na anglickém webu (filtr ekt_lightbox_texts).
 *
 * @param array $texts Texty.
 * @return array
 */
function mnd_site_lightbox_texts( array $texts ): array {
	if ( ! mnd_is_en() ) {
		return $texts;
	}
	return array_merge(
		$texts,
		array(
			'dialog' => 'Image preview',
			'close'  => 'Close',
			'prev'   => 'Previous image',
			'next'   => 'Next image',
			'zoom'   => 'Enlarge image',
		)
	);
}
add_filter( 'ekt_lightbox_texts', 'mnd_site_lightbox_texts' );

/**
 * Popis pro vyhledávače, když stránka žádný nemá (filtr ekt_seo_data).
 *
 * @param array $data Data modulu seo.
 * @return array
 */
function mnd_site_seo_data( array $data ): array {
	if ( '' !== (string) ( $data['description'] ?? '' ) || ! function_exists( 'ekt_seo_clean' ) ) {
		return $data;
	}
	$about = mnd_page_id_by_cs_path( 'o-spolecnosti/about-company' );
	if ( $about ) {
		$data['description'] = ekt_seo_clean( (string) get_post_field( 'post_content', $about ) );
	}
	return $data;
}
add_filter( 'ekt_seo_data', 'mnd_site_seo_data' );
