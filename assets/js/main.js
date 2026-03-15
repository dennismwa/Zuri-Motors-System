/**
 * Zuri Motors - Main Frontend JavaScript
 */

const BASE_URL = document.querySelector('meta[property="og:url"]')?.content?.replace(/\/[^/]*$/, '') || '';

// ============================================================
// FAVORITES
// ============================================================
function toggleFavorite(carId, btn) {
    fetch(BASE_URL + '/api/favorites', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ car_id: carId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const isFav = data.action === 'added';
            if (btn) {
                btn.classList.toggle('text-red-500', isFav);
                btn.setAttribute('data-fav', isFav ? '1' : '0');
                const icon = btn.querySelector('[data-lucide="heart"]');
                if (icon) {
                    icon.style.fill = isFav ? 'currentColor' : 'none';
                }
            }
        } else {
            if (data.message?.includes('login')) {
                window.location.href = BASE_URL + '/login';
            } else {
                showToast(data.message || 'Error', 'error');
            }
        }
    })
    .catch(() => showToast('Network error', 'error'));
}

// ============================================================
// COMPARE
// ============================================================
function addToCompare(carId) {
    fetch(BASE_URL + '/api/compare', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', car_id: carId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Added to compare list', 'success');
            updateCompareCount(data.count);
        } else {
            showToast(data.message || 'Failed to add', 'error');
        }
    })
    .catch(() => showToast('Network error', 'error'));
}

function removeFromCompare(carId) {
    fetch(BASE_URL + '/api/compare', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove', car_id: carId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Removed from compare', 'success');
            location.reload();
        }
    });
}

function updateCompareCount(count) {
    const badge = document.getElementById('compare-count');
    if (badge) {
        badge.textContent = count;
        badge.classList.toggle('hidden', count === 0);
    }
}

// ============================================================
// TOAST NOTIFICATIONS
// ============================================================
function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-emerald-600',
        error: 'bg-red-600',
        warning: 'bg-amber-600',
        info: 'bg-blue-600'
    };
    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert-triangle',
        info: 'info'
    };

    const toast = document.createElement('div');
    toast.className = `fixed top-20 right-4 z-[200] ${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 text-sm font-medium animate-slide-in max-w-sm`;
    toast.innerHTML = `<i data-lucide="${icons[type] || 'info'}" class="w-5 h-5 flex-shrink-0"></i><span>${escapeHtml(message)}</span>`;
    document.body.appendChild(toast);
    lucide.createIcons();
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        toast.style.transition = 'all 300ms ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================================
// LAZY LOADING IMAGES (fallback)
// ============================================================
if ('IntersectionObserver' in window) {
    const imgObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                imgObserver.unobserve(img);
            }
        });
    }, { rootMargin: '100px' });

    document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
}

// ============================================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ============================================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ============================================================
// FLASH MESSAGE AUTO-DISMISS
// ============================================================
document.querySelectorAll('.flash-message').forEach(msg => {
    setTimeout(() => {
        msg.style.opacity = '0';
        msg.style.transform = 'translateY(-10px)';
        msg.style.transition = 'all 300ms ease';
        setTimeout(() => msg.remove(), 300);
    }, 5000);
});

// ============================================================
// FORM HELPERS
// ============================================================

// Price formatting
document.querySelectorAll('input[data-format="price"]').forEach(input => {
    input.addEventListener('blur', function() {
        const val = parseFloat(this.value.replace(/[^0-9.]/g, ''));
        if (!isNaN(val)) {
            this.value = val.toLocaleString();
        }
    });
    input.addEventListener('focus', function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
});

// Image preview on file input
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files || !input.files[0]) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
}

// ============================================================
// UTILITY FUNCTIONS
// ============================================================

function formatCurrency(amount) {
    return 'KSh ' + Math.round(amount).toLocaleString();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// ============================================================
// PRINT SPECIFIC PAGE
// ============================================================
function printSection(elementId) {
    const content = document.getElementById(elementId);
    if (!content) return;
    
    const win = window.open('', '', 'width=800,height=600');
    win.document.write('<html><head><title>Print</title>');
    win.document.write('<link href="https://cdn.tailwindcss.com" rel="stylesheet">');
    win.document.write('<link href="' + BASE_URL + '/assets/css/style.css" rel="stylesheet">');
    win.document.write('</head><body class="p-8">');
    win.document.write(content.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.onload = () => { win.print(); win.close(); };
}

// ============================================================
// CONFIRM DELETE
// ============================================================
function confirmDelete(message, url) {
    if (confirm(message || 'Are you sure you want to delete this item? This action cannot be undone.')) {
        window.location.href = url;
    }
}

// ============================================================
// INITIALIZE
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    // Re-init lucide after dynamic content
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
