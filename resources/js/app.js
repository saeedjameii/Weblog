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

// Add products to the user's comparison list.

document.querySelectorAll('[data-compare-button]').forEach(button => {
    button.addEventListener('click', async () => {
        if (button.disabled) return;

        const label = button.querySelector('span');
        const originalLabel = label?.textContent;

        button.disabled = true;

        if (label) label.textContent = 'در حال افزودن…';

        try {
            const response = await fetch(button.dataset.compareUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    product_id: button.dataset.productId
                }),
            });

            const result = await response.json();

            if (!response.ok || result.error) {
                let message = 'افزودن محصول انجام نشد.';

                switch (response.status) {
                    case 401:
                        message = 'برای استفاده از این قابلیت ابتدا وارد حساب کاربری شوید.';
                        break;

                    case 403:
                        message = 'برای استفاده از این قابلیت ابتدا ایمیل خود را تأیید کنید.';
                        break;

                    case 422:
                        message = result.message || 'اطلاعات ارسالی معتبر نیست.';
                        break;

                    default:
                        message = 'خطایی رخ داد. لطفاً دوباره تلاش کنید.';
                }

                throw new Error(message);
            }

            if (label) label.textContent = 'به مقایسه اضافه شد';

            button.classList.add('is-added');

            button.setAttribute(
                'aria-label',
                'محصول به لیست مقایسه اضافه شد'
            );

        } catch (error) {
            if (label) {
                label.textContent = error.message || 'خطا در افزودن';

                window.setTimeout(() => {
                    label.textContent = originalLabel;
                }, 2500);
            }

        } finally {
            button.disabled = false;
        }
    });
});

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

