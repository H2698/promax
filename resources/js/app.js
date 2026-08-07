import './bootstrap';

const WISHLIST_KEY = 'power_wishlist';

function getWishlist() {
    try {
        return JSON.parse(localStorage.getItem(WISHLIST_KEY) || '[]');
    } catch {
        return [];
    }
}

function paintWishlistButtons() {
    const ids = getWishlist();
    document.querySelectorAll('[data-wishlist-toggle]').forEach((btn) => {
        const id = Number(btn.dataset.wishlistToggle);
        btn.classList.toggle('is-wishlisted', ids.includes(id));
        btn.style.color = ids.includes(id) ? '#F4B321' : '';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-search-toggle]');
    const panel = document.querySelector('[data-search-panel]');

    if (toggle && panel) {
        toggle.addEventListener('click', () => {
            panel.hidden = !panel.hidden;
            if (!panel.hidden) {
                panel.querySelector('input')?.focus();
            }
        });
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-wishlist-toggle]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const id = Number(btn.dataset.wishlistToggle);
        const ids = getWishlist();
        const next = ids.includes(id) ? ids.filter((i) => i !== id) : [...ids, id];
        localStorage.setItem(WISHLIST_KEY, JSON.stringify(next));
        paintWishlistButtons();
    });

    paintWishlistButtons();

    const navToggle = document.querySelector('[data-admin-nav-toggle]');
    const sidebar = document.querySelector('[data-admin-sidebar]');

    if (navToggle && sidebar) {
        navToggle.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });
    }
});
