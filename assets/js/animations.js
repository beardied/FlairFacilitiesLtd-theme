/**
 * FlairFacilitiesLtd — Animations + Mobile Menu + Header Shrink
 */
(function () {
    'use strict';

    /* Scroll reveal */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -50px 0px' });

    document.querySelectorAll('.ffl-fade-up, .ffl-slide-left, .ffl-slide-right, .ffl-scale-in').forEach(el => {
        observer.observe(el);
    });

    /* Mobile menu toggle */
    const toggle = document.querySelector('.ffl-menu-toggle');
    const mobileNav = document.querySelector('.ffl-mobile-nav');
    if (toggle && mobileNav) {
        toggle.addEventListener('click', () => {
            const isOpen = toggle.classList.toggle('is-open');
            mobileNav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                toggle.classList.remove('is-open');
                mobileNav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });
    }

    /* Header shrink on scroll */
    const header = document.querySelector('.ffl-header');
    let lastScroll = 0;
    if (header) {
        window.addEventListener('scroll', () => {
            const y = window.scrollY;
            if (y > 60) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
            lastScroll = y;
        }, { passive: true });
    }

    /* Smooth scroll */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const id = a.getAttribute('href');
            if (id === '#') return;
            const target = document.querySelector(id);
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
})();
