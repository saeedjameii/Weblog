import './bootstrap';

document.querySelectorAll('[data-product-thumbnail]').forEach(button => {
    button.addEventListener('click', () => {
        const mainImage = document.getElementById('product-main-image');
        if (!mainImage) return;

        mainImage.src = button.dataset.productThumbnail;
        document.querySelectorAll('[data-product-thumbnail]').forEach(item => item.classList.remove('is-active'));
        button.classList.add('is-active');
    });
});
