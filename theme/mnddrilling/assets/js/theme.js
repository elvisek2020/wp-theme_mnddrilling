/**
 * MND Drilling — chování stránek (bez jQuery).
 *
 * 1) Mobilní panel: divize, hledání a jazyk
 * 2) Výběr podstránky na mobilu
 * 3) Katalog položek a lidé: výběr položky (adresy #item-ID z původní šablony fungují dál)
 * 4) Slider na titulce
 * 5) Prohlížeč obrázků z odkazů v textu
 * 6) Formidable: tlačítko „Přiložit“ u nahrání souboru
 */
( function () {
	'use strict';

	var l10n = window.mndL10n || {};
	var isEn = document.documentElement.lang.indexOf( 'en' ) === 0;

	/* 1) Mobilní panel */
	var toggle = document.querySelector( '.mnd-nav-toggle' );
	if ( toggle ) {
		var setOpen = function ( open ) {
			document.body.classList.toggle( 'is-nav-open', open );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open ) {
				// Fokus na první divizi (ne do hledání – na mobilu by vyskočila klávesnice).
				var first = document.querySelector( '.mnd-tiles--menu a' );
				if ( first ) {
					first.focus( { preventScroll: true } );
				}
			}
		};
		toggle.addEventListener( 'click', function () {
			setOpen( ! document.body.classList.contains( 'is-nav-open' ) );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && document.body.classList.contains( 'is-nav-open' ) ) {
				setOpen( false );
				toggle.focus();
			}
		} );
	}

	/* 2) Výběr podstránky */
	document.querySelectorAll( 'select[data-mnd-navigate]' ).forEach( function ( select ) {
		select.addEventListener( 'change', function () {
			if ( select.value ) {
				window.location.href = select.value;
			}
		} );
	} );

	/* 3) Katalog položek a lidé */
	document.querySelectorAll( '[data-mnd-items]' ).forEach( function ( box ) {
		var views = box.querySelectorAll( '[data-item-view]' );
		if ( ! views.length ) {
			return;
		}
		box.classList.add( 'js-items' );

		var groups = box.querySelectorAll( '[data-group-list]' );
		var topNav = box.querySelector( '.mnd-items__nav' );

		function setActive( list, link ) {
			list.querySelectorAll( 'li' ).forEach( function ( li ) {
				li.classList.remove( 'is-active' );
			} );
			if ( link ) {
				link.parentNode.classList.add( 'is-active' );
			}
		}

		function openGroup( id ) {
			groups.forEach( function ( list ) {
				list.classList.toggle( 'is-open', list.getAttribute( 'data-group-list' ) === String( id ) );
			} );
		}

		function show( id, updateHash ) {
			var found = false;
			views.forEach( function ( view ) {
				var match = view.getAttribute( 'data-item-view' ) === String( id );
				view.classList.toggle( 'is-open', match );
				found = found || match;
			} );
			if ( ! found ) {
				return false;
			}
			// Položka v podskupině: otevřít skupinu a označit ji nahoře.
			var inGroup = box.querySelector( '[data-group-list] [data-item="' + id + '"]' );
			if ( inGroup ) {
				var group = inGroup.getAttribute( 'data-in-group' );
				openGroup( group );
				setActive( inGroup.closest( 'ul' ), inGroup );
				setActive( topNav, topNav.querySelector( '[data-group="' + group + '"]' ) );
			} else {
				openGroup( null );
				setActive( topNav, topNav.querySelector( '[data-item="' + id + '"]' ) );
			}
			if ( updateHash && window.history.replaceState ) {
				window.history.replaceState( null, '', '#item-' + id );
			}
			return true;
		}

		function firstOf( link ) {
			if ( link.hasAttribute( 'data-group' ) ) {
				var first = box.querySelector( '[data-group-list="' + link.getAttribute( 'data-group' ) + '"] [data-item]' );
				return first ? first.getAttribute( 'data-item' ) : null;
			}
			return link.getAttribute( 'data-item' );
		}

		box.addEventListener( 'click', function ( e ) {
			var link = e.target.closest( '[data-item], [data-group]' );
			if ( ! link || ! box.contains( link ) ) {
				return;
			}
			e.preventDefault();
			var id = firstOf( link );
			if ( id ) {
				show( id, true );
			} else if ( link.hasAttribute( 'data-group' ) ) {
				openGroup( link.getAttribute( 'data-group' ) );
				setActive( topNav, link );
			}
		} );

		var hash = window.location.hash.match( /^#item-(\d+)$/ );
		if ( ! hash || ! show( hash[ 1 ], false ) ) {
			var first = topNav ? topNav.querySelector( '[data-item], [data-group]' ) : null;
			var id = first ? firstOf( first ) : views[ 0 ].getAttribute( 'data-item-view' );
			show( id || views[ 0 ].getAttribute( 'data-item-view' ), false );
		}
	} );

	/* 4) Slider */
	var slider = document.querySelector( '[data-mnd-slider]' );
	if ( slider ) {
		var slides = slider.querySelectorAll( '.mnd-slider__slide' );
		var current = 0;
		var timer = null;
		var interval = parseInt( l10n.interval, 10 ) || 4000;
		var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		var go = function ( index ) {
			slides[ current ].classList.remove( 'is-active' );
			slides[ current ].setAttribute( 'aria-hidden', 'true' );
			current = ( index + slides.length ) % slides.length;
			slides[ current ].classList.add( 'is-active' );
			slides[ current ].removeAttribute( 'aria-hidden' );
		};
		var stop = function () {
			window.clearInterval( timer );
			timer = null;
		};
		var start = function () {
			if ( reduced || slides.length < 2 || timer ) {
				return;
			}
			timer = window.setInterval( function () {
				go( current + 1 );
			}, interval );
		};

		slider.addEventListener( 'click', function ( e ) {
			var button = e.target.closest( '[data-mnd-slide]' );
			if ( button ) {
				go( current + parseInt( button.getAttribute( 'data-mnd-slide' ), 10 ) );
			}
		} );
		slider.addEventListener( 'mouseenter', stop );
		slider.addEventListener( 'mouseleave', start );
		slider.addEventListener( 'focusin', stop );
		slider.addEventListener( 'focusout', start );
		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				stop();
			} else {
				start();
			}
		} );
		start();
	}

	/* 5) Prohlížeč obrázků */
	var imageLinks = Array.prototype.filter.call( document.querySelectorAll( '.mnd-entry a[href]' ), function ( a ) {
		return /\.(jpe?g|png|gif|webp|avif)(\?.*)?$/i.test( a.getAttribute( 'href' ) );
	} );
	if ( imageLinks.length ) {
		var box = document.createElement( 'div' );
		var index = 0;
		box.className = 'mnd-lightbox';
		box.hidden = true;
		box.setAttribute( 'role', 'dialog' );
		box.setAttribute( 'aria-modal', 'true' );
		box.innerHTML = '<img alt=""><button type="button" class="mnd-lightbox__close">×</button>' +
			( imageLinks.length > 1 ? '<button type="button" class="mnd-lightbox__prev">‹</button><button type="button" class="mnd-lightbox__next">›</button>' : '' );
		document.body.appendChild( box );
		var img = box.querySelector( 'img' );
		box.querySelector( '.mnd-lightbox__close' ).setAttribute( 'aria-label', l10n.close || 'Zavřít' );
		if ( imageLinks.length > 1 ) {
			box.querySelector( '.mnd-lightbox__prev' ).setAttribute( 'aria-label', l10n.prev || 'Předchozí' );
			box.querySelector( '.mnd-lightbox__next' ).setAttribute( 'aria-label', l10n.next || 'Další' );
		}
		var lastFocus = null;

		var open = function ( i ) {
			index = ( i + imageLinks.length ) % imageLinks.length;
			var link = imageLinks[ index ];
			var thumb = link.querySelector( 'img' );
			img.src = link.href;
			img.alt = thumb ? thumb.alt : '';
			if ( box.hidden ) {
				lastFocus = document.activeElement;
				box.hidden = false;
				box.querySelector( '.mnd-lightbox__close' ).focus();
			}
		};
		var close = function () {
			box.hidden = true;
			img.removeAttribute( 'src' );
			if ( lastFocus ) {
				lastFocus.focus();
			}
		};

		imageLinks.forEach( function ( link, i ) {
			link.addEventListener( 'click', function ( e ) {
				if ( e.metaKey || e.ctrlKey || e.shiftKey ) {
					return;
				}
				e.preventDefault();
				open( i );
			} );
		} );
		box.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( '.mnd-lightbox__prev' ) ) {
				open( index - 1 );
			} else if ( e.target.closest( '.mnd-lightbox__next' ) ) {
				open( index + 1 );
			} else if ( e.target !== img ) {
				close();
			}
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( box.hidden ) {
				return;
			}
			if ( 'Escape' === e.key ) {
				close();
			} else if ( 'ArrowLeft' === e.key && imageLinks.length > 1 ) {
				open( index - 1 );
			} else if ( 'ArrowRight' === e.key && imageLinks.length > 1 ) {
				open( index + 1 );
			}
		} );
	}

	/* 6) Formidable: „Přiložit“ místo anglického „Drop a file here or click to upload“ */
	document.querySelectorAll( '.mnd-form .frm_upload_text button' ).forEach( function ( button ) {
		button.textContent = isEn ? 'Attach' : 'Přiložit';
	} );
}() );
