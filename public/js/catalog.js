const cartCountEl = document.getElementById('cart-count');

// console.log('[каталог] страница загружена');

document.querySelectorAll('.product-actions').forEach((wrapper) => {
    const productId = Number(wrapper.dataset.productId);
    const addBtn = wrapper.querySelector('.add-to-cart');
    const minusBtn = wrapper.querySelector('.qty-minus');
    const plusBtn = wrapper.querySelector('.qty-plus');
    const qtyInput = wrapper.querySelector('.catalog-qty-input');

    addBtn.addEventListener('click', () => {
        void changeQuantity(productId, 1, 'add');
    });

    minusBtn.addEventListener('click', () => {
        const next = Math.max(0, Number(qtyInput.value) - 1);
        void changeQuantity(productId, next, 'set');
    });

    plusBtn.addEventListener('click', () => {
        const next = Number(qtyInput.value) + 1;
        void changeQuantity(productId, next, 'set');
    });

    qtyInput.addEventListener('change', () => {
        const next = Number(qtyInput.value);
        if (Number.isNaN(next) || next < 0) {
            return;
        }
        void changeQuantity(productId, next, 'set');
    });
});

async function changeQuantity(productId, quantity, mode) {
    const wrapper = document.querySelector(`.product-actions[data-product-id="${productId}"]`);
    setControlsDisabled(wrapper, true);

    const body = mode === 'add'
        ? { action: 'add_to_cart', product_id: productId, quantity: 1 }
        : { action: 'update_quantity', product_id: productId, quantity };

    // console.log('[API] запрос', body);

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });

        const result = await response.json();
        // console.log('[API] ответ', result);

        if (!result.ok) {
            showToast(result.error || 'Ошибка обновления корзины');
            return;
        }

        // console.log('[корзина] после изменения', result.data);
        cartCountEl.textContent = result.data.item_count;
        updateProductControls(productId, result.data);
    } catch (error) {
        // console.log('[API] ошибка', error);
        showToast('Не удалось связаться с сервером');
    } finally {
        setControlsDisabled(wrapper, false);
    }
}

function updateProductControls(productId, cart) {
    const wrapper = document.querySelector(`.product-actions[data-product-id="${productId}"]`);
    if (!wrapper) {
        return;
    }

    const item = cart.items.find((row) => row.id === productId);
    const quantity = item ? item.quantity : 0;
    const addBtn = wrapper.querySelector('.add-to-cart');
    const qtyControl = wrapper.querySelector('.catalog-qty-control');
    const qtyInput = wrapper.querySelector('.catalog-qty-input');

    if (quantity > 0) {
        addBtn.classList.add('d-none');
        qtyControl.classList.remove('d-none');
        qtyInput.value = quantity;
    } else {
        addBtn.classList.remove('d-none');
        qtyControl.classList.add('d-none');
        qtyInput.value = 0;
    }
}

function setControlsDisabled(wrapper, disabled) {
    wrapper.querySelectorAll('button, input').forEach((el) => {
        el.disabled = disabled;
    });
}

function showToast(message) {
    const toastEl = document.getElementById('toast');
    toastEl.querySelector('.toast-body').textContent = message;
    const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.show();
}
