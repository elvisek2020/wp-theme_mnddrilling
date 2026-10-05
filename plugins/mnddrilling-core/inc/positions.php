<?php
/**
 * Volné pozice: kategorie s kontaktními e-maily a výběr kategorie u pozice
 * (dřív „Ultimate Theme Settings“ a meta box ve staré šabloně).
 *
 * Data zůstávají ve stejných volbách (email_count, email_N, category_N) a v meta
 * „position_category“ (e-mail kategorie), takže fungují se starou i novou šablonou.
 * Dokud je stará šablona aktivní, používá se její stránka nastavení i meta box.
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Řádky nastavení: [[název, e-mail], …] v uloženém pořadí.
 *
 * @return array
 */
function mnd_core_position_category_rows() {
	$rows  = array();
	$count = (int) get_option( 'email_count' );
	for ( $i = 0; $i < $count; $i++ ) {
		$name = (string) get_option( 'category_' . $i );
		if ( '' !== $name ) {
			$rows[] = array( $name, (string) get_option( 'email_' . $i ) );
		}
	}
	return $rows;
}

/**
 * Kategorie pozic: e-mail => název (u pozice se ukládá e-mail kategorie).
 *
 * @return array
 */
function mnd_core_position_categories() {
	$cats = array();
	foreach ( mnd_core_position_category_rows() as $row ) {
		$cats[ $row[1] ] = $row[0];
	}
	return $cats;
}

/**
 * Název kategorie volné pozice.
 *
 * @param int $post_id ID pozice.
 * @return string
 */
function mnd_core_position_category_name( $post_id ) {
	$cats = mnd_core_position_categories();
	$key  = (string) get_post_meta( $post_id, 'position_category', true );
	return isset( $cats[ $key ] ) ? $cats[ $key ] : '';
}

/**
 * Běží ještě stará šablona se svou stránkou nastavení?
 *
 * @return bool
 */
function mnd_core_positions_legacy_theme() {
	return function_exists( 'ultimate_settings_page' );
}

/**
 * Stránka Volné pozice → Kategorie a e-maily.
 */
function mnd_core_positions_menu() {
	if ( mnd_core_positions_legacy_theme() || ! post_type_exists( 'position' ) ) {
		return;
	}
	add_submenu_page( 'edit.php?post_type=position', __( 'Kategorie a e-maily', 'mnddrilling-core' ), __( 'Kategorie a e-maily', 'mnddrilling-core' ), 'manage_options', 'mnd-position-categories', 'mnd_core_positions_page' );
}
add_action( 'admin_menu', 'mnd_core_positions_menu' );

/**
 * Uložení kategorií (formulář na stránce níže).
 */
function mnd_core_positions_save() {
	if ( ! isset( $_POST['mnd_positions_nonce'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'mnd_positions', 'mnd_positions_nonce' );

	$names  = isset( $_POST['mnd_cat_name'] ) ? (array) wp_unslash( $_POST['mnd_cat_name'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- očištěno níže
	$emails = isset( $_POST['mnd_cat_email'] ) ? (array) wp_unslash( $_POST['mnd_cat_email'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- očištěno níže
	$rows   = array();
	foreach ( $names as $i => $name ) {
		$name  = sanitize_text_field( $name );
		$email = isset( $emails[ $i ] ) ? implode( ', ', array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', (string) $emails[ $i ] ) ) ) ) ) : '';
		if ( '' !== $name ) {
			$rows[] = array( $name, $email );
		}
	}

	$old = (int) get_option( 'email_count' );
	foreach ( $rows as $i => $row ) {
		update_option( 'category_' . $i, $row[0] );
		update_option( 'email_' . $i, $row[1] );
	}
	for ( $i = count( $rows ); $i < $old; $i++ ) {
		delete_option( 'category_' . $i );
		delete_option( 'email_' . $i );
	}
	update_option( 'email_count', count( $rows ) );

	wp_safe_redirect( add_query_arg( 'updated', '1', menu_page_url( 'mnd-position-categories', false ) ) );
	exit;
}
add_action( 'admin_init', 'mnd_core_positions_save' );

/**
 * Obsah stránky kategorií.
 */
function mnd_core_positions_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$rows   = mnd_core_position_category_rows();
	$rows[] = array( '', '' ); // prázdný řádek pro novou kategorii
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Kategorie volných pozic', 'mnddrilling-core' ); ?></h1>
		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Uloženo.', 'mnddrilling-core' ); ?></p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Kategorie se vybírá u každé pozice a zobrazuje se ve výpisu volných pozic. Více e-mailů oddělte čárkou. Kategorii odeberete smazáním názvu.', 'mnddrilling-core' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'mnd_positions', 'mnd_positions_nonce' ); ?>
			<table class="widefat striped" style="max-width:860px">
				<thead><tr><th><?php esc_html_e( 'Kategorie', 'mnddrilling-core' ); ?></th><th><?php esc_html_e( 'Kontaktní e-mail', 'mnddrilling-core' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><input type="text" class="regular-text" name="mnd_cat_name[]" value="<?php echo esc_attr( $row[0] ); ?>"></td>
							<td><input type="text" class="large-text" name="mnd_cat_email[]" value="<?php echo esc_attr( $row[1] ); ?>"></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Meta box Kategorie u volné pozice.
 */
function mnd_core_positions_meta_box() {
	if ( mnd_core_positions_legacy_theme() ) {
		return;
	}
	add_meta_box( 'mnd-position-category', __( 'Kategorie pozice', 'mnddrilling-core' ), 'mnd_core_positions_meta_box_html', 'position', 'side', 'default' );
}
add_action( 'add_meta_boxes_position', 'mnd_core_positions_meta_box' );

/**
 * Obsah meta boxu.
 *
 * @param WP_Post $post Pozice.
 */
function mnd_core_positions_meta_box_html( $post ) {
	wp_nonce_field( 'mnd_position_category', 'mnd_position_category_nonce' );
	$current = (string) get_post_meta( $post->ID, 'position_category', true );
	echo '<select name="mnd_position_category" class="widefat"><option value="">' . esc_html__( '— Bez kategorie —', 'mnddrilling-core' ) . '</option>';
	foreach ( mnd_core_position_categories() as $email => $name ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $email ), selected( $current, $email, false ), esc_html( $name ) );
	}
	echo '</select>';
	if ( current_user_can( 'manage_options' ) ) {
		printf( '<p><a href="%s">%s</a></p>', esc_url( menu_page_url( 'mnd-position-categories', false ) ), esc_html__( 'Upravit kategorie', 'mnddrilling-core' ) );
	}
}

/**
 * Uložení kategorie pozice (hodnota = e-mail kategorie, jako ve staré šabloně).
 *
 * @param int $post_id ID pozice.
 */
function mnd_core_positions_save_meta( $post_id ) {
	if ( ! isset( $_POST['mnd_position_category_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mnd_position_category_nonce'] ) ), 'mnd_position_category' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$value = isset( $_POST['mnd_position_category'] ) ? sanitize_text_field( wp_unslash( $_POST['mnd_position_category'] ) ) : '';
	if ( '' === $value || ! array_key_exists( $value, mnd_core_position_categories() ) ) {
		delete_post_meta( $post_id, 'position_category' );
	} else {
		update_post_meta( $post_id, 'position_category', $value );
	}
}
add_action( 'save_post_position', 'mnd_core_positions_save_meta' );
