import './bootstrap';

// --- Mobile Navigation Drawer Toggle ---
const initMobileMenu = () => {
    const toggleButtons = document.querySelectorAll('[data-mobile-menu-toggle]');
    const closeButtons = document.querySelectorAll('[data-mobile-menu-close]');
    const drawers = document.querySelectorAll('[data-mobile-menu-drawer]');
    const backdrops = document.querySelectorAll('[data-mobile-menu-backdrop]');

    if (!toggleButtons.length) return;

    const openMenu = () => {
        drawers.forEach(d => {
            d.classList.add('is-open');
            d.setAttribute('aria-hidden', 'false');
        });
        backdrops.forEach(b => b.classList.add('is-open'));
        toggleButtons.forEach(t => t.setAttribute('aria-expanded', 'true'));
        document.body.classList.add('mobile-nav-locked');
    };

    const closeMenu = () => {
        drawers.forEach(d => {
            d.classList.remove('is-open');
            d.setAttribute('aria-hidden', 'true');
        });
        backdrops.forEach(b => b.classList.remove('is-open'));
        toggleButtons.forEach(t => t.setAttribute('aria-expanded', 'false'));
        document.body.classList.remove('mobile-nav-locked');
    };

    toggleButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = drawers[0]?.classList.contains('is-open');
            isOpen ? closeMenu() : openMenu();
        });
    });

    closeButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            closeMenu();
        });
    });

    backdrops.forEach(b => b.addEventListener('click', closeMenu));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawers[0]?.classList.contains('is-open')) {
            closeMenu();
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileMenu);
} else {
    initMobileMenu();
}

// --- Product detail thumbnail switcher ---
document.querySelectorAll('[data-product-thumbnail]').forEach(button => {
    button.addEventListener('click', () => {
        const mainImage = document.getElementById('product-main-image');
        if (!mainImage) return;

        mainImage.src = button.dataset.productThumbnail;
        document.querySelectorAll('[data-product-thumbnail]').forEach(item => item.classList.remove('is-active'));
        button.classList.add('is-active');
    });
});

