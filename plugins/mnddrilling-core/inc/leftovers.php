<?php
/**
 * Pozůstatky odebraných pluginů a staré šablony – sekce v Nástroje → Údržba webu.
 *
 * Ukáže jen to, co na webu opravdu zůstalo a patří pluginu, který není aktivní
 * (tabulky, volby, metadata, role, složky). Akce „Do karantény“ nic nemaže:
 *  - složky se přesunou do karantény (mimo webový kořen, pokud to hosting dovolí),
 *  - volby, metadata a role se uloží do JSON souboru v karanténě a pak odeberou,
 *  - tabulky se přejmenují na {prefix}mndq_… (data zůstanou v databázi).
 * Trvalé smazání karantény je samostatné tlačítko – rozhoduje správce webu.
 *
 * @package MNDDrillingCore
 */

defined( 'ABSPATH' ) || exit;

const MND_CORE_QUARANTINE_TABLE = 'mndq_';

/**
 * Známé pozůstatky: slug => [název, plugin nebo šablona, tabulky, volby, cesty].
 * Vzory: % = cokoli. Cesty relativně k wp-content (glob).
 *
 * @return array
 */
function mnd_core_leftover_defs() {
	return array(
		'simple-login-log'  => array(
			'name'    => 'Simple Login Log',
			'plugin'  => 'simple-login-log/simple-login-log.php',
			'tables'  => array( 'simple_login_log' ),
			'options' => array( 'sll_%', 'simple_login_log%' ),
			'paths'   => array(),
		),
		'backupwordpress'   => array(
			'name'    => 'BackUpWordPress',
			'plugin'  => 'backupwordpress/backupwordpress.php',
			'tables'  => array(),
			'options' => array( 'hmbkp_%', '_transient_hmbkp_%', '_transient_timeout_hmbkp_%' ),
			'paths'   => array( 'backupwordpress-*-backups' ),
		),
		'monsterinsights'   => array(
			'name'    => 'MonsterInsights',
			'plugin'  => 'google-analytics-for-wordpress/googleanalytics.php',
			'tables'  => array( 'monsterinsights_%' ),
			'options' => array( 'monsterinsights_%', 'widget_monsterinsights%', '_transient_monsterinsights_%', '_transient_timeout_monsterinsights_%', '_transient__monsterinsights_%', '_transient_timeout__monsterinsights_%', '_site_transient_monsterinsights_%', '_site_transient_timeout_monsterinsights_%' ),
			'paths'   => array(),
		),
		'lightbox'          => array(
			'name'    => 'Huge IT Lightbox',
			'plugin'  => 'lightbox/lightbox.php',
			'tables'  => array(),
			'options' => array( 'hugeit_lightbox_%', 'lightbox_open_close_effect' ),
			'paths'   => array(),
		),
		'admin-menu-editor' => array(
			'name'    => 'Admin Menu Editor',
			'plugin'  => 'admin-menu-editor/menu-editor.php',
			'tables'  => array(),
			'options' => array( 'ws_menu_editor', 'ws_menu_editor_%', 'ws_ame_%' ),
			'paths'   => array(),
		),
		'aam'               => array(
			'name'    => 'Advanced Access Manager',
			'plugin'  => 'advanced-access-manager/aam.php',
			'tables'  => array(),
			'options' => array( 'aam_%', 'aam-%', 'widget_aam_%' ),
			'paths'   => array(),
			// Role, kterou spravoval jen AAM: žádný uživatel ji nemá a měla nebezpečná oprávnění (editace šablon, pluginy, uživatelé).
			'roles'   => array( 'career manager' ),
		),
		'scpo'              => array(
			'name'    => 'Simple Custom Post Order',
			'plugin'  => 'simple-custom-post-order/simple-custom-post-order.php',
			'tables'  => array(),
			'options' => array( 'scporder_%' ),
			'paths'   => array(),
		),
		'acf'               => array(
			'name'    => 'Advanced Custom Fields',
			'plugin'  => 'advanced-custom-fields/acf.php',
			'tables'  => array(),
			'options' => array( 'acf_%' ),
			'paths'   => array(),
		),
		'simple-fields'     => array(
			'name'     => 'Simple Fields',
			'plugin'   => 'simple-fields/simple_fields.php',
			'tables'   => array(),
			'options'  => array( 'simple_fields_%' ),
			'paths'    => array(),
			// Stará data polí; dokumenty stránek už převedl inc/fields.php do pole „doc“.
			'postmeta' => array( '_simple_fields_%' ),
			'requires' => 'mnd_core_fields_migrated',
		),
		'responsive-lightbox' => array(
			'name'    => 'Responsive Lightbox',
			'plugin'  => 'responsive-lightbox/responsive-lightbox.php',
			'tables'  => array(),
			'options' => array( 'responsive_lightbox_%' ),
			'paths'   => array(),
		),
		'old-theme'         => array(
			'name'    => __( 'Původní šablona Ultimate for MND Drilling', 'mnddrilling-core' ),
			'theme'   => 'ultimate-for-mnd-drilling',
			'tables'  => array(),
			// Volby email_count, email_N a category_N (kategorie volných pozic) se používají dál – viz inc/positions.php.
			'options' => array( 'theme_mods_ultimate-for-mnd-drilling', 'theme_mods_ultimate-for-mnd', 'theme_mods_twentyfifteen' ),
			'paths'   => array( 'themes/ultimate-for-mnd-drilling' ),
		),
		'default-themes'    => array(
			'name'    => __( 'Nepoužívané výchozí šablony (Twenty Twenty-Three, Twenty Twenty-Four)', 'mnddrilling-core' ),
			'theme'   => array( 'twentytwentythree', 'twentytwentyfour' ),
			'tables'  => array(),
			'options' => array( 'theme_mods_twentytwentythree', 'theme_mods_twentytwentyfour' ),
			'paths'   => array( 'themes/twentytwentythree', 'themes/twentytwentyfour' ),
		),
	);
}

/**
 * Je plugin nebo šablona pozůstatku ještě v provozu?
 *
 * @param array $def Definice.
 * @return bool
 */
function mnd_core_leftover_in_use( $def ) {
	// Pozůstatek, jehož data se teprve převádějí, se zatím nenabízí.
	if ( ! empty( $def['requires'] ) && ( ! function_exists( $def['requires'] ) || ! call_user_func( $def['requires'] ) ) ) {
		return true;
	}
	if ( ! empty( $def['plugin'] ) ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( $def['plugin'] );
	}
	if ( ! empty( $def['theme'] ) ) {
		return (bool) array_intersect( (array) $def['theme'], array( get_template(), get_stylesheet() ) );
	}
	return false;
}

/**
 * Co z pozůstatku na webu skutečně je.
 *
 * @param array $def Definice.
 * @return array [tables => [název => bajty], options => [název => bajty], paths => [cesta => bajty]]
 */
function mnd_core_leftover_scan( $def ) {
	global $wpdb;
	$found = array(
		'tables'   => array(),
		'options'  => array(),
		'paths'    => array(),
		'roles'    => array(),
		'postmeta' => array(),
	);

	foreach ( $def['tables'] as $pattern ) {
		$like = $wpdb->esc_like( $wpdb->prefix ) . str_replace( '\%', '%', $wpdb->esc_like( $pattern ) );
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT table_name AS t, data_length + index_length AS b FROM information_schema.tables WHERE table_schema = %s AND table_name LIKE %s',
				DB_NAME,
				$like
			)
		);
		foreach ( (array) $rows as $row ) {
			if ( 0 !== strpos( $row->t, $wpdb->prefix . MND_CORE_QUARANTINE_TABLE ) ) {
				$found['tables'][ $row->t ] = (int) $row->b;
			}
		}
	}

	foreach ( $def['options'] as $pattern ) {
		$like = str_replace( '\%', '%', $wpdb->esc_like( $pattern ) );
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_name AS n, LENGTH(option_value) AS b FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) );
		foreach ( (array) $rows as $row ) {
			$found['options'][ $row->n ] = (int) $row->b;
		}
	}

	foreach ( $def['paths'] as $pattern ) {
		foreach ( (array) glob( WP_CONTENT_DIR . '/' . $pattern, GLOB_ONLYDIR ) as $path ) {
			if ( $path && is_dir( $path ) ) {
				$found['paths'][ $path ] = mnd_core_path_size( $path );
			}
		}
	}

	// Role jen tehdy, když ji nemá žádný uživatel.
	foreach ( isset( $def['roles'] ) ? $def['roles'] : array() as $role ) {
		if ( get_role( $role ) && ! get_users( array( 'role' => $role, 'fields' => 'ID', 'number' => 1 ) ) ) {
			$found['roles'][ $role ] = 0;
		}
	}

	foreach ( isset( $def['postmeta'] ) ? $def['postmeta'] : array() as $pattern ) {
		$like = str_replace( '\\%', '%', $wpdb->esc_like( $pattern ) );
		$row  = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(*) AS n, COALESCE(SUM(LENGTH(meta_value)), 0) AS b FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $like ) );
		if ( $row && (int) $row->n ) {
			$found['postmeta'][ $pattern ] = (int) $row->b;
		}
	}
	return $found;
}

/**
 * Přesun pozůstatku do karantény.
 *
 * @param string $slug Klíč definice.
 * @return string Zpráva.
 */
function mnd_core_leftover_quarantine( $slug ) {
	global $wpdb;
	$defs = mnd_core_leftover_defs();
	if ( ! isset( $defs[ $slug ] ) ) {
		return __( 'Neznámá položka.', 'mnddrilling-core' );
	}
	$def = $defs[ $slug ];
	if ( mnd_core_leftover_in_use( $def ) ) {
		return __( 'Plugin nebo šablona je pořád aktivní – nejdřív ji vypněte.', 'mnddrilling-core' );
	}

	$found = mnd_core_leftover_scan( $def );
	$dir   = mnd_core_quarantine_dir( wp_date( 'Y-m-d' ) . '/' . $slug );
	if ( is_wp_error( $dir ) ) {
		return $dir->get_error_message();
	}
	$done = array();

	// Role a metadata obsahu: záloha do JSON, pak odebrat.
	if ( $found['roles'] || $found['postmeta'] ) {
		$backup = array(
			'roles'    => array(),
			'postmeta' => array(),
		);
		foreach ( array_keys( $found['roles'] ) as $role ) {
			$backup['roles'][ $role ] = get_role( $role )->capabilities;
		}
		foreach ( array_keys( $found['postmeta'] ) as $pattern ) {
			$like                 = str_replace( '\\%', '%', $wpdb->esc_like( $pattern ) );
			$backup['postmeta'][] = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $like ), ARRAY_A );
		}
		$file = $dir . 'roles-meta-' . time() . '.json';
		if ( false === file_put_contents( $file, wp_json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
			return __( 'Zálohu rolí a metadat se nepodařilo uložit – nic se nezměnilo.', 'mnddrilling-core' );
		}
		foreach ( array_keys( $found['roles'] ) as $role ) {
			remove_role( $role );
		}
		foreach ( array_keys( $found['postmeta'] ) as $pattern ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", str_replace( '\\%', '%', $wpdb->esc_like( $pattern ) ) ) );
		}
		if ( $found['roles'] ) {
			/* translators: %s: role names. */
			$done[] = sprintf( __( 'role: %s', 'mnddrilling-core' ), implode( ', ', array_keys( $found['roles'] ) ) );
		}
		if ( $found['postmeta'] ) {
			/* translators: %d: number of meta rows. */
			$done[] = sprintf( __( 'metadat: %d', 'mnddrilling-core' ), array_sum( array_map( 'count', $backup['postmeta'] ) ) );
		}
	}

	// Volby: nejdřív záloha do JSON, pak odebrat.
	if ( $found['options'] ) {
		$backup = array();
		foreach ( array_keys( $found['options'] ) as $name ) {
			$backup[ $name ] = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name ) );
		}
		$file = $dir . 'options-' . time() . '.json';
		if ( false === file_put_contents( $file, wp_json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
			return __( 'Zálohu voleb se nepodařilo uložit – nic se nezměnilo.', 'mnddrilling-core' );
		}
		foreach ( array_keys( $backup ) as $name ) {
			delete_option( $name );
		}
		/* translators: %d: number of options. */
		$done[] = sprintf( __( 'voleb: %d', 'mnddrilling-core' ), count( $backup ) );
	}

	// Tabulky: přejmenovat do karantény.
	$renamed = 0;
	foreach ( array_keys( $found['tables'] ) as $table ) {
		$target = $wpdb->prefix . MND_CORE_QUARANTINE_TABLE . substr( $table, strlen( $wpdb->prefix ) );
		if ( false !== $wpdb->query( 'RENAME TABLE `' . esc_sql( $table ) . '` TO `' . esc_sql( substr( $target, 0, 64 ) ) . '`' ) ) {
			$renamed++;
		}
	}
	if ( $renamed ) {
		/* translators: %d: number of tables. */
		$done[] = sprintf( __( 'tabulek: %d', 'mnddrilling-core' ), $renamed );
	}

	// Složky: přesunout.
	$moved  = 0;
	$failed = array();
	foreach ( array_keys( $found['paths'] ) as $path ) {
		if ( mnd_core_quarantine_move( $path, $slug ) ) {
			$moved++;
		} else {
			$failed[] = str_replace( WP_CONTENT_DIR, 'wp-content', $path );
		}
	}
	if ( $moved ) {
		/* translators: %d: number of folders. */
		$done[] = sprintf( __( 'složek: %d', 'mnddrilling-core' ), $moved );
	}

	/* translators: 1: item name, 2: what was moved. */
	$msg = sprintf( __( '%1$s – do karantény přesunuto: %2$s.', 'mnddrilling-core' ), $def['name'], $done ? implode( ', ', $done ) : __( 'nic', 'mnddrilling-core' ) );
	if ( $failed ) {
		$msg .= ' ' . __( 'Nepodařilo se přesunout:', 'mnddrilling-core' ) . ' ' . implode( ', ', $failed ) . '.';
	}
	return $msg;
}

/**
 * Obsah karantény: složky (soubory) a přejmenované tabulky.
 *
 * @return array [dir => cesta|null, bytes => int, tables => [název => bajty]]
 */
function mnd_core_quarantine_contents() {
	global $wpdb;
	$outside = dirname( untrailingslashit( ABSPATH ) ) . '/mnddrilling-karantena';
	$inside  = WP_CONTENT_DIR . '/mnddrilling-karantena';
	$dir     = is_dir( $outside ) ? $outside : ( is_dir( $inside ) ? $inside : null );
	$bytes   = $dir ? mnd_core_path_size( $dir ) : 0;
	$tables  = array();
	$rows    = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT table_name AS t, data_length + index_length AS b FROM information_schema.tables WHERE table_schema = %s AND table_name LIKE %s',
			DB_NAME,
			$wpdb->esc_like( $wpdb->prefix . MND_CORE_QUARANTINE_TABLE ) . '%'
		)
	);
	foreach ( (array) $rows as $row ) {
		$tables[ $row->t ] = (int) $row->b;
	}
	return array(
		'dir'    => $dir,
		'bytes'  => $bytes,
		'tables' => $tables,
	);
}

/**
 * Trvalé smazání karantény (soubory i tabulky).
 *
 * @return string Zpráva.
 */
function mnd_core_quarantine_purge() {
	global $wpdb;
	$q = mnd_core_quarantine_contents();
	foreach ( array_keys( $q['tables'] ) as $table ) {
		if ( 0 === strpos( $table, $wpdb->prefix . MND_CORE_QUARANTINE_TABLE ) ) {
			$wpdb->query( 'DROP TABLE IF EXISTS `' . esc_sql( $table ) . '`' );
		}
	}
	if ( $q['dir'] && 'mnddrilling-karantena' === basename( $q['dir'] ) ) {
		$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $q['dir'], FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $it as $file ) {
			$file->isDir() ? @rmdir( $file->getPathname() ) : @unlink( $file->getPathname() ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
		@rmdir( $q['dir'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	/* translators: 1: size, 2: number of tables. */
	return sprintf( __( 'Karanténa smazána: %1$s souborů a %2$d tabulek.', 'mnddrilling-core' ), size_format( $q['bytes'], 1 ), count( $q['tables'] ) );
}

/**
 * Akce z formuláře (volá mnd_core_maintenance_action po ověření nonce a oprávnění).
 *
 * @param string $task Úloha.
 * @param string $item Slug pozůstatku.
 * @return string Zpráva.
 */
function mnd_core_leftovers_action( $task, $item ) {
	return 'quarantine_purge' === $task ? mnd_core_quarantine_purge() : mnd_core_leftover_quarantine( $item );
}

/**
 * Sekce v Údržbě webu.
 */
function mnd_core_leftovers_section() {
	$rows = array();
	foreach ( mnd_core_leftover_defs() as $slug => $def ) {
		if ( mnd_core_leftover_in_use( $def ) ) {
			continue;
		}
		$found = mnd_core_leftover_scan( $def );
		if ( $found['tables'] || $found['options'] || $found['paths'] ) {
			$rows[ $slug ] = array( $def, $found );
		}
	}
	$q = mnd_core_quarantine_contents();
	?>
	<h2><?php esc_html_e( 'Pozůstatky odebraných pluginů', 'mnddrilling-core' ); ?></h2>
	<?php if ( $rows ) : ?>
		<p><?php esc_html_e( 'Tabulky, volby, metadata, role a složky pluginů (a staré šablony), které na webu nejsou aktivní. „Do karantény“ nic nemaže: složky přesune do karantény, volby, metadata a role uloží do JSON souboru v karanténě a tabulky přejmenuje. Plugin samotný smažte v Pluginy.', 'mnddrilling-core' ); ?></p>
		<table class="widefat striped" style="max-width:860px">
			<tbody>
				<?php foreach ( $rows as $slug => $row ) : ?>
					<?php
					list( $def, $found ) = $row;
					$parts               = array();
					if ( $found['tables'] ) {
						/* translators: 1: number of tables, 2: size. */
						$parts[] = sprintf( __( 'tabulky: %1$d (%2$s)', 'mnddrilling-core' ), count( $found['tables'] ), size_format( array_sum( $found['tables'] ), 1 ) );
					}
					if ( $found['options'] ) {
						/* translators: 1: number of options, 2: size. */
						$parts[] = sprintf( __( 'volby: %1$d (%2$s)', 'mnddrilling-core' ), count( $found['options'] ), size_format( array_sum( $found['options'] ), 1 ) );
					}
					if ( $found['paths'] ) {
						$names = array_map(
							function ( $path ) {
								return str_replace( WP_CONTENT_DIR, 'wp-content', $path );
							},
							array_keys( $found['paths'] )
						);
						/* translators: 1: folders, 2: size. */
						$parts[] = sprintf( __( 'složky: %1$s (%2$s)', 'mnddrilling-core' ), implode( ', ', $names ), size_format( array_sum( $found['paths'] ), 1 ) );
					}
					if ( $found['roles'] ) {
						/* translators: %s: role names. */
						$parts[] = sprintf( __( 'role bez uživatelů: %s', 'mnddrilling-core' ), implode( ', ', array_keys( $found['roles'] ) ) );
					}
					if ( $found['postmeta'] ) {
						/* translators: %s: size. */
						$parts[] = sprintf( __( 'metadata obsahu (%s)', 'mnddrilling-core' ), size_format( array_sum( $found['postmeta'] ), 1 ) );
					}
					?>
					<tr>
						<td><strong><?php echo esc_html( $def['name'] ); ?></strong><br><span class="description"><?php echo esc_html( implode( ' · ', $parts ) ); ?></span></td>
						<td style="width:150px">
							<?php
							/* translators: %s: item name. */
							mnd_core_action_button( 'leftovers', __( 'Do karantény', 'mnddrilling-core' ), sprintf( __( 'Přesunout pozůstatky „%s“ do karantény?', 'mnddrilling-core' ), $def['name'] ), false, $slug );
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p><?php esc_html_e( 'Po odebraných pluginech nic nezůstalo.', 'mnddrilling-core' ); ?></p>
	<?php endif; ?>

	<h3><?php esc_html_e( 'Karanténa', 'mnddrilling-core' ); ?></h3>
	<?php if ( $q['dir'] || $q['tables'] ) : ?>
		<p>
			<?php
			/* translators: 1: size, 2: number of tables. */
			echo esc_html( sprintf( __( 'V karanténě: soubory %1$s, tabulky %2$d.', 'mnddrilling-core' ), size_format( $q['bytes'], 1 ), count( $q['tables'] ) ) );
			if ( $q['dir'] ) {
				echo ' ' . esc_html__( 'Složka:', 'mnddrilling-core' ) . ' <code>' . esc_html( $q['dir'] ) . '</code>';
				echo mnd_core_quarantine_outside() ? '' : ' ' . esc_html__( '(ve wp-content, přístup z webu zakázaný)', 'mnddrilling-core' );
			}
			?>
		</p>
		<p class="description"><?php esc_html_e( 'Až ověříte, že web funguje a nic nechybí, můžete karanténu smazat natrvalo. Pro jistotu mějte čerstvou zálohu.', 'mnddrilling-core' ); ?></p>
		<?php mnd_core_action_button( 'quarantine_purge', __( 'Smazat karanténu natrvalo', 'mnddrilling-core' ), __( 'Opravdu natrvalo smazat celou karanténu (soubory i tabulky)? Akce je nevratná.', 'mnddrilling-core' ) ); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Karanténa je prázdná.', 'mnddrilling-core' ); ?></p>
	<?php endif; ?>
	<?php
}
