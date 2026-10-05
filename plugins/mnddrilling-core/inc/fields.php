<?php
/**
 * Pole obsahu (nahrazuje Advanced Custom Fields a dávno odebraný Simple Fields).
 *
 * Meta boxy ukládají do stejných meta klíčů jako ACF, takže data zůstávají a ACF jde
 * kdykoli zapnout zpět. Dokud je ACF aktivní, meta boxy se nezobrazují (pole ukazuje ACF).
 *
 *  - stránka:   doc            dokumenty ke stažení (pole ID dokumentů, pořadí platí)
 *  - stránka:   item_category  kategorie položek (šablona „podstránka s položkami“)
 *  - dokument:  file           soubor z knihovny médií (ID přílohy)
 *  - událost:   from, to       rozmezí let (historie)
 *  - oblast:    zeme           země na mapě referencí
 *
 * Jednorázově převede dokumenty stránek ze Simple Fields do pole „doc“ (data Simple Fields
 * zůstávají, uklidit je jde v Údržbě webu).
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

const MND_CORE_FIELDS_MIGRATED = 'mnd_core_fields_migrated';

/**
 * Země na mapě referencí (stejné hodnoty jako v ACF).
 *
 * @return array
 */
function mnd_core_countries() {
	return array(
		'germany'  => __( 'Německo', 'mnddrilling-core' ),
		'czech'    => __( 'Česká republika', 'mnddrilling-core' ),
		'poland'   => __( 'Polsko', 'mnddrilling-core' ),
		'slovakia' => __( 'Slovensko', 'mnddrilling-core' ),
		'ukraine'  => __( 'Ukrajina', 'mnddrilling-core' ),
		'romania'  => __( 'Rumunsko', 'mnddrilling-core' ),
		'austria'  => __( 'Rakousko', 'mnddrilling-core' ),
		'hungary'  => __( 'Maďarsko', 'mnddrilling-core' ),
		'serbia'   => __( 'Srbsko', 'mnddrilling-core' ),
	);
}

/* =========================================================================
 * Funkce pro šablony
 * ====================================================================== */

/**
 * Zveřejněné dokumenty ke stažení u stránky (v uloženém pořadí).
 *
 * @param int $post_id ID stránky.
 * @return WP_Post[]
 */
function mnd_core_page_documents( $post_id ) {
	$ids = get_post_meta( $post_id, 'doc', true );
	if ( ( empty( $ids ) || ! is_array( $ids ) ) && ! mnd_core_fields_migrated() ) {
		$ids = mnd_core_simple_fields_docs( $post_id );
	}
	$docs = array();
	foreach ( (array) $ids as $id ) {
		$doc = get_post( (int) $id );
		if ( $doc && 'document' === $doc->post_type && 'publish' === $doc->post_status ) {
			$docs[] = $doc;
		}
	}
	return $docs;
}

/**
 * Adresa souboru dokumentu.
 *
 * @param int $doc_id ID dokumentu.
 * @return string
 */
function mnd_core_document_url( $doc_id ) {
	$file = (int) get_post_meta( $doc_id, 'file', true );
	return $file ? (string) wp_get_attachment_url( $file ) : '';
}

/* =========================================================================
 * Převod ze Simple Fields (skupina 7 = dokumenty stránky)
 * ====================================================================== */

/**
 * Jsou dokumenty ze Simple Fields převedené?
 *
 * @return bool
 */
function mnd_core_fields_migrated() {
	return (bool) get_option( MND_CORE_FIELDS_MIGRATED );
}

/**
 * Dokumenty stránky ve starém formátu Simple Fields.
 *
 * @param int $post_id ID stránky.
 * @return array ID dokumentů.
 */
function mnd_core_simple_fields_docs( $post_id ) {
	$ids = array();
	for ( $i = 0; $i < 50; $i++ ) {
		$id = (string) get_post_meta( $post_id, '_simple_fields_fieldGroupID_7_fieldID_1_numInSet_' . $i, true );
		if ( '' === $id ) {
			break;
		}
		if ( (int) $id > 0 ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * Jednorázový převod: stránky bez pole „doc“ dostanou dokumenty ze Simple Fields.
 */
function mnd_core_fields_migrate() {
	if ( mnd_core_fields_migrated() ) {
		return;
	}
	global $wpdb;
	$pages = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT m.post_id FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE p.post_type = 'page' AND m.meta_key LIKE %s",
			$wpdb->esc_like( '_simple_fields_fieldGroupID_7_fieldID_1_numInSet_' ) . '%'
		)
	);
	foreach ( $pages as $page_id ) {
		$current = get_post_meta( $page_id, 'doc', true );
		$ids     = mnd_core_simple_fields_docs( $page_id );
		if ( ( empty( $current ) || ! is_array( $current ) ) && $ids ) {
			update_post_meta( $page_id, 'doc', $ids );
		}
	}
	update_option( MND_CORE_FIELDS_MIGRATED, time(), false );
}
add_action( 'admin_init', 'mnd_core_fields_migrate' );

/* =========================================================================
 * Meta boxy (jen když ACF není aktivní)
 * ====================================================================== */

/**
 * Registrace meta boxů.
 *
 * @param string  $post_type Typ obsahu.
 * @param WP_Post $post      Obsah.
 */
function mnd_core_fields_meta_boxes( $post_type, $post = null ) {
	if ( class_exists( 'ACF' ) ) {
		return;
	}
	switch ( $post_type ) {
		case 'page':
			add_meta_box( 'mnd-doc', __( 'Dokumenty ke stažení', 'mnddrilling-core' ), 'mnd_core_field_docs', 'page', 'normal', 'default' );
			if ( $post && 'page-child-with-items.php' === get_page_template_slug( $post ) ) {
				add_meta_box( 'mnd-item-category', __( 'Položky stránky', 'mnddrilling-core' ), 'mnd_core_field_item_category', 'page', 'side', 'default' );
			}
			break;
		case 'document':
			add_meta_box( 'mnd-file', __( 'Soubor', 'mnddrilling-core' ), 'mnd_core_field_file', 'document', 'normal', 'high' );
			break;
		case 'timelineitem':
			add_meta_box( 'mnd-range', __( 'Rozmezí', 'mnddrilling-core' ), 'mnd_core_field_range', 'timelineitem', 'side', 'high' );
			break;
		case 'mapitem':
			add_meta_box( 'mnd-zeme', __( 'Země', 'mnddrilling-core' ), 'mnd_core_field_country', 'mapitem', 'side', 'high' );
			break;
	}
}
add_action( 'add_meta_boxes', 'mnd_core_fields_meta_boxes', 10, 2 );

/**
 * Dokumenty ke stažení: seznam vybraných (pořadí šipkami) a výběr dalšího.
 *
 * @param WP_Post $post Stránka.
 */
function mnd_core_field_docs( $post ) {
	wp_nonce_field( 'mnd_core_fields', 'mnd_core_fields_nonce' );
	$selected = get_post_meta( $post->ID, 'doc', true );
	$selected = is_array( $selected ) ? array_map( 'intval', $selected ) : array();
	$all      = get_posts(
		array(
			'post_type'      => 'document',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'lang'           => '', // dokumenty všech jazyků (reference jsou společné)
		)
	);
	$titles   = array();
	foreach ( $all as $doc ) {
		$titles[ $doc->ID ] = get_the_title( $doc );
	}
	?>
	<input type="hidden" name="mnd_doc_present" value="1">
	<ul class="mnd-doc-list" style="margin:0 0 8px">
		<?php foreach ( $selected as $id ) : ?>
			<?php if ( isset( $titles[ $id ] ) ) : ?>
				<li style="display:flex;gap:6px;align-items:center;margin:0 0 4px">
					<input type="hidden" name="mnd_doc[]" value="<?php echo (int) $id; ?>">
					<span style="flex:1"><?php echo esc_html( $titles[ $id ] ); ?></span>
					<button type="button" class="button-link mnd-doc-up" aria-label="<?php esc_attr_e( 'Výš', 'mnddrilling-core' ); ?>">↑</button>
					<button type="button" class="button-link mnd-doc-down" aria-label="<?php esc_attr_e( 'Níž', 'mnddrilling-core' ); ?>">↓</button>
					<button type="button" class="button-link button-link-delete mnd-doc-remove"><?php esc_html_e( 'Odebrat', 'mnddrilling-core' ); ?></button>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
	<select class="mnd-doc-add" style="max-width:100%">
		<option value=""><?php esc_html_e( '— Přidat dokument —', 'mnddrilling-core' ); ?></option>
		<?php foreach ( $titles as $id => $title ) : ?>
			<option value="<?php echo (int) $id; ?>"><?php echo esc_html( $title ); ?></option>
		<?php endforeach; ?>
	</select>
	<p class="description"><?php esc_html_e( 'Odkazy ke stažení pod textem stránky. Dokumenty se spravují v menu Dokumenty.', 'mnddrilling-core' ); ?></p>
	<script>
	( function () {
		var box = document.getElementById( 'mnd-doc' );
		if ( ! box ) { return; }
		var list = box.querySelector( '.mnd-doc-list' );
		box.addEventListener( 'click', function ( e ) {
			var li = e.target.closest( 'li' );
			if ( ! li ) { return; }
			if ( e.target.classList.contains( 'mnd-doc-remove' ) ) { li.remove(); }
			if ( e.target.classList.contains( 'mnd-doc-up' ) && li.previousElementSibling ) { list.insertBefore( li, li.previousElementSibling ); }
			if ( e.target.classList.contains( 'mnd-doc-down' ) && li.nextElementSibling ) { list.insertBefore( li.nextElementSibling, li ); }
		} );
		box.querySelector( '.mnd-doc-add' ).addEventListener( 'change', function () {
			var opt = this.options[ this.selectedIndex ];
			if ( ! opt.value || list.querySelector( 'input[value="' + opt.value + '"]' ) ) { this.value = ''; return; }
			var tpl = document.createElement( 'li' );
			tpl.style.cssText = 'display:flex;gap:6px;align-items:center;margin:0 0 4px';
			tpl.innerHTML = '<input type="hidden" name="mnd_doc[]"><span style="flex:1"></span>'
				+ '<button type="button" class="button-link mnd-doc-up">↑</button>'
				+ '<button type="button" class="button-link mnd-doc-down">↓</button>'
				+ '<button type="button" class="button-link button-link-delete mnd-doc-remove"><?php echo esc_js( __( 'Odebrat', 'mnddrilling-core' ) ); ?></button>';
			tpl.querySelector( 'input' ).value = opt.value;
			tpl.querySelector( 'span' ).textContent = opt.text;
			list.appendChild( tpl );
			this.value = '';
		} );
	}() );
	</script>
	<?php
}

/**
 * Kategorie položek (kořen kategorie, jejíž položky stránka vypíše).
 *
 * @param WP_Post $post Stránka.
 */
function mnd_core_field_item_category( $post ) {
	wp_nonce_field( 'mnd_core_fields', 'mnd_core_fields_nonce' );
	wp_dropdown_categories(
		array(
			'taxonomy'          => 'itemcategory',
			'name'              => 'mnd_item_category',
			'selected'          => (int) get_post_meta( $post->ID, 'item_category', true ),
			'hierarchical'      => true,
			'hide_empty'        => false,
			'show_option_none'  => __( '— Vyberte —', 'mnddrilling-core' ),
			'option_none_value' => '',
			'lang'              => '',
		)
	);
	echo '<p class="description">' . esc_html__( 'Položky této kategorie (a jejích podkategorií) se vypíšou pod textem stránky.', 'mnddrilling-core' ) . '</p>';
}

/**
 * Soubor dokumentu z knihovny médií.
 *
 * @param WP_Post $post Dokument.
 */
function mnd_core_field_file( $post ) {
	wp_nonce_field( 'mnd_core_fields', 'mnd_core_fields_nonce' );
	wp_enqueue_media();
	$file = (int) get_post_meta( $post->ID, 'file', true );
	$url  = $file ? wp_get_attachment_url( $file ) : '';
	?>
	<input type="hidden" name="mnd_file" id="mnd-file-id" value="<?php echo $file ? (int) $file : ''; ?>">
	<p>
		<a id="mnd-file-name" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"<?php echo $url ? '' : ' hidden'; ?>><?php echo esc_html( $url ? wp_basename( $url ) : '' ); ?></a>
		<span id="mnd-file-empty"<?php echo $url ? ' hidden' : ''; ?>><?php esc_html_e( 'Žádný soubor.', 'mnddrilling-core' ); ?></span>
	</p>
	<p>
		<button type="button" class="button" id="mnd-file-pick"><?php esc_html_e( 'Vybrat soubor', 'mnddrilling-core' ); ?></button>
		<button type="button" class="button-link button-link-delete" id="mnd-file-clear"><?php esc_html_e( 'Odebrat', 'mnddrilling-core' ); ?></button>
	</p>
	<script>
	( function () {
		var frame, id = document.getElementById( 'mnd-file-id' ), name = document.getElementById( 'mnd-file-name' ), empty = document.getElementById( 'mnd-file-empty' );
		function show( url, filename ) {
			name.href = url || ''; name.textContent = filename || ''; name.hidden = ! url; empty.hidden = !! url;
		}
		document.getElementById( 'mnd-file-pick' ).addEventListener( 'click', function () {
			if ( ! frame ) {
				frame = wp.media( { title: '<?php echo esc_js( __( 'Soubor dokumentu', 'mnddrilling-core' ) ); ?>', multiple: false } );
				frame.on( 'select', function () {
					var a = frame.state().get( 'selection' ).first().toJSON();
					id.value = a.id; show( a.url, a.filename );
				} );
			}
			frame.open();
		} );
		document.getElementById( 'mnd-file-clear' ).addEventListener( 'click', function () { id.value = ''; show( '', '' ); } );
	}() );
	</script>
	<?php
}

/**
 * Rozmezí let u události v historii.
 *
 * @param WP_Post $post Událost.
 */
function mnd_core_field_range( $post ) {
	wp_nonce_field( 'mnd_core_fields', 'mnd_core_fields_nonce' );
	?>
	<p><label><?php esc_html_e( 'Od', 'mnddrilling-core' ); ?><br><input type="text" name="mnd_from" value="<?php echo esc_attr( get_post_meta( $post->ID, 'from', true ) ); ?>" class="widefat"></label></p>
	<p><label><?php esc_html_e( 'Do', 'mnddrilling-core' ); ?><br><input type="text" name="mnd_to" value="<?php echo esc_attr( get_post_meta( $post->ID, 'to', true ) ); ?>" class="widefat"></label></p>
	<?php
}

/**
 * Země oblasti na mapě referencí.
 *
 * @param WP_Post $post Oblast.
 */
function mnd_core_field_country( $post ) {
	wp_nonce_field( 'mnd_core_fields', 'mnd_core_fields_nonce' );
	$value = get_post_meta( $post->ID, 'zeme', true );
	echo '<select name="mnd_zeme" class="widefat">';
	foreach ( mnd_core_countries() as $key => $label ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $key ), selected( $value, $key, false ), esc_html( $label ) );
	}
	echo '</select>';
}

/**
 * Uložení polí.
 *
 * @param int     $post_id ID obsahu.
 * @param WP_Post $post    Obsah.
 */
function mnd_core_fields_save( $post_id, $post ) {
	if ( class_exists( 'ACF' ) || ! isset( $_POST['mnd_core_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mnd_core_fields_nonce'] ) ), 'mnd_core_fields' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	switch ( $post->post_type ) {
		case 'page':
			if ( isset( $_POST['mnd_doc_present'] ) ) {
				$ids = isset( $_POST['mnd_doc'] ) ? array_values( array_unique( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['mnd_doc'] ) ) ) ) ) : array();
				// Stejný formát jako ACF (pole řetězců), prázdné pole jako prázdný řetězec.
				update_post_meta( $post_id, 'doc', $ids ? array_map( 'strval', $ids ) : '' );
			}
			if ( isset( $_POST['mnd_item_category'] ) ) {
				$term = absint( wp_unslash( $_POST['mnd_item_category'] ) );
				update_post_meta( $post_id, 'item_category', $term ? (string) $term : '' );
			}
			break;
		case 'document':
			if ( isset( $_POST['mnd_file'] ) ) {
				$file = absint( wp_unslash( $_POST['mnd_file'] ) );
				update_post_meta( $post_id, 'file', $file ? (string) $file : '' );
			}
			break;
		case 'timelineitem':
			foreach ( array( 'from', 'to' ) as $key ) {
				if ( isset( $_POST[ 'mnd_' . $key ] ) ) {
					update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ 'mnd_' . $key ] ) ) );
				}
			}
			break;
		case 'mapitem':
			$zeme = isset( $_POST['mnd_zeme'] ) && is_string( $_POST['mnd_zeme'] ) ? sanitize_key( wp_unslash( $_POST['mnd_zeme'] ) ) : '';
			if ( isset( mnd_core_countries()[ $zeme ] ) ) {
				update_post_meta( $post_id, 'zeme', $zeme );
			}
			break;
	}
}
add_action( 'save_post', 'mnd_core_fields_save', 10, 2 );
