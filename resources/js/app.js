import './bootstrap';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

document.addEventListener('DOMContentLoaded', () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ── Reduced motion: reveal everything instantly, skip all motion ── */
    if (reducedMotion) {
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale')
            .forEach(el => el.classList.add('active'));
        return;
    }

    /* ── Scroll reveals ─────────────────────────────────────────────────
       ScrollTrigger fires once when the element enters the viewport and
       adds .active — CSS transitions on the element do the actual fade/slide.
       This keeps the animation declaration in CSS (easy to tweak) while
       GSAP provides the reliable scroll detection.
    ─────────────────────────────────────────────────────────────────── */
    gsap.utils.toArray('.reveal, .reveal-left, .reveal-right, .reveal-scale')
        .forEach(el => {
            ScrollTrigger.create({
                trigger: el,
                start: 'top 88%',
                once: true,
                onEnter: () => el.classList.add('active'),
            });
        });

    /* ── Hero media gentle float (replaces heavy CSS floatY keyframe) ───
       One element, one GSAP tween, repeating yoyo — very low overhead.
    ─────────────────────────────────────────────────────────────────── */
    const heroMedia = document.querySelector('.hero-media');
    if (heroMedia) {
        gsap.to(heroMedia, {
            y: -10,
            duration: 4.5,
            ease: 'sine.inOut',
            repeat: -1,
            yoyo: true,
        });
    }

    /* ── FAQ accordion ──────────────────────────────────────────────────
       GSAP animates the <p> directly to height:auto.
       No wrapper div needed — cleaner DOM.
    ─────────────────────────────────────────────────────────────────── */
    document.querySelectorAll('.faq-item').forEach(details => {
        const summary = details.querySelector('summary');
        const body    = details.querySelector('p');
        if (!summary || !body) return;

        /* Set initial collapsed state */
        gsap.set(body, { height: 0, opacity: 0, overflow: 'hidden', marginTop: 0 });

        summary.addEventListener('click', e => {
            e.preventDefault();
            const isOpen = details.hasAttribute('open');

            if (isOpen) {
                gsap.to(body, {
                    height: 0,
                    opacity: 0,
                    marginTop: 0,
                    duration: 0.32,
                    ease: 'power2.in',
                    onComplete: () => details.removeAttribute('open'),
                });
            } else {
                details.setAttribute('open', '');
                gsap.to(body, {
                    height: 'auto',
                    opacity: 1,
                    marginTop: '0.5rem',
                    duration: 0.4,
                    ease: 'power2.out',
                });
            }
        });
    });
});
