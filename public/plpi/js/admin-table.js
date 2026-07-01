document.addEventListener('DOMContentLoaded', function () {
    const confirmModal = document.createElement('div');
    confirmModal.className = 'admin-modal admin-confirm-modal';
    confirmModal.setAttribute('aria-hidden', 'true');
    confirmModal.innerHTML = [
        '<div class="admin-modal-backdrop" data-confirm-cancel></div>',
        '<section class="admin-modal-dialog admin-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="adminConfirmTitle">',
        '  <div class="admin-confirm-icon"><iconify-icon icon="mdi:alert-circle-outline"></iconify-icon></div>',
        '  <div class="admin-confirm-copy">',
        '    <span>Konfirmasi</span>',
        '    <h2 id="adminConfirmTitle">Lanjutkan aksi?</h2>',
        '    <p data-confirm-message>Apakah Anda yakin ingin melanjutkan?</p>',
        '  </div>',
        '  <div class="admin-confirm-actions">',
        '    <button class="admin-btn secondary" type="button" data-confirm-cancel><iconify-icon icon="mdi:close"></iconify-icon>Batal</button>',
        '    <button class="admin-btn danger" type="button" data-confirm-ok><iconify-icon icon="mdi:check"></iconify-icon>Ya, lanjutkan</button>',
        '  </div>',
        '</section>'
    ].join('');
    document.body.appendChild(confirmModal);

    const confirmMessage = confirmModal.querySelector('[data-confirm-message]');
    const confirmOk = confirmModal.querySelector('[data-confirm-ok]');
    const confirmCancel = confirmModal.querySelector('[data-confirm-cancel]');
    let pendingConfirm = null;

    function decodeConfirmMessage(message) {
        const textarea = document.createElement('textarea');
        textarea.innerHTML = message || '';
        return textarea.value.replace(/\\'/g, "'").replace(/\\"/g, '"').trim();
    }

    function extractConfirmMessage(handlerText) {
        const match = String(handlerText || '').match(/confirm\((['"])([\s\S]*?)\1\)/);
        return match ? decodeConfirmMessage(match[2]) : '';
    }

    document.querySelectorAll('form[onsubmit*="confirm("]').forEach(function (form) {
        const message = extractConfirmMessage(form.getAttribute('onsubmit'));
        if (message && !form.dataset.confirm) {
            form.dataset.confirm = message;
        }
        form.removeAttribute('onsubmit');
    });

    function openConfirm(message) {
        if (pendingConfirm) {
            pendingConfirm(false);
            pendingConfirm = null;
        }

        confirmMessage.textContent = message || 'Apakah Anda yakin ingin melanjutkan?';
        confirmModal.classList.add('is-open');
        confirmModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        window.setTimeout(function () { confirmOk.focus(); }, 80);

        return new Promise(function (resolve) {
            pendingConfirm = resolve;
        });
    }

    function closeConfirm(result) {
        if (!pendingConfirm) return;
        const resolve = pendingConfirm;
        pendingConfirm = null;
        confirmModal.classList.remove('is-open');
        confirmModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        resolve(result);
    }

    confirmOk.addEventListener('click', function () { closeConfirm(true); });
    confirmCancel.addEventListener('click', function () { closeConfirm(false); });
    confirmModal.querySelectorAll('[data-confirm-cancel]').forEach(function (button) {
        button.addEventListener('click', function () { closeConfirm(false); });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape' || !confirmModal.classList.contains('is-open')) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        closeConfirm(false);
    }, true);

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;

        if (form.dataset.confirmed === '1') {
            window.setTimeout(function () { delete form.dataset.confirmed; }, 0);
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        const submitter = event.submitter || document.activeElement;
        openConfirm(form.dataset.confirm).then(function (accepted) {
            if (!accepted) return;
            form.dataset.confirmed = '1';
            if (form.requestSubmit && submitter && submitter.form === form) {
                form.requestSubmit(submitter);
            } else if (form.requestSubmit) {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });
    }, true);

    const tooltip = document.createElement('div');
    tooltip.className = 'admin-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    document.body.appendChild(tooltip);

    function tooltipText(element) {
        return element.dataset.adminTooltip || element.getAttribute('title') || element.getAttribute('aria-label') || '';
    }

    function showTooltip(element) {
        const text = tooltipText(element).trim();
        if (!text || element.disabled) return;

        if (element.hasAttribute('title')) {
            element.dataset.adminTooltip = text;
            element.removeAttribute('title');
        }

        tooltip.textContent = text;
        tooltip.classList.add('is-visible');
        positionTooltip(element);
    }

    function positionTooltip(element) {
        if (!tooltip.classList.contains('is-visible')) return;

        const rect = element.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        const gap = 10;
        const viewportPadding = 10;
        let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
        let top = rect.top - tooltipRect.height - gap;

        left = Math.max(viewportPadding, Math.min(left, window.innerWidth - tooltipRect.width - viewportPadding));
        if (top < viewportPadding) {
            top = rect.bottom + gap;
            tooltip.classList.add('is-below');
        } else {
            tooltip.classList.remove('is-below');
        }

        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
        tooltip.style.setProperty('--tooltip-anchor', (rect.left + rect.width / 2 - left) + 'px');
    }

    function hideTooltip() {
        tooltip.classList.remove('is-visible', 'is-below');
    }

    document.querySelectorAll('.icon-btn, [data-admin-tooltip]').forEach(function (element) {
        const text = tooltipText(element).trim();
        if (!text) return;

        if (element.hasAttribute('title')) {
            element.dataset.adminTooltip = text;
            element.removeAttribute('title');
        }

        element.addEventListener('mouseenter', function () { showTooltip(element); });
        element.addEventListener('focus', function () { showTooltip(element); });
        element.addEventListener('mouseleave', hideTooltip);
        element.addEventListener('blur', hideTooltip);
        element.addEventListener('click', hideTooltip);
    });

    window.addEventListener('scroll', hideTooltip, true);
    window.addEventListener('resize', hideTooltip);

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        const firstField = modal.querySelector('input, select, textarea, button');
        if (firstField) {
            window.setTimeout(function () { firstField.focus(); }, 80);
        }
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    document.querySelectorAll('[data-open-modal]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            openModal(document.getElementById(trigger.dataset.openModal));
        });
    });

    document.querySelectorAll('.admin-modal').forEach(function (modal) {
        modal.querySelectorAll('[data-close-modal]').forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                closeModal(modal);
            });
        });
        if (modal.classList.contains('is-open')) {
            document.body.classList.add('modal-open');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        const open = document.querySelector('.admin-modal.is-open');
        if (open) closeModal(open);
    });

    document.querySelectorAll('[data-table-helper]').forEach(function (helper) {
        const table = helper.querySelector('[data-interactive-table]');
        const search = helper.querySelector('[data-table-search]');
        const count = helper.querySelector('[data-table-count]');
        const bulkAll = helper.querySelector('[data-bulk-all]');
        const bulkForm = helper.querySelector('[data-bulk-form]');
        const bulkInputs = helper.querySelector('[data-bulk-inputs]');
        const bulkSubmit = helper.querySelector('[data-bulk-submit]');
        const bulkLabel = helper.querySelector('[data-bulk-label]');

        if (!table) return;

        const rows = Array.from(table.querySelectorAll('tbody tr')).filter(function (row) {
            return !row.hasAttribute('data-empty-row');
        });

        function visibleRows() {
            return rows.filter(function (row) {
                return row.style.display !== 'none';
            });
        }

        function selectedChecks() {
            return rows
                .map(function (row) { return row.querySelector('[data-bulk-row]'); })
                .filter(function (check) { return check && check.checked && !check.disabled; });
        }

        function updateCount() {
            if (count) {
                count.textContent = visibleRows().length + ' data';
            }
        }

        function updateBulk() {
            const selected = selectedChecks();
            if (bulkSubmit) {
                bulkSubmit.disabled = selected.length === 0;
            }
            if (bulkLabel) {
                bulkLabel.textContent = selected.length ? 'Hapus ' + selected.length + ' data' : 'Pilih data';
            }
            if (bulkInputs) {
                bulkInputs.innerHTML = '';
                selected.forEach(function (check) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = check.value;
                    bulkInputs.appendChild(input);
                });
            }
            if (bulkAll) {
                const selectable = visibleRows()
                    .map(function (row) { return row.querySelector('[data-bulk-row]'); })
                    .filter(function (check) { return check && !check.disabled; });
                bulkAll.checked = selectable.length > 0 && selectable.every(function (check) { return check.checked; });
                bulkAll.indeterminate = selected.length > 0 && !bulkAll.checked;
            }
        }

        if (search) {
            search.addEventListener('input', function () {
                const query = search.value.toLowerCase().trim();
                rows.forEach(function (row) {
                    row.style.display = row.textContent.toLowerCase().indexOf(query) === -1 ? 'none' : '';
                });
                updateCount();
                updateBulk();
            });
        }

        if (bulkAll) {
            bulkAll.addEventListener('change', function () {
                visibleRows().forEach(function (row) {
                    const check = row.querySelector('[data-bulk-row]');
                    if (check && !check.disabled) {
                        check.checked = bulkAll.checked;
                    }
                });
                updateBulk();
            });
        }

        rows.forEach(function (row) {
            const check = row.querySelector('[data-bulk-row]');
            if (check) {
                check.addEventListener('change', updateBulk);
            }
        });

        table.querySelectorAll('th[data-sortable]').forEach(function (th, index) {
            th.tabIndex = 0;
            th.setAttribute('role', 'button');
            th.addEventListener('click', function () {
                const direction = th.dataset.sortDirection === 'asc' ? 'desc' : 'asc';
                table.querySelectorAll('th[data-sortable]').forEach(function (header) {
                    delete header.dataset.sortDirection;
                });
                th.dataset.sortDirection = direction;

                rows.sort(function (a, b) {
                    const aText = (a.children[index + (bulkAll ? 1 : 0)] || {}).textContent || '';
                    const bText = (b.children[index + (bulkAll ? 1 : 0)] || {}).textContent || '';
                    return direction === 'asc'
                        ? aText.trim().localeCompare(bText.trim(), 'id', { numeric: true })
                        : bText.trim().localeCompare(aText.trim(), 'id', { numeric: true });
                });
                rows.forEach(function (row) { table.tBodies[0].appendChild(row); });
            });
            th.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    th.click();
                }
            });
        });

        if (bulkForm) {
            bulkForm.addEventListener('submit', function (event) {
                const selected = selectedChecks();
                if (selected.length === 0 || (bulkForm.dataset.confirmed !== '1' && !window.confirm(bulkForm.dataset.confirm || 'Lanjutkan aksi massal?'))) {
                    event.preventDefault();
                }
            });
        }

        updateCount();
        updateBulk();
    });
});
