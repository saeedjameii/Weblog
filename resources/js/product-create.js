const productForm = document.querySelector('[data-product-form]');

if (productForm) {
  const priceList = productForm.querySelector('[data-price-list]');
  const template = document.querySelector('[data-price-template]');
  const addButton = productForm.querySelector('[data-add-price]');
  const numberFormat = new Intl.NumberFormat('fa-IR');

  if (priceList && template && addButton) {
    let nextIndex = priceList.querySelectorAll('[data-price-row]').length;

    const updatePrice = row => {
      const amountInput = row.querySelector('[data-price-amount]');
      const discountInput = row.querySelector('[data-price-discount]');
      const finalPrice = row.querySelector('[data-final-price]');
      if (!amountInput || !finalPrice) return;

      const amount = Number(amountInput.value);
      const discount = Number(discountInput?.value || 0);

      finalPrice.textContent = Number.isFinite(amount) && amount >= 0 && discount >= 0 && discount <= 100
        ? numberFormat.format(Math.round((amount * (1 - discount / 100) + Number.EPSILON) * 100) / 100)
        : '—';
    };

    const refreshPrices = () => {
      const rows = [...priceList.querySelectorAll('[data-price-row]')];
      rows.forEach((row, index) => {
        const num = row.querySelector('[data-price-number]');
        if (num) num.textContent = numberFormat.format(index + 1);
        const rm = row.querySelector('[data-remove-price]');
        if (rm) rm.disabled = rows.length === 1;
        updatePrice(row);
      });
    };

    priceList.addEventListener('input', event => {
      if (event.target.matches('[data-price-amount], [data-price-discount]')) {
        updatePrice(event.target.closest('[data-price-row]'));
      }
    });

    priceList.addEventListener('click', event => {
      const button = event.target.closest('[data-remove-price]');
      if (!button || priceList.querySelectorAll('[data-price-row]').length <= 1) return;
      button.closest('[data-price-row]').remove();
      refreshPrices();
    });

    addButton.addEventListener('click', () => {
      const row = template.content.cloneNode(true);
      row.querySelectorAll('[name]').forEach(input => {
        input.name = input.name.replaceAll('__INDEX__', String(nextIndex++));
      });
      priceList.append(row);
      refreshPrices();
    });

    refreshPrices();
  }

  // --- Image Upload Preview & Drag-and-Drop ---
  const imageInput = productForm.querySelector('[data-image-input]');
  const previewContainer = productForm.querySelector('[data-image-preview]');
  const dropzone = productForm.querySelector('[data-file-dropzone]');

  if (imageInput && previewContainer) {
    let createdUrls = [];

    const formatFileSize = bytes => {
      if (bytes < 1024) return bytes + ' بایت';
      if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' کیلوبایت';
      return (bytes / 1048576).toFixed(1) + ' مگابایت';
    };

    const renderPreviews = files => {
      createdUrls.forEach(url => URL.revokeObjectURL(url));
      createdUrls = [];
      previewContainer.innerHTML = '';

      if (!files || files.length === 0) return;

      Array.from(files).slice(0, 5).forEach((file, index) => {
        if (!file.type.startsWith('image/')) return;

        const objectUrl = URL.createObjectURL(file);
        createdUrls.push(objectUrl);

        const card = document.createElement('div');
        card.className = 'image-preview-card';

        const thumb = document.createElement('img');
        thumb.className = 'image-preview-thumb';
        thumb.src = objectUrl;
        thumb.alt = file.name;

        const badge = document.createElement('span');
        badge.className = 'image-preview-badge';
        badge.textContent = `تصویر ${numberFormat.format(index + 1)}`;

        const meta = document.createElement('div');
        meta.className = 'image-preview-meta';

        const name = document.createElement('span');
        name.className = 'image-preview-name';
        name.title = file.name;
        name.textContent = file.name;

        const size = document.createElement('span');
        size.className = 'image-preview-size';
        size.textContent = formatFileSize(file.size);

        meta.appendChild(name);
        meta.appendChild(size);

        card.appendChild(thumb);
        card.appendChild(badge);
        card.appendChild(meta);
        previewContainer.appendChild(card);
      });
    };

    imageInput.addEventListener('change', () => {
      renderPreviews(imageInput.files);
    });

    if (dropzone) {
      ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, e => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.add('is-dragover');
        });
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, e => {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.remove('is-dragover');
        });
      });

      dropzone.addEventListener('drop', e => {
        if (e.dataTransfer && e.dataTransfer.files.length) {
          imageInput.files = e.dataTransfer.files;
          renderPreviews(imageInput.files);
        }
      });
    }
  }
}

