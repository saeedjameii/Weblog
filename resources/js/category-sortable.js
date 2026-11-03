document.addEventListener('DOMContentLoaded', () => {
    // لیست اصلی که آدرس ذخیره‌ی ترتیب روی آن است
    const container = document.querySelector('[data-reorder-url]');
    if (!container) return;

    const reorderUrl = container.dataset.reorderUrl;
    let draggedItem = null;

    // نمایش پیام وضعیت به کاربر
    const showToast = (message, isError = false) => {
        let toast = document.getElementById('reorder-status-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'reorder-status-toast';
            toast.className = 'reorder-toast';
            document.body.appendChild(toast);
        }
        toast.style.background = isError ? 'var(--danger)' : 'var(--forest)';
        toast.textContent = message;
        toast.style.display = 'flex';

        clearTimeout(toast.hideTimer);
        toast.hideTimer = setTimeout(() => {
            toast.style.display = 'none';
        }, 2500);
    };

    // آیتم‌های سطح اول مستقیم داخل لیست اصلی نیستند (هر کدام داخل یک .spec-row هستند)
    const isRoot = (item) => item.parentElement.closest('.sortable-list') === container;

    // «واحد جابه‌جایی»: برای سطح اول کل ردیف .spec-row، برای زیرمجموعه‌ها خود آیتم
    const unitOf = (item) => (isRoot(item) ? item.closest('.spec-row') : item);

    // هم‌گروه بودن: همه‌ی آیتم‌های سطح اول یک گروه‌اند، فرزندان هر والد هم گروه خودشان
    const groupOf = (item) => (isRoot(item) ? container : item.parentElement);

    // همه‌ی آیتم‌های هم‌گروه (به ترتیب فعلی در صفحه)
    const siblingsOf = (item) => {
        if (isRoot(item)) {
            return [...container.children]
                .map((row) => row.querySelector('.sortable-item'))
                .filter(Boolean);
        }
        return [...item.parentElement.children].filter((child) => child.classList.contains('sortable-item'));
    };

    const clearIndicators = () => {
        container.querySelectorAll('.drop-target-above, .drop-target-below').forEach((el) => {
            el.classList.remove('drop-target-above', 'drop-target-below');
        });
    };

    const cleanup = () => {
        container.querySelectorAll('.sortable-item').forEach((el) => {
            el.removeAttribute('draggable');
            el.classList.remove('is-dragging');
        });
        clearIndicators();
        draggedItem = null;
    };

    // پیدا کردن آیتمِ هم‌گروه زیر موس (حتی اگر موس روی زیرمجموعه‌های آن باشد)
    const findTarget = (element) => {
        let node = element.closest('.sortable-item');
        while (node) {
            if (node === draggedItem) return null;
            if (groupOf(node) === groupOf(draggedItem)) return node;
            node = node.parentElement ? node.parentElement.closest('.sortable-item') : null;
        }
        return null;
    };

    // موس در نیمه‌ی بالایی ردیفِ عنوان است یا پایینی؟
    const isAbove = (event, target) => {
        const header = target.querySelector('.category-node-name') || target;
        const rect = header.getBoundingClientRect();
        return event.clientY < rect.top + rect.height / 2;
    };

    const saveOrder = async (orderedIds) => {
        showToast('در حال ذخیره ترتیب جدید...');

        try {
            const response = await window.axios.post(reorderUrl, { order: orderedIds });

            if (response.data.status === 'success') {
                showToast('ترتیب دسته‌بندی‌ها ذخیره شد ✓');
            }
        } catch (error) {
            const status = error.response ? error.response.status : '؟';
            showToast(`خطا در ذخیره ترتیب (کد ${status})`, true);
            console.error(error);
        }
    };

    // فقط با گرفتن دستگیره اجازه‌ی کشیدن بده
    container.addEventListener('mousedown', (e) => {
        const handle = e.target.closest('.drag-handle');
        if (!handle) return;
        handle.closest('.sortable-item').setAttribute('draggable', 'true');
    });

    // اگر فقط کلیک شد و کشیدن شروع نشد، حالت draggable را برگردان
    document.addEventListener('mouseup', () => {
        if (!draggedItem) {
            container.querySelectorAll('.sortable-item[draggable]').forEach((el) => el.removeAttribute('draggable'));
        }
    });

    container.addEventListener('dragstart', (e) => {
        const item = e.target.closest ? e.target.closest('.sortable-item') : null;
        if (!item || item.getAttribute('draggable') !== 'true') return;

        draggedItem = item;
        item.classList.add('is-dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', item.dataset.id);
    });

    container.addEventListener('dragover', (e) => {
        if (!draggedItem) return;

        const target = findTarget(e.target);
        if (!target) return;

        e.preventDefault();
        clearIndicators();
        target.classList.add(isAbove(e, target) ? 'drop-target-above' : 'drop-target-below');
    });

    container.addEventListener('drop', async (e) => {
        if (!draggedItem) return;

        const target = findTarget(e.target);
        if (!target) return;

        e.preventDefault();

        const moved = draggedItem;
        const movingUnit = unitOf(moved);
        const targetUnit = unitOf(target);

        if (isAbove(e, target)) {
            targetUnit.before(movingUnit);
        } else {
            targetUnit.after(movingUnit);
        }

        // آیدی‌ها را همین الان (بعد از جابه‌جایی) بخوان، قبل از پاک‌سازی
        const orderedIds = siblingsOf(moved).map((el) => Number(el.dataset.id));

        cleanup();
        await saveOrder(orderedIds);
    });

    container.addEventListener('dragend', cleanup);
});
