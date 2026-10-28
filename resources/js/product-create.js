const productForm = document.querySelector('[data-product-form]');

if (productForm) {
  const priceList = productForm.querySelector('[data-price-list]');
  const template = document.querySelector('[data-price-template]');
  const addButton = productForm.querySelector('[data-add-price]');
  const numberFormat = new Intl.NumberFormat('fa-IR');
  let nextIndex = priceList.querySelectorAll('[data-price-row]').length;

  const updatePrice = row => {
    const amount = Number(row.querySelector('[data-price-amount]').value);
    const discount = Number(row.querySelector('[data-price-discount]').value || 0);
    const finalPrice = row.querySelector('[data-final-price]');
    finalPrice.textContent = Number.isFinite(amount) && amount >= 0 && discount >= 0 && discount <= 100
      ? numberFormat.format(Math.round((amount * (1 - discount / 100) + Number.EPSILON) * 100) / 100)
      : '—';
  };

  const refreshPrices = () => {
    const rows = [...priceList.querySelectorAll('[data-price-row]')];
    rows.forEach((row, index) => {
      row.querySelector('[data-price-number]').textContent = numberFormat.format(index + 1);
      row.querySelector('[data-remove-price]').disabled = rows.length === 1;
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
