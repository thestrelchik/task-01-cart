const cartItemsEl = document.getElementById('cart-items');
const cartTotalEl = document.getElementById('cart-total');
const clearCartBtn = document.getElementById('clear-cart-btn');
const checkoutForm = document.getElementById('checkout-form');
const checkoutError = document.getElementById('checkout-error');
const checkoutSuccess = document.getElementById('checkout-success');
const nameInput = document.getElementById('customer-name');
const emailInput = document.getElementById('customer-email');

const NAME_PATTERN = /^\p{L}+(?:\s+\p{L}+)*$/u;
const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// console.log('[корзина] страница загружена');

nameInput.addEventListener('input', () => {
    nameInput.value = nameInput.value.replace(/[^\p{L}\s]/gu, '').replace(/\s{2,}/g, ' ');
    if (nameInput.classList.contains('is-invalid')) {
        validateCheckoutFields();
    }
});

nameInput.addEventListener('blur', () => {
    validateCheckoutFields();
});

emailInput.addEventListener('input', () => {
    emailInput.value = emailInput.value.replace(/[,\s]/g, '');
    if (emailInput.classList.contains('is-invalid')) {
        validateCheckoutFields();
    }
});

emailInput.addEventListener('blur', () => {
    validateCheckoutFields();
});

cartItemsEl.addEventListener('change', async (event) => {
    const input = event.target;
    if (!input.classList.contains('quantity-input')) {
        return;
    }

    const productId = Number(input.dataset.productId);
    const quantity = Number(input.value);

    if (Number.isNaN(quantity) || quantity < 0) {
        return;
    }

    input.disabled = true;

    try {
        const result = await apiRequest({
            action: 'update_quantity',
            product_id: productId,
            quantity,
        });

        if (!result.ok) {
            alert(result.error || 'Ошибка обновления корзины');
            return;
        }

        renderCart(result.data);
    } catch (error) {
        alert('Не удалось связаться с сервером');
    } finally {
        input.disabled = false;
    }
});

clearCartBtn.addEventListener('click', async () => {
    clearCartBtn.disabled = true;

    try {
        const result = await apiRequest({ action: 'clear_cart' });

        if (!result.ok) {
            alert(result.error || 'Не удалось очистить корзину');
            return;
        }

        renderCart(result.data);
        checkoutError.classList.add('d-none');
        checkoutSuccess.classList.add('d-none');
    } catch (error) {
        alert('Не удалось связаться с сервером');
    } finally {
        clearCartBtn.disabled = false;
    }
});

checkoutForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    checkoutError.classList.add('d-none');
    checkoutSuccess.classList.add('d-none');

    const fieldErrors = validateCheckoutFields();
    if (Object.keys(fieldErrors).length > 0) {
        return;
    }

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();

    try {
        const result = await apiRequest({ action: 'checkout', name, email });

        if (!result.ok) {
            if (result.errors) {
                showFieldErrors(result.errors);
                return;
            }

            checkoutError.textContent = result.error || 'Ошибка оформления заказа';
            checkoutError.classList.remove('d-none');
            return;
        }

        checkoutSuccess.textContent = result.data.message;
        checkoutSuccess.classList.remove('d-none');
        checkoutForm.reset();
        clearFieldErrors();
        renderCart({ items: [], total_formatted: '0,00 BYN', item_count: 0 });
    } catch (error) {
        checkoutError.textContent = 'Не удалось связаться с сервером';
        checkoutError.classList.remove('d-none');
    }
});

function validateCheckoutFields() {
    const errors = {};
    const name = nameInput.value.trim();
    const email = emailInput.value.trim();

    if (name === '') {
        errors.name = 'Имя обязательно для заполнения.';
    } else if (!NAME_PATTERN.test(name)) {
        errors.name = 'Имя может содержать только буквы (без цифр, запятых и других знаков).';
    }

    if (email === '') {
        errors.email = 'Email обязателен для заполнения.';
    } else if (!EMAIL_PATTERN.test(email)) {
        errors.email = 'Укажите корректный email.';
    }

    showFieldErrors(errors);
    return errors;
}

function showFieldErrors(errors) {
    clearFieldErrors();

    const fields = {
        name: nameInput,
        email: emailInput,
    };

    Object.entries(fields).forEach(([key, input]) => {
        const message = errors[key];
        const feedback = document.getElementById(`${key}-error`);

        if (message) {
            input.classList.add('is-invalid');
            feedback.textContent = message;
        }
    });
}

async function apiRequest(body) {
    // console.log('[API] запрос', body);

    const response = await fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });

    const result = await response.json();
    // console.log('[API] ответ', result);

    return result;
}

function renderCart(cart) {
    cartTotalEl.textContent = cart.total_formatted || '0,00 BYN';

    if (!cart.items || cart.items.length === 0) {
        cartItemsEl.innerHTML = '<p class="text-muted" id="empty-cart-message">Корзина пуста. <a href="index.php">Перейти к товарам</a></p>';
        clearCartBtn.classList.add('d-none');
        return;
    }

    clearCartBtn.classList.remove('d-none');

    cartItemsEl.innerHTML = cart.items.map((item) => `
        <article class="cart-item row g-3 align-items-center border-bottom py-3" data-product-id="${item.id}">
            <div class="col-auto">
                <img src="${escapeHtml(item.image_path)}" alt="${escapeHtml(item.title)}" class="cart-item-image">
            </div>
            <section class="col">
                <h3 class="h6 mb-1">${escapeHtml(item.title)}</h3>
                <p class="mb-1 text-muted">Цена: <span class="item-price">${escapeHtml(item.price_formatted)}</span></p>
                <label class="d-inline-flex align-items-center gap-2">
                    Количество:
                    <input type="number" class="form-control form-control-sm quantity-input" min="0"
                           value="${item.quantity}" data-product-id="${item.id}">
                </label>
                <p class="mb-0 mt-2">Стоимость: <strong class="line-total">${escapeHtml(item.line_total_formatted)}</strong></p>
            </section>
        </article>
    `).join('');
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function clearFieldErrors() {
    [nameInput, emailInput].forEach((input) => {
        input.classList.remove('is-invalid');
    });

    ['name', 'email'].forEach((key) => {
        document.getElementById(`${key}-error`).textContent = '';
    });
}
