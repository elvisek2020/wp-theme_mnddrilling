<?php
/**
 * Formulář hledání v hlavičce.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="mnd-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="mnd-search-input"><?php echo esc_html( mnd_t( 'Hledat', 'Search' ) ); ?></label>
	<input type="search" id="mnd-search-input" class="mnd-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>">
	<button type="submit" class="mnd-search__button" aria-label="<?php echo esc_attr( mnd_t( 'Hledat', 'Search' ) ); ?>"><?php echo mnd_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</form>
