/**
 * FlairFacilitiesLtd — Subtle Animations
 * Clean, professional scroll reveals only.
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
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.ffl-fade-up').forEach(el => observer.observe(el));

    /* Sticky header shadow */
    const header = document.querySelector('.ffl-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.style.boxShadow = window.pageYOffset > 10
                ? '0 4px 30px rgba(0,0,0,0.25)'
                : '0 2px 20px rgba(0,0,0,0.15)';
        }, { passive: true });
    }

    /* Smooth scroll for anchors */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const id = a.getAttribute('href');
            if (id === '#') return;
            const target = document.querySelector(id);
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
})();
