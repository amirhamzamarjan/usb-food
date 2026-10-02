/**
 * US BENGOL - Core Application JavaScript
 */

(function () {
  'use strict';

  // State
  let productsData = [];
  let currentProduct = null;
  let currentQuantity = 1;

  // DOM Elements
  const header = document.querySelector('.site-header');
  const mobileToggle = document.querySelector('.mobile-toggle');
  const mobileDrawer = document.querySelector('.mobile-drawer');
  const drawerBackdrop = document.querySelector('.drawer-backdrop');
  const drawerClose = document.querySelector('.drawer-close');
  const drawerLinks = document.querySelectorAll('.drawer-link');

  // Modals
  const productModal = document.getElementById('product-modal');
  const bulkModal = document.getElementById('bulk-modal');
  const modalOverlays = document.querySelectorAll('.modal-overlay');
  const modalCloseBtns = document.querySelectorAll('.modal-close-btn');

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    initScrollHeader();
    initMobileNav();
    initModals();
    initProductFilters();
    initProductQuantity();
    loadInitialProducts();
  });

  // Sticky Navbar Scroll Effect
  function initScrollHeader() {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 40) {
        header?.classList.add('scrolled');
      } else {
        header?.classList.remove('scrolled');
      }
    }, { passive: true });
  }

  // Mobile Navigation Drawer
  function initMobileNav() {
    if (!mobileToggle || !mobileDrawer || !drawerBackdrop) return;

    const openDrawer = () => {
      mobileDrawer.classList.add('open');
      drawerBackdrop.classList.add('open');
      document.body.style.overflow = 'hidden';
    };

    const closeDrawer = () => {
      mobileDrawer.classList.remove('open');
      drawerBackdrop.classList.remove('open');
      document.body.style.overflow = '';
    };

    mobileToggle.addEventListener('click', openDrawer);
    drawerClose?.addEventListener('click', closeDrawer);
    drawerBackdrop.addEventListener('click', closeDrawer);

    drawerLinks.forEach(link => {
      link.addEventListener('click', closeDrawer);
    });
  }

  // Modal System
  function initModals() {
    // Global close on backdrop or button
    modalCloseBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        const modal = e.target.closest('.modal-overlay');
        if (modal) closeModal(modal);
      });
    });

    modalOverlays.forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          closeModal(overlay);
        }
      });
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        modalOverlays.forEach(modal => {
          if (modal.classList.contains('active')) {
            closeModal(modal);
          }
        });
      }
    });

    // Bulk Modal Triggers
    const bulkTriggers = document.querySelectorAll('.trigger-bulk-modal');
    bulkTriggers.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const productName = btn.getAttribute('data-product') || '';
        openBulkModal(productName);
      });
    });
  }

  function openModal(modal) {
    if (!modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }

  function openBulkModal(preselectedProduct = '') {
    if (bulkModal) {
      const selectElem = document.getElementById('bulk-product-select');
      if (selectElem && preselectedProduct) {
        selectElem.value = preselectedProduct;
      }
      openModal(bulkModal);
    }
  }

  // Load Products from API (or fallback to embedded DOM products)
  async function loadInitialProducts() {
    try {
      const res = await fetch('api/products.php');
      if (res.ok) {
        const data = await res.json();
        if (data.success && Array.isArray(data.products)) {
          productsData = data.products;
          renderProductGrid(productsData);
          populateBulkProductOptions(productsData);
        }
      }
    } catch (err) {
      console.warn('API fetch products fallback:', err);
    }
  }

  function populateBulkProductOptions(products) {
    const selectElem = document.getElementById('bulk-product-select');
    if (!selectElem) return;

    // Keep default prompt
    selectElem.innerHTML = '<option value="">Select a Product</option>';
    products.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.name;
      opt.textContent = p.name + (p.name_bn ? ` (${p.name_bn})` : '');
      selectElem.appendChild(opt);
    });
    const generalOpt = document.createElement('option');
    generalOpt.value = 'Multiple Products / Custom Batch';
    generalOpt.textContent = 'Multiple Products / Custom Batch';
    selectElem.appendChild(generalOpt);
  }

  // Filter Products by Category Tabs
  function initProductFilters() {
    const filterBtns = document.querySelectorAll('.filter-tab-btn');
    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const category = btn.getAttribute('data-category');

        if (category === 'all') {
          renderProductGrid(productsData);
        } else {
          const filtered = productsData.filter(p => p.category === category);
          renderProductGrid(filtered);
        }
      });
    });
  }

  // Render Product Grid Cards
  function renderProductGrid(products) {
    const gridContainer = document.getElementById('products-grid-container');
    if (!gridContainer) return;

    if (!products || products.length === 0) {
      gridContainer.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 48px; background: #FFF; border-radius: 16px; border: 1px solid var(--color-border);">
          <h4 style="margin-bottom: 8px;">No products found in this category</h4>
          <p class="text-muted">Please select another category tab above.</p>
        </div>
      `;
      return;
    }

    gridContainer.innerHTML = products.map(product => {
      const isDietary = product.category === 'dietary';
      const badgeClass = isDietary ? 'dietary' : (product.badge?.toLowerCase().includes('pure') ? 'gold' : '');
      const priceDisplay = (parseFloat(product.price) > 0) ? product.formatted_price : '৳0.00';

      return `
        <div class="product-card" data-id="${product.id}" data-slug="${product.slug}">
          <div class="product-card-media">
            ${product.badge ? `<span class="product-card-badge ${badgeClass}">${escapeHtml(product.badge)}</span>` : ''}
            <img src="${escapeHtml(product.image || 'images/ataruti.png')}" alt="${escapeHtml(product.name)}" class="product-card-img" loading="lazy">
          </div>
          <div class="product-card-body">
            <div class="product-header-row">
              <div>
                <h3 class="product-title">${escapeHtml(product.name)}</h3>
                ${product.name_bn ? `<span class="product-title-bn">${escapeHtml(product.name_bn)}</span>` : ''}
              </div>
              <div class="product-price-box">
                <span class="product-price">${priceDisplay}</span>
                <span class="product-unit">${escapeHtml(product.unit || 'pack')}</span>
              </div>
            </div>
            <p class="product-desc">${escapeHtml(product.short_description)}</p>
            <div class="product-card-footer">
              <button type="button" class="btn btn-outline btn-sm btn-quick-view" data-id="${product.id}">
                View Details
              </button>
              <a href="${product.whatsapp_link}" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                Order
              </a>
            </div>
          </div>
        </div>
      `;
    }).join('');

    // Attach Click Handlers for Quick View
    gridContainer.querySelectorAll('.btn-quick-view').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = parseInt(btn.getAttribute('data-id'), 10);
        openProductModalById(id);
      });
    });
  }

  // Open Product Modal
  function openProductModalById(id) {
    const product = productsData.find(p => p.id === id);
    if (!product) return;

    currentProduct = product;
    currentQuantity = 1;

    // Populate Modal Elements
    document.getElementById('modal-product-img').src = product.image || 'images/ataruti.png';
    document.getElementById('modal-product-title').textContent = product.name;
    document.getElementById('modal-product-title-bn').textContent = product.name_bn || '';
    document.getElementById('modal-product-badge').textContent = product.badge || 'US BENGOL';
    document.getElementById('modal-product-price').textContent = (parseFloat(product.price) > 0) ? product.formatted_price : '৳0.00';
    document.getElementById('modal-product-unit').textContent = product.unit || 'pack';
    document.getElementById('modal-product-desc').textContent = product.description || product.short_description;

    // Characteristics Tags
    const tagsContainer = document.getElementById('modal-product-tags');
    if (tagsContainer) {
      const chars = Array.isArray(product.characteristics) ? product.characteristics : [];
      tagsContainer.innerHTML = chars.map(tag => `
        <span style="display:inline-flex; align-items:center; gap:6px; background:#FAF6EE; border:1px solid var(--color-border); padding:4px 12px; border-radius:999px; font-size:0.8rem; font-weight:700; color:var(--color-brown-800);">
          <span style="color:var(--color-green-deep);">✓</span> ${escapeHtml(tag)}
        </span>
      `).join('');
    }

    updateModalQuantityAndLink();
    openModal(productModal);
  }

  // Quantity Counter & WhatsApp Link Updater
  function initProductQuantity() {
    const decBtn = document.getElementById('qty-dec-btn');
    const incBtn = document.getElementById('qty-inc-btn');
    const qtyInput = document.getElementById('modal-qty-input');

    decBtn?.addEventListener('click', () => {
      if (currentQuantity > 1) {
        currentQuantity--;
        if (qtyInput) qtyInput.value = currentQuantity;
        updateModalQuantityAndLink();
      }
    });

    incBtn?.addEventListener('click', () => {
      currentQuantity++;
      if (qtyInput) qtyInput.value = currentQuantity;
      updateModalQuantityAndLink();
    });

    qtyInput?.addEventListener('change', () => {
      const val = parseInt(qtyInput.value, 10);
      currentQuantity = (!isNaN(val) && val >= 1) ? val : 1;
      qtyInput.value = currentQuantity;
      updateModalQuantityAndLink();
    });
  }

  function updateModalQuantityAndLink() {
    const qtyInput = document.getElementById('modal-qty-input');
    if (qtyInput) qtyInput.value = currentQuantity;

    const orderBtn = document.getElementById('modal-whatsapp-btn');
    if (orderBtn && currentProduct) {
      const unitPrice = parseFloat(currentProduct.price) || 0;
      const calculatedTotal = unitPrice * currentQuantity;

      let msg = `Hello US BENGOL,\nI would like to place an order for:\n• Product: ${currentProduct.name}\n• Quantity: ${currentQuantity} ${currentProduct.unit || 'pack(s)'}`;
      if (calculatedTotal > 0) {
        msg += `\n• Estimated Price: ৳${calculatedTotal.toFixed(2)}`;
      }
      msg += `\n\nPlease confirm availability and delivery details.`;

      orderBtn.href = `https://wa.me/8801921692008?text=${encodeURIComponent(msg)}`;
    }
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Expose global modal opener for buttons
  window.openBulkModal = openBulkModal;
  window.openProductModalById = openProductModalById;

})();
