<?php
/**
 * Pomocné funkce pro šablony: texty, ikony, podmenu sekce, dokumenty, stránkování.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Je aktuální jazyk angličtina? (Polylang; bez něj podle jazyka webu.)
 *
 * @return bool
 */
function mnd_is_en() {
	$lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : substr( get_locale(), 0, 2 );
	return 'en' === $lang;
}

/**
 * Pevný text šablony v češtině nebo angličtině podle jazyka stránky.
 *
 * @param string $cs Česky.
 * @param string $en Anglicky.
 * @return string
 */
function mnd_t( $cs, $en ) {
	return mnd_is_en() ? $en : $cs;
}

/**
 * Název obsahu jako čistý text (s typografií a pevnými mezerami, bez HTML entit).
 * Escapuje se až při výpisu (esc_html / esc_attr).
 *
 * @param int|WP_Post|null $post Obsah (výchozí aktuální).
 * @return string
 */
function mnd_title_text( $post = null ) {
	return trim( html_entity_decode( wp_strip_all_tags( get_the_title( $post ) ), ENT_QUOTES, 'UTF-8' ) );
}

/**
 * Inline SVG ikona (stroke/fill = currentColor).
 *
 * @param string $name Název ikony.
 * @param int    $size Velikost v px.
 * @return string
 */
function mnd_icon( $name, $size = 16 ) {
	$paths = array(
		'search'   => '<circle cx="7.5" cy="7.5" r="5.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M11.6 11.6 17 17" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"/>',
		'linkedin' => '<path fill="currentColor" d="M2.6 6.1h2.9V15H2.6zM4 1.6a1.7 1.7 0 1 1 0 3.4 1.7 1.7 0 0 1 0-3.4M7.3 6.1h2.8v1.2c.4-.7 1.3-1.4 2.7-1.4 2.9 0 3.4 1.9 3.4 4.3V15h-2.9v-4.3c0-1 0-2.4-1.5-2.4s-1.7 1.1-1.7 2.3V15H7.3z"/>',
		'youtube'  => '<path fill="currentColor" fill-rule="evenodd" d="M3.4 3.6h11.2c1.2 0 2 .9 2 2v6.8c0 1.1-.8 2-2 2H3.4c-1.2 0-2-.9-2-2V5.6c0-1.1.8-2 2-2M7.3 6.3v5.4L11.8 9z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf( '<svg class="mnd-icon mnd-icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 18 18" aria-hidden="true" focusable="false">%3$s</svg>', esc_attr( $name ), (int) $size, $paths[ $name ] );
}

/**
 * Logo webu (soubor v šabloně – stejné jako původní logo v knihovně médií).
 *
 * @param string $class Třída odkazu.
 */
function mnd_logo( $class = 'mnd-logo' ) {
	$name = get_bloginfo( 'name' );
	printf(
		'<a href="%1$s" class="%2$s" rel="home"><img src="%3$s" width="150" height="44" alt="%4$s"></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $class ),
		esc_url( MND_URI . '/assets/img/logo.png' ),
		esc_attr( $name )
	);
}

/**
 * Název webu s prvním slovem tučně („<strong>MND</strong> Drilling & Services“).
 *
 * @return string HTML.
 */
function mnd_site_name_html() {
	$name  = esc_html( html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ) );
	$parts = explode( ' ', $name, 2 );
	return '<strong>' . $parts[0] . '</strong>' . ( isset( $parts[1] ) ? ' ' . $parts[1] : '' );
}

/**
 * ID stránky v aktuálním jazyce podle české cesty (např. „kariera/volne-pozice“).
 *
 * @param string $cs_path Cesta české stránky.
 * @return int 0 = neexistuje.
 */
function mnd_page_id_by_cs_path( $cs_path ) {
	static $cache = array();
	if ( isset( $cache[ $cs_path ] ) ) {
		return $cache[ $cs_path ];
	}
	$page = get_page_by_path( $cs_path );
	$id   = $page ? (int) $page->ID : 0;
	if ( $id && function_exists( 'pll_get_post' ) ) {
		$id = (int) pll_get_post( $id );
	}
	$cache[ $cs_path ] = $id;
	return $id;
}

/**
 * Stránka „Zájem o pozici“ (formulář) v aktuálním jazyce – v podmenu se neukazuje.
 *
 * @return int
 */
function mnd_career_form_page_id() {
	return mnd_page_id_by_cs_path( 'kariera/volne-pozice/zajem-o-pozici' );
}

/**
 * Hlavní stránka sekce (divize) pro podmenu – nejvyšší předek stránky.
 *
 * @param int $page_id ID stránky.
 * @return int
 */
function mnd_section_root( $page_id ) {
	$ancestors = get_post_ancestors( $page_id );
	return $ancestors ? (int) end( $ancestors ) : (int) $page_id;
}

/**
 * Podstránky (zveřejněné, podle pořadí).
 *
 * @param int $parent_id ID rodiče.
 * @return WP_Post[]
 */
function mnd_child_pages( $parent_id ) {
	return get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post_parent'    => (int) $parent_id,
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
}

/**
 * Podmenu sekce vlevo (a výběr na mobilu): podstránky divize a jejich podstránky.
 *
 * @param int $current_id Aktivní stránka.
 * @param int $root_id    Hlavní stránka sekce.
 */
function mnd_sub_nav( $current_id, $root_id ) {
	$children = mnd_child_pages( $root_id );
	if ( ! $children ) {
		return;
	}
	$hide = mnd_career_form_page_id();
	?>
	<nav class="mnd-subnav" aria-label="<?php echo esc_attr( mnd_title_text( $root_id ) ); ?>">
		<h2 class="mnd-subnav__title"><?php echo esc_html( mnd_title_text( $root_id ) ); ?></h2>
		<ul class="mnd-subnav__list">
			<?php foreach ( $children as $child ) : ?>
				<li class="mnd-subnav__item<?php echo $child->ID === $current_id ? ' is-active' : ''; ?>">
					<a href="<?php echo esc_url( get_permalink( $child ) ); ?>"<?php echo $child->ID === $current_id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( mnd_title_text( $child ) ); ?></a>
					<?php
					$grandchildren = array_filter(
						mnd_child_pages( $child->ID ),
						function ( $page ) use ( $hide ) {
							return $page->ID !== $hide;
						}
					);
					?>
					<?php if ( $grandchildren ) : ?>
						<ul class="mnd-subnav__sub">
							<?php foreach ( $grandchildren as $grandchild ) : ?>
								<li<?php echo $grandchild->ID === $current_id ? ' class="is-active"' : ''; ?>><a href="<?php echo esc_url( get_permalink( $grandchild ) ); ?>"><?php echo esc_html( mnd_title_text( $grandchild ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<div class="mnd-subnav-mobile">
		<label class="screen-reader-text" for="mnd-subnav-select"><?php echo esc_html( mnd_title_text( $root_id ) ); ?></label>
		<select id="mnd-subnav-select" class="mnd-select" data-mnd-navigate>
			<?php foreach ( $children as $child ) : ?>
				<option value="<?php echo esc_url( get_permalink( $child ) ); ?>"<?php selected( $child->ID, $current_id ); ?>><?php echo esc_html( mnd_title_text( $child ) ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
}

/**
 * Nadpis stránky a úvodní obrázek.
 *
 * @param WP_Post $page Stránka.
 */
function mnd_page_header( $page ) {
	?>
	<header class="mnd-page__header">
		<h1 class="mnd-page__title"><?php echo esc_html( mnd_title_text( $page ) ); ?></h1>
	</header>
	<?php if ( has_post_thumbnail( $page ) ) : ?>
		<div class="mnd-page__image"><?php echo get_the_post_thumbnail( $page, 'page-image' ); ?></div>
	<?php endif; ?>
	<?php
}

/**
 * Dokumenty ke stažení pod textem stránky (pole z pluginu MND Drilling Core).
 *
 * @param int $page_id ID stránky.
 */
function mnd_page_documents( $page_id ) {
	if ( ! function_exists( 'mnd_core_page_documents' ) ) {
		return;
	}
	$docs = mnd_core_page_documents( $page_id );
	if ( ! $docs ) {
		return;
	}
	echo '<div class="mnd-documents">';
	foreach ( $docs as $doc ) {
		$url = mnd_core_document_url( $doc->ID );
		if ( ! $url ) {
			continue;
		}
		printf(
			'<a href="%1$s" class="mnd-btn mnd-btn--doc">%2$s %3$s</a>',
			esc_url( $url ),
			esc_html( mnd_pll( 'Stáhnout' ) ),
			esc_html( mnd_title_text( $doc ) )
		);
	}
	echo '</div>';
}

/**
 * Odkazy pro přihlášené redaktory (upravit, přidat stránku) – jako v původní šabloně.
 *
 * @param int    $post_id ID obsahu.
 * @param string $new_type Typ pro odkaz „Přidat…“ (prázdné = bez odkazu).
 */
function mnd_edit_links( $post_id, $new_type = 'page' ) {
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	echo '<p class="mnd-edit-links">';
	edit_post_link( mnd_pll( 'Upravit' ), '<span class="mnd-edit-link">', '</span>', $post_id );
	if ( $new_type ) {
		printf(
			' <a href="%1$s" class="mnd-new-link">%2$s</a>',
			esc_url( admin_url( 'post-new.php?post_type=' . $new_type ) ),
			esc_html( 'position' === $new_type ? mnd_t( 'Přidat novou pozici', 'Add new position' ) : mnd_t( 'Přidat novou stránku', 'Add new page' ) )
		);
	}
	echo '</p>';
}

/**
 * Stránkování výpisu (Začátek – Předchozí – čísla – Další – Konec, „Strana X z Y“).
 *
 * @param WP_Query $query Dotaz.
 * @param int      $paged Aktuální strana.
 */
function mnd_pagination( $query, $paged ) {
	$max = (int) $query->max_num_pages;
	if ( $max < 2 ) {
		return;
	}
	$item = function ( $label, $page, $class, $enabled ) {
		$inner = $enabled
			? sprintf( '<a href="%1$s" class="mnd-pagination__link">%2$s</a>', esc_url( get_pagenum_link( $page ) ), esc_html( $label ) )
			: sprintf( '<span class="mnd-pagination__link">%s</span>', esc_html( $label ) );
		return sprintf( '<li class="%1$s">%2$s</li>', esc_attr( $class ), $inner );
	};
	echo '<nav class="mnd-pagination" aria-label="' . esc_attr( mnd_t( 'Stránkování', 'Pagination' ) ) . '"><ul>';
	echo $item( mnd_pll( 'Začátek' ), 1, 'mnd-pagination__edge', $paged > 1 ); // phpcs:ignore WordPress.Security.EscapeOutput -- escapováno v $item
	echo $item( mnd_pll( 'Předchozí' ), $paged - 1, 'mnd-pagination__edge', $paged > 1 ); // phpcs:ignore WordPress.Security.EscapeOutput
	for ( $i = 1; $i <= $max; $i++ ) {
		if ( $i === $paged ) {
			printf( '<li class="is-current"><span class="mnd-pagination__link" aria-current="page">%d</span></li>', (int) $i );
		} else {
			echo $item( (string) $i, $i, '', true ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
	echo $item( mnd_pll( 'Další' ), $paged + 1, 'mnd-pagination__edge', $paged < $max ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo $item( mnd_pll( 'Konec' ), $max, 'mnd-pagination__edge', $paged < $max ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</ul>';
	printf( '<p class="mnd-pagination__info">%1$s %2$d %3$s %4$d</p>', esc_html( mnd_pll( 'Strana' ) ), (int) $paged, esc_html( mnd_pll( 'z' ) ), (int) $max );
	echo '</nav>';
}
