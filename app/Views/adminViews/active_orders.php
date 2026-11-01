<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Orders</title>
    <link rel="stylesheet" href="<?= base_url('css/admin_global.css') ?>">
     <link rel="stylesheet" href="<?= base_url('css/active_orders.css') ?>">
   
</head>
<body>

<?php
$prices = $prices ?? [
    'Wash & Fold' => 55,
    'Wash & Dry'  => 50,
];

$shop = $shop ?? [
    'name'      => 'SAS Laundry',
    'phone'     => '',
    'address'   => '',
    'sms_ready' => 'Hi {name}! Your laundry {code} is ready for pickup. Please bring your claim code.',
];

$csrfName = csrf_token();
$csrfHash = csrf_hash();
?>

<div class="admin-layout">
    <aside class="sidebar">
        <?= $this->include('partials/admin_sidebar') ?>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <h2 style="font-size:1.25rem;color:var(--text);">Active Pipeline</h2>
            <button type="button" class="btn-new" onclick="openIntakeModal()">＋ New Drop-off (Intake)</button>
        </header>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;">
                    ✓ <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <div class="toolbar">
                <div class="filters">
                    <select class="filter-select" id="filter-stage">
                        <option value="">All Stages</option>
                        <option value="intake">Intake</option>
                        <option value="washing">Washing</option>
                        <option value="drying">Drying</option>
                        <option value="folding">Folding</option>
                        <option value="ready">Ready</option>
                    </select>

                    <select class="filter-select" id="filter-payment">
                        <option value="">Payment Status</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>

                <input
                    type="text"
                    class="search-input"
                    id="search-input"
                    placeholder="Search claim code, name, or phone..."
                    autocomplete="off"
                >
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px;text-align:center;">#</th>
                            <th>Claim Code</th>
                            <th>Customer</th>
                            <th>Service Package</th>
                            <th>Stage &amp; Progress</th>
                            <th>Payment</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>

                    <tbody id="order-table-body">
                    <?php if (empty($orders)): ?>
                        <tr id="empty-orders-row">
                            <td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">
                                No active orders. All caught up!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $index => $order): ?>
                            <?php
                                $orderId = (int) ($order['id'] ?? 0);
                                $service = $order['service'] ?? '';
                                $stage = $order['stage'] ?? 'intake';
                                $isPaid = !empty($order['paid']);
                                $price = (float) ($order['price'] ?? 0);
                                $weight = (float) ($order['weight'] ?? 0);
                            ?>
                            <tr
                                class="order-row"
                                id="row-<?= $orderId ?>"
                                data-id="<?= $orderId ?>"
                                data-code="<?= esc($order['claim_code'] ?? '', 'attr') ?>"
                                data-name="<?= esc($order['name'] ?? '', 'attr') ?>"
                                data-phone="<?= esc($order['phone'] ?? '', 'attr') ?>"
                                data-service="<?= esc($service, 'attr') ?>"
                                data-weight="<?= esc((string) $weight, 'attr') ?>"
                                data-price="<?= esc((string) $price, 'attr') ?>"
                                data-paid="<?= $isPaid ? '1' : '0' ?>"
                                data-stage="<?= esc($stage, 'attr') ?>"
                            >
                                <td class="row-number" style="text-align:center;color:var(--text-muted);font-weight:600;">
                                    <?= $index + 1 ?>
                                </td>

                                <td><span class="claim-code"><?= esc($order['claim_code'] ?? '') ?></span></td>

                                <td>
                                    <div class="cust-info">
                                        <span class="cust-name"><?= esc($order['name'] ?? '') ?></span>
                                        <span class="cust-phone"><?= esc($order['phone'] ?? '') ?></span>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight:600;font-size:.85rem;color:<?= $service === 'Wash & Dry' ? '#d97706' : '#2563eb' ?>;">
                                        <?= esc($service) ?>
                                    </div>
                                    <div style="font-size:.75rem;color:var(--text-muted);">
                                        <?= number_format($weight, 1) ?> kg · ₱<?= number_format($price, 2) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="stage-progress-wrap" onclick="toggleRevertMenu(event, <?= $orderId ?>)">
                                        <div class="stage-progress" id="dots-<?= $orderId ?>"></div>
                                        <span class="badge <?= esc($stage, 'attr') ?>"><?= esc(ucfirst($stage)) ?></span>
                                    </div>
                                </td>

                                <td>
                                    <?php if ($isPaid): ?>
                                        <button type="button" class="pay-toggle paid" disabled>✓ Paid</button>
                                    <?php else: ?>
                                        <button type="button" class="pay-toggle unpaid" onclick="markPaid(<?= $orderId ?>)">
                                            Mark Paid
                                        </button>
                                    <?php endif; ?>
                                </td>

                                <td style="text-align:right;">
                                    <div id="action-<?= $orderId ?>"></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- New intake modal -->
<div class="modal-overlay" id="intakeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>New Drop-off (Intake)</h3>
            <button type="button" class="btn-close" onclick="closeModal('intakeModal')">&times;</button>
        </div>

        <form action="<?= base_url('admin/save-intake') ?>" method="POST" id="intake-form" onsubmit="return true;">
            <?= csrf_field() ?>

            <div class="modal-body">
                <div class="form-group">
                    <label>Claim Code</label>
                    <div class="auto-code" style="background:#fffbeb;color:#d97706;">
                        Auto-Generated on Save
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="input-name">Name / Alias</label>
                        <input type="text" id="input-name" name="name" class="form-control"
                               placeholder="e.g. Alex Santos" maxlength="150" required>
                    </div>

                    <div class="form-group">
                        <label for="input-phone">Phone Number</label>
                        <input type="text" id="input-phone" name="phone" class="form-control"
                               placeholder="0917-XXX-XXXX" maxlength="30" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="input-service">Service Package</label>
                        <select name="service" id="input-service" class="form-control" required onchange="calculatePrice()">
                            <option value="">Select Service...</option>
                            <option value="Wash & Fold">Wash &amp; Fold (Standard)</option>
                            <option value="Wash & Dry">Wash &amp; Dry (No Folding)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="input-weight">Weight (kg)</label>
                        <input type="number" name="weight" id="input-weight" step="0.1" min="0.1" max="500"
                               class="form-control" placeholder="e.g. 4.5" required oninput="calculatePrice()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="input-price">Total Price (₱)</label>
                        <input type="text" name="price" id="input-price" class="form-control" value="0.00" readonly
                               style="background:#f8fafc;font-weight:bold;color:var(--primary);">
                        <div class="helper-text" id="rate-hint">Rate: —</div>
                    </div>

                    <div class="form-group">
                        <label for="input-payment">Payment Status</label>
                        <select name="payment" id="input-payment" class="form-control" required>
                            <option value="unpaid">Unpaid (Pay at Pickup)</option>
                            <option value="paid">Paid Upfront</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('intakeModal')">Cancel</button>
                <button type="submit" class="btn-save">Save Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Mark ready modal -->
<div class="modal-overlay" id="markReadyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Mark as Ready for Pickup?</h3>
            <button type="button" class="btn-close" onclick="closeModal('markReadyModal')">&times;</button>
        </div>

        <div class="modal-body">
            <div class="confirm-order-summary" id="mark-ready-summary"></div>
            <label style="display:block;font-size:.8rem;font-weight:600;color:var(--text-muted);margin-bottom:.4rem;">
                SMS message preview (not sent by this page):
            </label>
            <div class="sms-preview" id="mark-ready-sms-preview"></div>
            <p class="helper-text">
                Your current controller does not include an SMS-sending endpoint. Marking the order ready will update its stage only.
            </p>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('markReadyModal')">Cancel</button>
            <button type="button" class="btn-save" onclick="finishMarkReady()" style="background:var(--success);">
                Mark Ready
            </button>
        </div>
    </div>
</div>

<!-- More actions modal -->
<div class="modal-overlay" id="actionMenuModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="action-menu-title">Order Actions</h3>
            <button type="button" class="btn-close" onclick="closeModal('actionMenuModal')">&times;</button>
        </div>

        <div class="modal-body">
            <div class="confirm-order-summary" id="action-menu-summary" style="margin-bottom:1rem;"></div>
            <div class="action-modal-list" id="action-menu-list"></div>
        </div>
    </div>
</div>

<!-- Complete order modal -->
<div class="modal-overlay" id="completeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Complete Order?</h3>
            <button type="button" class="btn-close" onclick="closeModal('completeModal')">&times;</button>
        </div>

        <div class="modal-body"><div id="complete-summary"></div></div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('completeModal')">Cancel</button>
            <button type="button" class="btn-save" onclick="confirmComplete()" style="background:var(--success);">
                Complete Order ✓
            </button>
        </div>
    </div>
</div>

<!-- Cancel order modal -->
<div class="modal-overlay" id="cancelModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Cancel Order?</h3>
            <button type="button" class="btn-close" onclick="closeModal('cancelModal')">&times;</button>
        </div>

        <div class="modal-body">
            <div id="cancel-summary"></div>
            <p style="font-size:.8rem;color:var(--text-muted);text-align:center;margin-top:1rem;">
                This will remove the order from the active pipeline.
            </p>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('cancelModal')">Keep Order</button>
            <button type="button" class="btn-save" onclick="confirmCancel()" style="background:var(--danger);">
                Cancel Order
            </button>
        </div>
    </div>
</div>

<script>
'use strict';

const BASE_URL = <?= json_encode(rtrim(base_url(), '/') . '/') ?>;
const CSRF_NAME = <?= json_encode($csrfName) ?>;
let csrfHash = <?= json_encode($csrfHash) ?>;

const PRICES = <?= json_encode([
    'Wash & Fold' => (float) ($prices['Wash & Fold'] ?? 55),
    'Wash & Dry'  => (float) ($prices['Wash & Dry'] ?? 50),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

const SHOP = <?= json_encode([
    'name'      => (string) ($shop['name'] ?? 'SAS Laundry'),
    'phone'     => (string) ($shop['phone'] ?? ''),
    'address'   => (string) ($shop['address'] ?? ''),
    'sms_ready' => (string) ($shop['sms_ready'] ?? 'Hi {name}! Your laundry {code} is ready for pickup. Please bring your claim code.'),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

const PIPELINES = {
    'Wash & Fold': [
        { key: 'intake', label: 'Intake', cls: 'intake', action: 'Start Wash →' },
        { key: 'washing', label: 'Washing', cls: 'washing', action: 'Move to Dry →' },
        { key: 'drying', label: 'Drying', cls: 'drying', action: 'Move to Fold →' },
        { key: 'folding', label: 'Folding', cls: 'folding', action: 'Mark Ready →' },
        { key: 'ready', label: 'Ready', cls: 'ready', action: 'Complete ✓' }
    ],
    'Wash & Dry': [
        { key: 'intake', label: 'Intake', cls: 'intake', action: 'Start Wash →' },
        { key: 'washing', label: 'Washing', cls: 'washing', action: 'Move to Dry →' },
        { key: 'drying', label: 'Drying', cls: 'drying', action: 'Mark Ready →' },
        { key: 'ready', label: 'Ready', cls: 'ready', action: 'Complete ✓' }
    ]
};

let currentOrderId = null;
let activePortalMenu = null;

const pendingStageRequests = new Map();

function saveStageInBackground(id, stage) {
    const previous = pendingStageRequests.get(id) || Promise.resolve();

    const request = previous
        .catch(() => {})
        .then(() => postAction('ajax-advance-stage', { id, stage }));

    pendingStageRequests.set(id, request);

    request.finally(() => {
        if (pendingStageRequests.get(id) === request) {
            pendingStageRequests.delete(id);
        }
    });

    return request;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.order-row').forEach(renderOrder);

    document.getElementById('filter-stage').addEventListener('change', filterTable);
    document.getElementById('filter-payment').addEventListener('change', filterTable);
    document.getElementById('search-input').addEventListener('input', filterTable);

     document.getElementById('intake-form')
        .addEventListener('submit', saveIntakeInstantly);

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', event => {
            if (event.target === overlay) closeModal(overlay.id);
        });
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => closeModal(modal.id));
            closePortalMenu();
        }
    });
});

async function saveIntakeInstantly(event) {
    event.preventDefault();

    const form     = event.target;
    const saveBtn  = form.querySelector('.btn-save');

    // Collect form values
    const payload = {
        name:    form.querySelector('#input-name').value.trim(),
        phone:   form.querySelector('#input-phone').value.trim(),
        service: form.querySelector('#input-service').value,
        weight:  form.querySelector('#input-weight').value,
        price:   form.querySelector('#input-price').value,
        payment: form.querySelector('#input-payment').value,
    };

    // Basic client-side guard
    if (!payload.name || !payload.phone || !payload.service || !payload.weight) {
        showError('Please fill in all required fields.');
        return;
    }

    // Prevent double-submit
    if (saveBtn.disabled) return;
    const originalLabel = saveBtn.textContent;
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving…';

    try {
        const result = await postAction('ajax-save-intake', payload);

        // Instantly add row to the table
        addOrderToTable(result.order);

        // Close modal + reset form
        closeModal('intakeModal');
        form.reset();
        document.getElementById('input-price').value = '0.00';
        document.getElementById('rate-hint').textContent = 'Rate: —';

        showToast(`Order ${result.order.claim_code} saved ✓`);
    } catch (error) {
        showError(error.message);
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = originalLabel;
    }
}

function getPipeline(row) {
    return PIPELINES[row.dataset.service] || PIPELINES['Wash & Fold'];
}

function renderOrder(row) {
    const id = Number(row.dataset.id);
    const pipeline = getPipeline(row);
    const foundIndex = pipeline.findIndex(stage => stage.key === row.dataset.stage);
    const safeIndex = foundIndex < 0 ? 0 : foundIndex;
    const currentStage = pipeline[safeIndex];

    const dots = pipeline.map((stage, index) => {
        let className = 'stage-dot';
        if (index < safeIndex) className += ' completed';
        else if (index === safeIndex) className += ' active';
        return `<div class="${className}"></div>`;
    }).join('');

    const dotsElement = document.getElementById(`dots-${id}`);
    if (dotsElement) dotsElement.innerHTML = dots;

    const badge = row.querySelector('.badge');
    if (badge) {
        badge.className = `badge ${currentStage.cls}`;
        badge.textContent = currentStage.label;
    }

    renderActionButtons(row, currentStage);
}

function renderActionButtons(row, currentStage) {
    const id = Number(row.dataset.id);
    let primaryButton;

    if (currentStage.key === 'ready') {
        primaryButton = `<button type="button" class="btn-action btn-complete" onclick="openCompleteModal(${id})">Complete ✓</button>`;
    } else if (
        currentStage.key === 'folding' ||
        (currentStage.key === 'drying' && row.dataset.service === 'Wash & Dry')
    ) {
        primaryButton = `<button type="button" class="btn-action btn-ready" onclick="openMarkReadyModal(${id})">Mark Ready →</button>`;
    } else {
        primaryButton = `<button type="button" class="btn-action" onclick="advanceStage(${id})">${currentStage.action}</button>`;
    }

    const menuButton = `<button type="button" class="btn-menu" title="More actions" onclick="openActionMenu(${id})">⋮</button>`;
    const container = document.getElementById(`action-${id}`);

    if (container) {
        container.innerHTML = `<div class="action-group">${primaryButton}${menuButton}</div>`;
    }
}

function openModal(id) {
    document.getElementById(id)?.classList.add('active');
}

function closeModal(id) {
    document.getElementById(id)?.classList.remove('active');
}

function openIntakeModal() {
    document.getElementById('intake-form').reset();
    document.getElementById('input-price').value = '0.00';
    document.getElementById('rate-hint').textContent = 'Rate: —';
    openModal('intakeModal');
}

function calculatePrice() {
    const service = document.getElementById('input-service').value;
    const weight = parseFloat(document.getElementById('input-weight').value) || 0;
    const rate = Object.prototype.hasOwnProperty.call(PRICES, service) ? Number(PRICES[service]) : 0;
    const total = Math.max(0, weight) * rate;

    document.getElementById('input-price').value = total.toFixed(2);
    document.getElementById('rate-hint').textContent = rate > 0
        ? `Rate: ₱${rate.toFixed(2)} per kg`
        : 'Rate: —';
}

async function postAction(endpoint, data) {
    const body = new URLSearchParams();

    Object.entries(data).forEach(([key, value]) => body.append(key, String(value)));
    body.append(CSRF_NAME, csrfHash);

    const response = await fetch(`${BASE_URL}admin/${endpoint}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: body.toString(),
        credentials: 'same-origin'
    });

    const result = await response.json().catch(() => null);

    if (!response.ok || !result || result.status !== 'success') {
        throw new Error((result && result.message) || 'The request could not be completed. Please try again.');
    }

    if (result.csrfHash) csrfHash = result.csrfHash;
    return result;
}






function addOrderToTable(order) {
    const tbody = document.getElementById('order-table-body');
    document.getElementById('empty-orders-row')?.remove();

    const id = Number(order.id);
    const service = String(order.service || '');
    const stage = String(order.stage || 'intake');
    const weight = Number(order.weight || 0);
    const price = Number(order.price || 0);
    const isPaid = order.paid === true || order.paid === 1 || order.paid === '1';

    const row = document.createElement('tr');
    row.className = 'order-row';
    row.id = `row-${id}`;

    row.dataset.id = String(id);
    row.dataset.code = String(order.claim_code || '');
    row.dataset.name = String(order.name || '');
    row.dataset.phone = String(order.phone || '');
    row.dataset.service = service;
    row.dataset.weight = String(weight);
    row.dataset.price = String(price);
    row.dataset.paid = isPaid ? '1' : '0';
    row.dataset.stage = stage;

    row.innerHTML = `
        <td class="row-number" style="text-align:center;color:var(--text-muted);font-weight:600;"></td>
        <td><span class="claim-code">${escapeHTML(order.claim_code || '')}</span></td>
        <td>
            <div class="cust-info">
                <span class="cust-name">${escapeHTML(order.name || '')}</span>
                <span class="cust-phone">${escapeHTML(order.phone || '')}</span>
            </div>
        </td>
        <td>
            <div style="font-weight:600;font-size:0.85rem;color:${service === 'Wash & Dry' ? '#d97706' : '#2563eb'};">
                ${escapeHTML(service)}
            </div>
            <div style="font-size:0.75rem;color:var(--text-muted);">
                ${weight.toFixed(1)} kg · ₱${price.toFixed(2)}
            </div>
        </td>
        <td>
            <div class="stage-progress-wrap" onclick="toggleRevertMenu(event, ${id})">
                <div class="stage-progress" id="dots-${id}"></div>
                <span class="badge"></span>
            </div>
        </td>
        <td>
            ${isPaid
                ? '<button type="button" class="pay-toggle paid" disabled>✓ Paid</button>'
                : `<button type="button" class="pay-toggle unpaid" onclick="markPaid(${id})">Mark Paid</button>`}
        </td>
        <td style="text-align:right;"><div id="action-${id}"></div></td>
    `;

    tbody.prepend(row);
    renderOrder(row);
    renumberRows();
}

function setBusy(row, busy) {
    if (!row) return;
    row.dataset.busy = busy ? '1' : '0';
}

function isBusy(row) {
    return row?.dataset.busy === '1';
}

function advanceStage(id) {
    const row = document.getElementById(`row-${id}`);
    if (!row) return;

    const pipeline = getPipeline(row);
    const currentIndex = pipeline.findIndex(stage => stage.key === row.dataset.stage);
    const nextStage = pipeline[currentIndex + 1];
    if (!nextStage) return;

    const previousStage = row.dataset.stage;

    row.dataset.stage = nextStage.key;
    renderOrder(row);
    filterTable();
    showToast(`Advanced to ${nextStage.label}`);

    saveStageInBackground(id, nextStage.key).catch(error => {
        if (row.dataset.stage === nextStage.key) {
            row.dataset.stage = previousStage;
            renderOrder(row);
            filterTable();
            showError(error.message);
        }
    });
}

function markPaid(id) {
    const row = document.getElementById(`row-${id}`);
    if (!row || isBusy(row) || row.dataset.paid === '1') return;

    const previousPaid = row.dataset.paid;

    setBusy(row, true);
    row.dataset.paid = '1';
    updatePaymentCell(row);
    filterTable();
    closeActionMenu();
    showToast('Marked as Paid ✓');

    postAction('ajax-mark-paid', { id })
        .catch(error => {
            row.dataset.paid = previousPaid;
            updatePaymentCell(row);
            filterTable();
            showError(error.message);
        })
        .finally(() => setBusy(row, false));
}

function updatePaymentCell(row) {
    const id = Number(row.dataset.id);
    const cell = row.querySelector('td:nth-child(6)');
    if (!cell) return;

    if (row.dataset.paid === '1') {
        cell.innerHTML = '<button type="button" class="pay-toggle paid" disabled>✓ Paid</button>';
    } else {
        cell.innerHTML = `<button type="button" class="pay-toggle unpaid" onclick="markPaid(${id})">Mark Paid</button>`;
    }
}

function openMarkReadyModal(id) {
    const row = document.getElementById(`row-${id}`);
    if (!row) return;

    currentOrderId = id;

    document.getElementById('mark-ready-summary').innerHTML = `
        <div class="row"><span class="label">Claim Code</span><span class="value">${escapeHTML(row.dataset.code)}</span></div>
        <div class="row"><span class="label">Customer</span><span class="value">${escapeHTML(row.dataset.name)}</span></div>
        <div class="row"><span class="label">Phone</span><span class="value">${escapeHTML(row.dataset.phone)}</span></div>
        <div class="row"><span class="label">Service</span><span class="value">${escapeHTML(row.dataset.service)}</span></div>
        <div class="row"><span class="label">Amount</span><span class="value">₱${Number(row.dataset.price).toFixed(2)}</span></div>
    `;

    const template = SHOP.sms_ready || 'Hi {name}! Your laundry {code} is ready for pickup. Please bring your claim code.';
    const smsMessage = template
        .replaceAll('{name}', row.dataset.name)
        .replaceAll('{code}', row.dataset.code);

    document.getElementById('mark-ready-sms-preview').textContent = smsMessage;
    openModal('markReadyModal');
}

function finishMarkReady() {
    if (!currentOrderId) return;

    const id = currentOrderId;
    const row = document.getElementById(`row-${id}`);
    if (!row || isBusy(row)) return;

    const previousStage = row.dataset.stage;

    setBusy(row, true);
    row.dataset.stage = 'ready';
    renderOrder(row);
    filterTable();
    closeModal('markReadyModal');
    currentOrderId = null;
    showToast('Order marked as Ready');

    saveStageInBackground(id, 'ready')
        .catch(error => {
            row.dataset.stage = previousStage;
            renderOrder(row);
            filterTable();
            showError(error.message);
        })
        .finally(() => setBusy(row, false));
}

function openCompleteModal(id) {
    const row = document.getElementById(`row-${id}`);
    if (!row) return;

    currentOrderId = id;

    const isPaid = row.dataset.paid === '1';
    const amount = Number(row.dataset.price).toFixed(2);

    const paymentNotice = isPaid
        ? `<div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:.9rem;margin-top:1rem;font-size:.85rem;color:#065f46;text-align:center;font-weight:600;">✓ Payment already settled</div>`
        : `<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:.9rem;margin-top:1rem;">
                <div style="color:var(--danger);font-weight:700;font-size:.85rem;margin-bottom:.3rem;">⚠️ OUTSTANDING BALANCE: ₱${amount}</div>
                <div style="font-size:.8rem;color:#7f1d1d;line-height:1.4;">Completing this order will also mark it as paid in the database.</div>
           </div>`;

    document.getElementById('complete-summary').innerHTML = `
        <div class="confirm-order-summary">
            <div class="row"><span class="label">Claim Code</span><span class="value">${escapeHTML(row.dataset.code)}</span></div>
            <div class="row"><span class="label">Customer</span><span class="value">${escapeHTML(row.dataset.name)}</span></div>
            <div class="row"><span class="label">Service</span><span class="value">${escapeHTML(row.dataset.service)} · ${escapeHTML(row.dataset.weight)} kg</span></div>
            <div class="row"><span class="label">Amount</span><span class="value">₱${amount}</span></div>
        </div>
        ${paymentNotice}
        <p style="font-size:.8rem;color:var(--text-muted);text-align:center;margin-top:1rem;">
            Order will be moved to Completed History.
        </p>
    `;

    openModal('completeModal');
}

function confirmComplete() {
    if (!currentOrderId) return;

    const id = currentOrderId;
    const row = document.getElementById(`row-${id}`);
    if (!row || isBusy(row)) return;

    const code = row.dataset.code;
    const price = Number(row.dataset.price).toFixed(2);
    const parent = row.parentNode;
    const nextSibling = row.nextSibling;

    setBusy(row, true);
    row.remove();
    closeModal('completeModal');
    currentOrderId = null;
    updateEmptyState();
    renumberRows();
    showToast(`Order ${code} completed · ₱${price} collected`);

    postAction('ajax-complete-order', { id })
        .catch(error => {
            restoreRow(row, parent, nextSibling);
            showError(error.message);
        });
}

function openActionMenu(id) {
    const row = document.getElementById(`row-${id}`);
    if (!row) return;

    currentOrderId = id;

    document.getElementById('action-menu-title').textContent = `Order ${row.dataset.code} — Actions`;
    document.getElementById('action-menu-summary').innerHTML = `
        <div class="row"><span class="label">Customer</span><span class="value">${escapeHTML(row.dataset.name)}</span></div>
        <div class="row"><span class="label">Service</span><span class="value">${escapeHTML(row.dataset.service)} · ${escapeHTML(row.dataset.weight)} kg</span></div>
        <div class="row"><span class="label">Amount</span><span class="value">₱${Number(row.dataset.price).toFixed(2)}</span></div>
        <div class="row"><span class="label">Stage</span><span class="value">${escapeHTML(row.dataset.stage)}</span></div>
    `;

    const actions = [];

    if (row.dataset.paid === '0') {
        actions.push(`
            <button type="button" class="action-modal-item" onclick="markPaid(${id})">
                <span class="icon">💰</span>
                <span class="text">Mark as Paid<small>Collect ₱${Number(row.dataset.price).toFixed(2)} payment now</small></span>
            </button>
        `);
    }

    actions.push(`
        <button type="button" class="action-modal-item danger" onclick="closeActionMenu(); cancelOrder(${id})">
            <span class="icon">❌</span>
            <span class="text">Cancel Order<small>Remove from pipeline (does not refund)</small></span>
        </button>
    `);

    document.getElementById('action-menu-list').innerHTML = actions.join('');
    openModal('actionMenuModal');
}

function closeActionMenu() {
    closeModal('actionMenuModal');
}

function cancelOrder(id) {
    const row = document.getElementById(`row-${id}`);
    if (!row) return;

    currentOrderId = id;

    let warningHTML;

    if (row.dataset.stage === 'intake') {
        warningHTML = `
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:.9rem;text-align:center;font-weight:600;color:var(--text-muted);">
                Stage: Intake (Not yet washed)
                <span style="color:var(--success);font-size:.85rem;margin-top:.5rem;display:block;">✓ No processing stage completed</span>
            </div>
        `;
    } else {
        warningHTML = `
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:.9rem;margin-top:1rem;">
                <div style="color:var(--danger);font-weight:700;font-size:.85rem;margin-bottom:.3rem;">⚠️ WORK HAS ALREADY STARTED</div>
                <div style="font-size:.8rem;color:#7f1d1d;line-height:1.4;">
                    The shop may already have spent money on water, electricity, and labor for this order.
                    Cancelling does not automatically issue a refund.
                </div>
            </div>
        `;
    }

    document.getElementById('cancel-summary').innerHTML = `
        <div class="confirm-order-summary">
            <div class="row"><span class="label">Claim Code</span><span class="value">${escapeHTML(row.dataset.code)}</span></div>
            <div class="row"><span class="label">Customer</span><span class="value">${escapeHTML(row.dataset.name)}</span></div>
            <div class="row"><span class="label">Amount</span><span class="value">₱${Number(row.dataset.price).toFixed(2)}</span></div>
        </div>
        ${warningHTML}
    `;

    openModal('cancelModal');
}

function confirmCancel() {
    if (!currentOrderId) return;

    const id = currentOrderId;
    const row = document.getElementById(`row-${id}`);
    if (!row || isBusy(row)) return;

    const code = row.dataset.code;
    const parent = row.parentNode;
    const nextSibling = row.nextSibling;

    setBusy(row, true);
    row.remove();
    closeModal('cancelModal');
    closeActionMenu();
    currentOrderId = null;
    updateEmptyState();
    renumberRows();
    showToast(`Order ${code} cancelled`);

    postAction('ajax-cancel-order', { id })
        .catch(error => {
            restoreRow(row, parent, nextSibling);
            showError(error.message);
        });
}

function restoreRow(row, parent, nextSibling) {
    if (nextSibling && nextSibling.parentNode === parent) {
        parent.insertBefore(row, nextSibling);
    } else {
        parent.appendChild(row);
    }

    setBusy(row, false);
    updateEmptyState();
    renumberRows();
    filterTable();
}

function toggleRevertMenu(event, id) {
    event.stopPropagation();

    if (activePortalMenu) {
        const wasSameRow = activePortalMenu.dataset.rowId === String(id);
        closePortalMenu();
        if (wasSameRow) return;
    }

    const row = document.getElementById(`row-${id}`);
    if (!row) return;

    const pipeline = getPipeline(row);
    const currentIndex = pipeline.findIndex(stage => stage.key === row.dataset.stage);
    const menu = document.createElement('div');

    menu.className = 'revert-menu-portal';
    menu.dataset.rowId = String(id);

    menu.innerHTML = pipeline.map((stage, index) => {
        let className = 'revert-menu-item';
        if (index === currentIndex) className += ' current';
        else if (index < currentIndex) className += ' done';

        return `
            <button type="button" class="${className}" data-stage="${stage.key}">
                <span class="dot"></span>${stage.label}
            </button>
        `;
    }).join('');

    menu.querySelectorAll('[data-stage]').forEach(button => {
        button.addEventListener('click', () => jumpToStage(id, button.dataset.stage));
    });

    document.body.appendChild(menu);

    const rect = event.currentTarget.getBoundingClientRect();
    const menuHeight = menu.offsetHeight;
    const menuWidth = menu.offsetWidth;

    let top = rect.bottom + 5;
    if (top + menuHeight > window.innerHeight) {
        top = Math.max(5, rect.top - menuHeight - 5);
    }

    let left = rect.left;
    if (left + menuWidth > window.innerWidth) {
        left = Math.max(5, window.innerWidth - menuWidth - 5);
    }

    menu.style.top = `${top}px`;
    menu.style.left = `${left}px`;
    activePortalMenu = menu;
}

function jumpToStage(id, newStage) {
    const row = document.getElementById(`row-${id}`);
    if (!row) return;

    const pipeline = getPipeline(row);
    if (!pipeline.some(stage => stage.key === newStage)) return;

    const previousStage = row.dataset.stage;

    row.dataset.stage = newStage;
    renderOrder(row);
    filterTable();
    closePortalMenu();
    showToast(`Stage changed to ${newStage}`);

    saveStageInBackground(id, newStage).catch(error => {
        if (row.dataset.stage === newStage) {
            row.dataset.stage = previousStage;
            renderOrder(row);
            filterTable();
            showError(error.message);
        }
    });
}

function closePortalMenu() {
    if (activePortalMenu) {
        activePortalMenu.remove();
        activePortalMenu = null;
    }
}

document.addEventListener('click', event => {
    if (activePortalMenu && !activePortalMenu.contains(event.target)) {
        closePortalMenu();
    }
});

function filterTable() {
    const stageFilter = document.getElementById('filter-stage').value.toLowerCase();
    const paymentFilter = document.getElementById('filter-payment').value.toLowerCase();
    const searchTerm = document.getElementById('search-input').value.trim().toLowerCase();

    document.querySelectorAll('.order-row').forEach(row => {
        const matchesStage = !stageFilter || row.dataset.stage.toLowerCase() === stageFilter;
        const paymentStatus = row.dataset.paid === '1' ? 'paid' : 'unpaid';
        const matchesPayment = !paymentFilter || paymentStatus === paymentFilter;

        const searchableText = [
            row.dataset.code,
            row.dataset.name,
            row.dataset.phone
        ].join(' ').toLowerCase();

        const matchesSearch = !searchTerm || searchableText.includes(searchTerm);
        row.style.display = matchesStage && matchesPayment && matchesSearch ? '' : 'none';
    });
}

function updateEmptyState() {
    const tbody = document.getElementById('order-table-body');
    const remainingRows = tbody.querySelectorAll('.order-row');
    const emptyRow = document.getElementById('empty-orders-row');

    if (remainingRows.length === 0 && !emptyRow) {
        const row = document.createElement('tr');
        row.id = 'empty-orders-row';
        row.innerHTML = `
            <td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">
                No active orders. All caught up!
            </td>
        `;
        tbody.appendChild(row);
    } else if (remainingRows.length > 0 && emptyRow) {
        emptyRow.remove();
    }
}

function renumberRows() {
    document.querySelectorAll('.order-row').forEach((row, index) => {
        const numberCell = row.querySelector('.row-number');
        if (numberCell) numberCell.textContent = String(index + 1);
    });
}

function escapeHTML(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';

    const icon = document.createElement('span');
    icon.textContent = '✓';

    const text = document.createElement('span');
    text.textContent = message;

    toast.append(icon, text);
    document.body.appendChild(toast);

    window.setTimeout(() => toast.remove(), 3000);
}

function showError(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = 'var(--danger)';
    toast.textContent = message || 'Something went wrong. Please try again.';
    document.body.appendChild(toast);

    window.setTimeout(() => toast.remove(), 4500);
}
</script>
</body>
</html>