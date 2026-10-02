/**
 * US BENGOL - AJAX Form Handlers & Notification Toasts
 */

(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    initBulkOrderForm();
    initContactForm();
  });

  // Bulk Order Form Handler
  function initBulkOrderForm() {
    const bulkForm = document.getElementById('bulk-order-form');
    if (!bulkForm) return;

    bulkForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearFormErrors(bulkForm);

      const submitBtn = bulkForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Submit Bulk Request';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
          <svg style="animation: spin 1s linear infinite; width:18px; height:18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" stroke-width="3" stroke-dasharray="32" stroke-dashoffset="12"></circle>
          </svg>
          Submitting...
        `;
      }

      const formData = new FormData(bulkForm);

      try {
        const response = await fetch('api/submit-bulk.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (response.ok && result.success) {
          // Show polished modal success state
          const modalBody = bulkForm.closest('.modal-body');
          if (modalBody) {
            modalBody.innerHTML = `
              <div style="text-align: center; padding: 24px 0;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #E8F5E9; color: #2E7D32; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                  <svg style="width: 36px; height: 36px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
                <h3 style="font-size: 1.6rem; font-weight: 800; color: var(--color-brown-900); margin-bottom: 12px;">Request Received</h3>
                <p style="font-size: 1.05rem; color: var(--color-text-body); max-width: 440px; margin: 0 auto 28px auto; line-height: 1.6;">
                  ${escapeHtml(result.message)}
                </p>
                <div style="display: inline-flex; gap: 12px;">
                  <button type="button" class="btn btn-primary" onclick="location.reload();">Done</button>
                  <a href="https://wa.me/8801921692008?text=Hello%20US%20BENGOL,%20I%20have%20submitted%20a%20bulk%20order%20request." target="_blank" class="btn btn-whatsapp">
                    Follow up on WhatsApp
                  </a>
                </div>
              </div>
            `;
          }
          showToast('success', 'Bulk order request submitted successfully!');
        } else {
          if (result.errors) {
            applyFormErrors(bulkForm, result.errors);
          }
          showToast('error', result.message || 'Please check the highlighted fields.');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }
      } catch (err) {
        console.error('Bulk submission error:', err);
        showToast('error', 'Connection issue. Please contact us via WhatsApp: 01921692008');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  }

  // Contact Form Handler
  function initContactForm() {
    const contactForm = document.getElementById('general-contact-form');
    if (!contactForm) return;

    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearFormErrors(contactForm);

      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Send Message';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Sending...';
      }

      const formData = new FormData(contactForm);

      try {
        const response = await fetch('api/submit-contact.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (response.ok && result.success) {
          contactForm.reset();
          showToast('success', result.message);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Message Sent ✓';
            setTimeout(() => {
              submitBtn.innerHTML = originalBtnText;
            }, 3500);
          }
        } else {
          if (result.errors) {
            applyFormErrors(contactForm, result.errors);
          }
          showToast('error', result.message || 'Please correct errors in the form.');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }
      } catch (err) {
        console.error('Contact submission error:', err);
        showToast('error', 'Unable to send message right now. Call us at 01921692008.');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  }

  function applyFormErrors(form, errors) {
    for (const [field, msg] of Object.entries(errors)) {
      const input = form.querySelector(`[name="${field}"]`);
      if (input) {
        const group = input.closest('.form-group');
        if (group) {
          group.classList.add('has-error');
          let errorElem = group.querySelector('.form-error-msg');
          if (!errorElem) {
            errorElem = document.createElement('div');
            errorElem.className = 'form-error-msg';
            group.appendChild(errorElem);
          }
          errorElem.textContent = msg;
          errorElem.style.display = 'block';
        }
      }
    }
  }

  function clearFormErrors(form) {
    form.querySelectorAll('.form-group.has-error').forEach(g => {
      g.classList.remove('has-error');
      const errorElem = g.querySelector('.form-error-msg');
      if (errorElem) errorElem.style.display = 'none';
    });
  }

  // Toast System
  function showToast(type, message) {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-main);">
        ${escapeHtml(message)}
      </div>
    `;

    container.appendChild(toast);

    // Trigger reveal
    requestAnimationFrame(() => {
      toast.classList.add('show');
    });

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 400);
    }, 4500);
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

  window.showToast = showToast;
})();
