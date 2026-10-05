<?php
/**
 * Hlavička: logo, název webu, hledání, přepínač jazyků; na titulce slider.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text mnd-skip" href="#main"><?php echo esc_html( mnd_t( 'Přejít na obsah', 'Skip to content' ) ); ?></a>

<header class="mnd-header">
	<div class="mnd-inner mnd-header__inner">
		<?php mnd_logo( 'mnd-header__logo' ); ?>
		<?php $mnd_name_tag = is_front_page() ? 'h1' : 'p'; // na titulce je název webu hlavním nadpisem ?>
		<<?php echo $mnd_name_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="mnd-header__name"><?php echo mnd_site_name_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- escapováno ve funkci ?></<?php echo $mnd_name_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>

		<div class="mnd-header__tools" id="mnd-tools">
			<?php $mnd_languages = mnd_languages(); ?>
			<?php if ( $mnd_languages ) : ?>
				<ul class="mnd-lang" aria-label="<?php echo esc_attr( mnd_t( 'Jazyk', 'Language' ) ); ?>">
					<?php foreach ( $mnd_languages as $mnd_lang ) : ?>
						<?php if ( ! empty( $mnd_lang['current_lang'] ) ) : ?>
							<li class="mnd-lang__item is-active"><span lang="<?php echo esc_attr( $mnd_lang['slug'] ); ?>"><?php echo esc_html( $mnd_lang['slug'] ); ?></span></li>
						<?php else : ?>
							<li class="mnd-lang__item"><a href="<?php echo esc_url( $mnd_lang['url'] ); ?>" hreflang="<?php echo esc_attr( $mnd_lang['slug'] ); ?>" lang="<?php echo esc_attr( $mnd_lang['slug'] ); ?>" title="<?php echo esc_attr( $mnd_lang['name'] ); ?>"><?php echo esc_html( $mnd_lang['slug'] ); ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php get_search_form(); ?>
		</div>

		<button type="button" class="mnd-nav-toggle" aria-controls="mnd-tools" aria-expanded="false">
			<span class="screen-reader-text"><?php echo esc_html( mnd_t( 'Hledání a jazyk', 'Search and language' ) ); ?></span>
			<i></i><i></i><i></i>
		</button>
	</div>
</header>

<?php
if ( is_front_page() ) {
	get_template_part( 'template-parts/slider' );
}
?>

<main id="main" class="mnd-main">
