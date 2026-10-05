/**
 * Pořadí obsahu přetažením řádků v přehledu administrace (MND Drilling Core, inc/order.php).
 * Bez jQuery: HTML5 drag & drop, po puštění se pořadí uloží přes admin-ajax.
 */
( function () {
	'use strict';

	var cfg = window.mndCoreOrder;
	var list = document.getElementById( 'the-list' );
	if ( ! cfg || ! list ) {
		return;
	}

	var rows = function () {
		return Array.prototype.filter.call( list.children, function ( tr ) {
			return /^post-\d+$/.test( tr.id );
		} );
	};
	if ( rows().length < 2 ) {
		return;
	}

	var notice = document.createElement( 'p' );
	notice.className = 'description';
	notice.style.margin = '6px 0';
	notice.textContent = cfg.hint;
	var table = list.closest( 'table' );
	table.parentNode.insertBefore( notice, table );

	var dragged = null;

	rows().forEach( function ( tr ) {
		tr.draggable = true;
		tr.style.cursor = 'move';
	} );

	list.addEventListener( 'dragstart', function ( e ) {
		var tr = e.target.closest && e.target.closest( 'tr' );
		// Odkazy a pole formuláře se přetahovat nemají.
		if ( ! tr || e.target.closest( 'a, input, textarea, select, button' ) ) {
			e.preventDefault();
			return;
		}
		dragged = tr;
		tr.style.opacity = '0.5';
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData( 'text/plain', tr.id );
	} );

	list.addEventListener( 'dragover', function ( e ) {
		var tr = e.target.closest( 'tr' );
		if ( ! dragged || ! tr || tr === dragged || tr.parentNode !== list ) {
			return;
		}
		e.preventDefault();
		var box = tr.getBoundingClientRect();
		list.insertBefore( dragged, e.clientY > box.top + box.height / 2 ? tr.nextSibling : tr );
	} );

	list.addEventListener( 'dragend', function () {
		if ( ! dragged ) {
			return;
		}
		dragged.style.opacity = '';
		dragged = null;
		save();
	} );

	function save() {
		var body = new URLSearchParams();
		body.append( 'action', 'mnd_core_order' );
		body.append( 'nonce', cfg.nonce );
		body.append( 'type', cfg.type );
		rows().forEach( function ( tr ) {
			body.append( 'ids[]', tr.id.replace( 'post-', '' ) );
		} );
		fetch( cfg.ajax, { method: 'POST', credentials: 'same-origin', body: body } )
			.then( function ( r ) {
				return r.json();
			} )
			.then( function ( r ) {
				notice.textContent = r && r.success ? cfg.saved : cfg.error;
			} )
			.catch( function () {
				notice.textContent = cfg.error;
			} );
	}
}() );
