/**
 * FlairFacilitiesLtd — Animations
 * Fade up, slide in, scale effects on scroll. Sticky header.
 */
(function () {
    'use strict';

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

    /* Sticky header */
    const header = document.querySelector('.ffl-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('is-scrolled', window.pageYOffset > 20);
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
