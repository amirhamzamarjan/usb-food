/**
 * US BENGOL - Smooth Animations & Intersection Observers
 */

(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    // Check reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    initScrollReveal();
    initSmoothAnchorScroll();
  });

  function initScrollReveal() {
    const revealElements = document.querySelectorAll(
      '.section-header, .trust-card, .product-card, .dietary-spotlight-card, .story-visual-frame, .story-paragraphs, .timeline-step, .bulk-target-box, .contact-info-card, .bulk-section-card'
    );

    if (!('IntersectionObserver' in window)) {
      revealElements.forEach(el => el.style.opacity = '1');
      return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          obs.unobserve(entry.target);
        }
      });
    }, {
      rootMargin: '0px 0px -60px 0px',
      threshold: 0.12
    });

    revealElements.forEach((el, index) => {
      el.classList.add('reveal-on-scroll');
      observer.observe(el);
    });

    // Inject reveal CSS animation dynamically
    const style = document.createElement('style');
    style.textContent = `
      .reveal-on-scroll {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
      }
      .reveal-on-scroll.revealed {
        opacity: 1;
        transform: translateY(0);
      }
      .prepare-timeline .timeline-step:nth-child(1) { transition-delay: 0.05s; }
      .prepare-timeline .timeline-step:nth-child(2) { transition-delay: 0.12s; }
      .prepare-timeline .timeline-step:nth-child(3) { transition-delay: 0.19s; }
      .prepare-timeline .timeline-step:nth-child(4) { transition-delay: 0.26s; }
      .prepare-timeline .timeline-step:nth-child(5) { transition-delay: 0.33s; }
    `;
    document.head.appendChild(style);
  }

  function initSmoothAnchorScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const targetId = this.getAttribute('href');
        if (targetId === '#' || targetId === '') return;

        const targetEl = document.querySelector(targetId);
        if (targetEl) {
          e.preventDefault();
          const headerOffset = 90;
          const elementPosition = targetEl.getBoundingClientRect().top;
          const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

          window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
          });
        }
      });
    });
  }
})();
