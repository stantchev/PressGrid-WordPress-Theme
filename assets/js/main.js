/**
 * PressGrid — Vanilla JS (deferred, no jQuery)
 * @package PressGrid
 */
( function () {
	'use strict';

	/* Mobile nav */
	function initNav() {
		var btn = document.getElementById( 'pg-nav-toggle' );
		var nav = document.getElementById( 'pg-primary-nav' );
		if ( ! btn || ! nav ) { return; }
		btn.addEventListener( 'click', function () {
			var open = nav.classList.toggle( 'is-open' );
			btn.setAttribute( 'aria-expanded', String( open ) );
		} );
		document.addEventListener( 'click', function ( e ) {
			if ( ! nav.contains( e.target ) && ! btn.contains( e.target ) ) {
				nav.classList.remove( 'is-open' );
				btn.setAttribute( 'aria-expanded', 'false' );
			}
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				nav.classList.remove( 'is-open' );
				btn.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	}

	/* Dropdown ARIA */
	function initDropdowns() {
		document.querySelectorAll( '.pg-primary-nav li' ).forEach( function ( li ) {
			var a   = li.querySelector( ':scope > a' );
			var sub = li.querySelector( ':scope > ul' );
			if ( ! a || ! sub ) { return; }
			a.setAttribute( 'aria-haspopup', 'true' );
			a.setAttribute( 'aria-expanded', 'false' );
			li.addEventListener( 'focusin',  function () { a.setAttribute( 'aria-expanded', 'true' ); } );
			li.addEventListener( 'focusout', function () {
				setTimeout( function () {
					if ( ! li.contains( document.activeElement ) ) { a.setAttribute( 'aria-expanded', 'false' ); }
				}, 80 );
			} );
		} );
	}

	/* Sticky nav shadow on scroll */
	function initSticky() {
		var wrap = document.getElementById( 'pg-nav-wrap' );
		if ( ! wrap ) { return; }
		window.addEventListener( 'scroll', function () {
			wrap.classList.toggle( 'scrolled', window.scrollY > 4 );
		}, { passive: true } );
	}

	/* Lazy image via IntersectionObserver (fallback for browsers without native lazy) */
	function initLazyImages() {
		if ( 'loading' in HTMLImageElement.prototype ) { return; }
		var imgs = document.querySelectorAll( 'img[loading="lazy"]' );
		if ( ! imgs.length || ! window.IntersectionObserver ) { return; }
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				if ( e.isIntersecting ) {
					var img = e.target;
					if ( img.dataset.src ) { img.src = img.dataset.src; }
					io.unobserve( img );
				}
			} );
		}, { rootMargin: '200px' } );
		imgs.forEach( function ( img ) { io.observe( img ); } );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initNav();
		initDropdowns();
		initSticky();
		initLazyImages();
	} );
}() );
