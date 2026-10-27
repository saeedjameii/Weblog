const productForm = document.querySelector('[data-product-form]');

if (productForm) {
  const variantList = productForm.querySelector('[data-variant-list]');
  const template = document.querySelector('[data-variant-template]');
  const attributeInputs = [...productForm.querySelectorAll('[data-product-attribute]')];
  const addButton = productForm.querySelector('[data-add-variant]');
  const numberFormat = new Intl.NumberFormat('fa-IR');
  let nextIndex = variantList.querySelectorAll('[data-variant-row]').length;

  const selectedAttributeIds = () => new Set(
    attributeInputs.filter(input => input.checked).map(input => input.value),
  );

  const updateRow = row => {
    const selected = selectedAttributeIds();
    row.querySelectorAll('[data-variant-attribute]').forEach(field => {
      const enabled = selected.has(field.dataset.variantAttribute);
      field.hidden = !enabled;
      const select = field.querySelector('[data-variant-value]');
      select.disabled = !enabled;
      select.required = enabled;
      if (!enabled) select.value = '';
    });

    const price = Number(row.querySelector('[data-variant-price]').value);
    const discount = Number(row.querySelector('[data-variant-discount]').value || 0);
    const finalPrice = row.querySelector('[data-final-price]');
    finalPrice.textContent = Number.isFinite(price) && price >= 0 && discount >= 0 && discount <= 100
      ? numberFormat.format(Math.round((price * (1 - discount / 100) + Number.EPSILON) * 100) / 100)
      : '?';
  };

  const refreshRows = () => {
    const rows = [...variantList.querySelectorAll('[data-variant-row]')];
    rows.forEach((row, index) => {
      row.querySelector('[data-variant-number]').textContent = numberFormat.format(index + 1);
      const removeButton = row.querySelector('[data-remove-variant]');
      removeButton.disabled = rows.length === 1;
      updateRow(row);
    });
  };

  variantList.addEventListener('input', event => {
    if (event.target.matches('[data-variant-price], [data-variant-discount]')) {
      updateRow(event.target.closest('[data-variant-row]'));
    }
  });

  variantList.addEventListener('click', event => {
    const button = event.target.closest('[data-remove-variant]');
    if (!button || variantList.querySelectorAll('[data-variant-row]').length <= 1) return;
    button.closest('[data-variant-row]').remove();
    refreshRows();
  });

  attributeInputs.forEach(input => input.addEventListener('change', refreshRows));
  addButton.addEventListener('click', () => {
    const index = nextIndex++;
    const row = template.content.cloneNode(true);
    row.querySelectorAll('[name]').forEach(input => {
      input.name = input.name.replaceAll('__INDEX__', String(index));
    });
    variantList.append(row);
    refreshRows();
  });

  refreshRows();
}
