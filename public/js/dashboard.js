(function () {
    'use strict';

    /* Chart.js revenue chart */
    function initRevenueChart() {
        const canvas = document.getElementById('revenueChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const data = JSON.parse(canvas.dataset.values || '[]');

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Revenue (GH₵)',
                    data: data,
                    backgroundColor: 'rgba(29, 78, 216, 0.7)',
                    borderColor: 'rgba(29, 78, 216, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'GH₵' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    /* Order status update */
    function initOrderStatusUpdate() {
        document.querySelectorAll('.order-status-select').forEach(function (select) {
            select.addEventListener('change', function () {
                const orderId = this.dataset.orderId;
                const status = this.value;

                fetch('/dashboard/orders/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ order_id: orderId, status: status })
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            showToast('Order status updated', 'success');
                        } else {
                            showToast(data.message || 'Update failed', 'error');
                        }
                    })
                    .catch(function () {
                        showToast('Failed to update status', 'error');
                    });
            });
        });
    }

    /* Product variants dynamic add/remove */
    function initVariantManager() {
        const container = document.getElementById('variantsContainer');
        const addBtn = document.getElementById('addVariantBtn');
        if (!container || !addBtn) return;

        let variantCount = container.querySelectorAll('.variant-row').length;

        addBtn.addEventListener('click', function () {
            variantCount++;
            const template = container.dataset.template || '';
            const row = document.createElement('div');
            row.className = 'variant-row grid grid-cols-5 gap-4 items-end mb-3';
            row.innerHTML = template.replace(/{index}/g, variantCount);
            container.appendChild(row);
        });

        container.addEventListener('click', function (e) {
            if (e.target.closest('.remove-variant')) {
                const row = e.target.closest('.variant-row');
                if (container.querySelectorAll('.variant-row').length > 1) {
                    row.remove();
                } else {
                    showToast('At least one variant is required', 'warning');
                }
            }
        });
    }

    /* Image preview before upload */
    function initImagePreview() {
        document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
            input.addEventListener('change', function () {
                const preview = document.getElementById(this.dataset.preview);
                if (!preview) return;

                preview.innerHTML = '';
                const files = Array.from(this.files);

                files.forEach(function (file) {
                    if (!file.type.match('image.*')) return;
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-24 h-24 object-cover rounded border mr-2 mb-2';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
    }

    /* Data table search/filter */
    function initDataTable() {
        document.querySelectorAll('.data-table-filter').forEach(function (input) {
            input.addEventListener('input', function () {
                const query = this.value.toLowerCase();
                const table = document.getElementById(this.dataset.table);
                if (!table) return;

                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(function (row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        });
    }

    /* Bulk select actions */
    function initBulkActions() {
        const selectAll = document.getElementById('selectAll');
        const bulkActionBtn = document.getElementById('bulkActionBtn');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.bulk-item');
                checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
                updateBulkButton();
            });
        }

        document.querySelectorAll('.bulk-item').forEach(function (cb) {
            cb.addEventListener('change', updateBulkButton);
        });

        function updateBulkButton() {
            const selected = document.querySelectorAll('.bulk-item:checked');
            if (bulkActionBtn) {
                bulkActionBtn.disabled = selected.length === 0;
                bulkActionBtn.dataset.count = selected.length;
            }
        }

        if (bulkActionBtn) {
            bulkActionBtn.addEventListener('click', function () {
                const action = document.getElementById('bulkActionSelect');
                if (!action || !action.value) {
                    showToast('Select an action', 'warning');
                    return;
                }

                const ids = [];
                document.querySelectorAll('.bulk-item:checked').forEach(function (cb) {
                    ids.push(cb.value);
                });

                fetch('/dashboard/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ action: action.value, ids: ids })
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            showToast(data.message || 'Action completed', 'success');
                            if (data.reload) location.reload();
                        } else {
                            showToast(data.message || 'Action failed', 'error');
                        }
                    })
                    .catch(function () {
                        showToast('Request failed', 'error');
                    });
            });
        }
    }

    /* Withdrawal form validation */
    function initWithdrawalForm() {
        const form = document.getElementById('withdrawalForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const amount = parseFloat(document.getElementById('withdrawalAmount')?.value);
            const balance = parseFloat(document.getElementById('availableBalance')?.dataset.balance);

            if (!amount || amount <= 0) {
                e.preventDefault();
                showToast('Enter a valid amount', 'error');
                return;
            }

            if (balance !== undefined && amount > balance) {
                e.preventDefault();
                showToast('Amount exceeds available balance', 'error');
                return;
            }

            if (amount < 10) {
                e.preventDefault();
                showToast('Minimum withdrawal is GH₵10', 'warning');
                return;
            }
        });
    }

    /* Category/Brand management AJAX */
    function initCategoryBrandManager() {
        document.querySelectorAll('.ajax-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            showToast(data.message || 'Saved successfully', 'success');
                            if (data.reload) location.reload();
                        } else {
                            showToast(data.message || 'Failed to save', 'error');
                        }
                    })
                    .catch(function () {
                        showToast('Request failed', 'error');
                    });
            });
        });

        document.querySelectorAll('.delete-ajax').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (!confirm('Delete this item?')) return;

                fetch(this.href, {
                    method: 'DELETE',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            showToast('Deleted successfully', 'success');
                            if (data.reload) location.reload();
                            else {
                                const row = btn.closest('tr');
                                if (row) row.remove();
                            }
                        } else {
                            showToast(data.message || 'Delete failed', 'error');
                        }
                    })
                    .catch(function () {
                        showToast('Request failed', 'error');
                    });
            });
        });
    }

    function showToast(message, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        }
    }

    /* Init */
    function init() {
        initRevenueChart();
        initOrderStatusUpdate();
        initVariantManager();
        initImagePreview();
        initDataTable();
        initBulkActions();
        initWithdrawalForm();
        initCategoryBrandManager();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
