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
			Member of KKCG Group | Copyright © <?php echo esc_html( wp_date( 'Y' ) ); ?> MND Drilling &amp; Services a.s.<?php do_action( 'mnd_footer_info' ); ?>
		</p>
		<ul class="mnd-social">
			<li><a href="https://www.youtube.com/@mnddrillingservices7650/videos" class="mnd-social__link mnd-social__link--yt" target="_blank" rel="noopener" title="MND Drilling &amp; Services na YouTube"><?php echo mnd_icon( 'youtube', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text">YouTube</span></a></li>
			<li><a href="https://www.linkedin.com/company/mnd-drilling-and-services" class="mnd-social__link mnd-social__link--li" target="_blank" rel="noopener" title="MND Drilling &amp; Services na LinkedIn"><?php echo mnd_icon( 'linkedin', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text">LinkedIn</span></a></li>
		</ul>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
