/**
 * PressGrid — Vanilla JS (deferred, no jQuery)
 * v2.5.0
 * @package PressGrid
 */
( function () {
	'use strict';

	/* ── Mobile nav ─────────────────────────────────────────────── */
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

	/* ── Dropdown ARIA ───────────────────────────────────────────── */
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
					if ( ! li.contains( document.activeElement ) ) {
						a.setAttribute( 'aria-expanded', 'false' );
					}
				}, 80 );
			} );
		} );
	}

	/* ── Sticky nav shadow on scroll ─────────────────────────────── */
	function initSticky() {
		var wrap = document.getElementById( 'pg-nav-wrap' );
		if ( ! wrap ) { return; }
		window.addEventListener( 'scroll', function () {
			wrap.classList.toggle( 'scrolled', window.scrollY > 4 );
		}, { passive: true } );
	}

	/* ── Lazy image fallback (за стари браузъри) ─────────────────── */
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

	/* ── Reading Progress Bar ─────────────────────────────────────
	   Показва се само на single post страниците.
	   Инжектира тънка червена линия горе в страницата.
	────────────────────────────────────────────────────────────── */
	function initProgressBar() {
		// Показваме само на single статии (body има class 'single-post')
		if ( ! document.body.classList.contains( 'single-post' ) ) { return; }

		var bar = document.createElement( 'div' );
		bar.id = 'pg-progress-bar';
		bar.setAttribute( 'role', 'progressbar' );
		bar.setAttribute( 'aria-hidden', 'true' );
		document.body.appendChild( bar );

		function updateProgress() {
			var scrollTop    = window.scrollY || document.documentElement.scrollTop;
			var docHeight    = document.documentElement.scrollHeight - document.documentElement.clientHeight;
			var progress     = docHeight > 0 ? ( scrollTop / docHeight ) * 100 : 0;
			bar.style.width  = Math.min( 100, Math.max( 0, progress ) ).toFixed( 2 ) + '%';
		}

		window.addEventListener( 'scroll', updateProgress, { passive: true } );
		updateProgress();
	}

	/* ── Back To Top Button ───────────────────────────────────────
	   Появява се след 400px scroll.
	────────────────────────────────────────────────────────────── */
	function initBackToTop() {
		var btn = document.getElementById( 'pg-back-to-top' );
		if ( ! btn ) { return; }

		window.addEventListener( 'scroll', function () {
			btn.classList.toggle( 'visible', window.scrollY > 400 );
		}, { passive: true } );

		btn.addEventListener( 'click', function () {
			window.scrollTo( { top: 0, behavior: 'smooth' } );
		} );
	}

	/* ── Native Share Buttons ─────────────────────────────────────
	   Ползва Web Share API ако е наличен (мобилни устройства).
	   Fallback: копира линка в clipboard.
	   Нула external JS, нула external requests.
	────────────────────────────────────────────────────────────── */
	function initShare() {
		// Share button (Web Share API)
		var shareBtn = document.getElementById( 'pg-share-native' );
		if ( shareBtn && navigator.share ) {
			shareBtn.style.display = 'inline-flex';
			shareBtn.addEventListener( 'click', function () {
				navigator.share( {
					title: document.title,
					url:   window.location.href,
				} ).catch( function () {} ); // потребителят може да откаже
			} );
		}

		// Copy link button
		var copyBtn = document.getElementById( 'pg-share-copy' );
		if ( copyBtn ) {
			copyBtn.addEventListener( 'click', function () {
				var url  = copyBtn.dataset.url || window.location.href;
				var label = copyBtn.dataset.copied || 'Копирано!';
				if ( navigator.clipboard ) {
					navigator.clipboard.writeText( url ).then( function () {
						showCopied( copyBtn, label );
					} );
				} else {
					// Стар fallback
					var ta = document.createElement( 'textarea' );
					ta.value = url;
					ta.style.position = 'fixed';
					ta.style.opacity  = '0';
					document.body.appendChild( ta );
					ta.select();
					document.execCommand( 'copy' );
					document.body.removeChild( ta );
					showCopied( copyBtn, label );
				}
			} );
		}

		function showCopied( btn, label ) {
			var original = btn.innerHTML;
			btn.innerHTML = '✓ ' + label;
			btn.classList.add( 'copied' );
			setTimeout( function () {
				btn.innerHTML = original;
				btn.classList.remove( 'copied' );
			}, 2000 );
		}
	}

	/* ── Init ─────────────────────────────────────────────────────── */
	document.addEventListener( 'DOMContentLoaded', function () {
		initNav();
		initDropdowns();
		initSticky();
		initLazyImages();
		initProgressBar();
		initBackToTop();
		initShare();
	} );

}() );
