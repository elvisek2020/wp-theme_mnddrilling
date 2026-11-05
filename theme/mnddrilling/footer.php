<?php
/**
 * Patička: menu, adresa a kontakty (widgety), Compliance Hotline; spodní lišta s logem a sítěmi.
 *
 * @package MNDDrilling
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<div class="mnd-footer">
	<div class="mnd-inner mnd-footer__cols">
		<div class="mnd-footer__col">
			<h2 class="mnd-footer__title"><?php echo esc_html( mnd_pll( 'Mohlo by Vás zajímat' ) ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-menu',
					'container'      => false,
					'menu_class'     => 'mnd-footer__menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
		<div class="mnd-footer__col">
			<h2 class="mnd-footer__title"><?php echo esc_html( mnd_pll( 'Sídlo firmy' ) ); ?></h2>
			<?php dynamic_sidebar( 'homepage_footer_1' ); ?>
		</div>
		<div class="mnd-footer__col">
			<h2 class="mnd-footer__title"><?php echo esc_html( mnd_pll( 'Kontakty' ) ); ?></h2>
			<?php dynamic_sidebar( 'homepage_footer_2' ); ?>
		</div>
		<div class="mnd-footer__col mnd-footer__col--hotline">
			<span class="mnd-btn mnd-btn--hotline"><?php echo esc_html( mnd_pll( 'Compliance Hotline' ) ); ?></span>
			<a href="mailto:compliance.mndds@mnd.cz">compliance.mndds@mnd.cz</a>
		</div>
	</div>
</div>

<footer class="mnd-bottom">
	<div class="mnd-inner mnd-bottom__inner">
		<?php mnd_logo( 'mnd-bottom__logo' ); ?>
		<p class="mnd-bottom__text">
			Member of KKCG Group | Copyright © <?php echo esc_html( wp_date( 'Y' ) ); ?> MND Drilling &amp; Services a.s.<?php
			$mnd_consent = function_exists( 'ekt_consent_link' ) ? ekt_consent_link() : '';
			if ( '' !== $mnd_consent ) {
				echo '<span class="mnd-footer__consent"> | ' . $mnd_consent . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escapováno v ekt_consent_link()
			}
			?>
		</p>
		<?php echo function_exists( 'ekt_social_links' ) ? ekt_social_links() : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- markup v template-parts/ekt/social.php ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
