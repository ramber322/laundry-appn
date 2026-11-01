<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track - <?= esc($settings['shop_name'] ?? 'SS Laundry') ?></title>
    <link rel="stylesheet" href="<?= base_url('css/tracking.css') ?>">
</head>
<body>

<div class="brand-bar">
    <div class="brand-inner">
        <div class="brand-icon">
            <img 
                src="<?= base_url('images/laundry-icon.png') ?>" 
                alt="<?= esc($settings['shop_name'] ?? 'Laundry') ?> logo"
                style="width: 100%; height: 100%; object-fit: contain;"
            >
        </div>
        <span><?= esc($settings['shop_name'] ?? 'SS Laundry') ?></span>
    </div>
</div>

<div class="container">
    
    <!-- ERROR MESSAGE -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="search-error show" style="margin-bottom: 1rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- SEARCH VIEW -->
    <div id="searchView" style="display: <?= isset($order) ? 'none' : 'block' ?>;">
        <div class="search-card">
           <div class="search-icon">
    <img 
        src="<?= base_url('images/magnifying-glass.png') ?>" 
        alt="Search"
        style="width: 70px; height: 70px; object-fit: contain; transform: scaleX(-1);"
    >
</div>
            <h1>Track Your Laundry</h1>
            <p>Enter the claim code from your receipt or SMS to see your order's live status.</p>

            <form class="search-form" action="<?= base_url('track') ?>" method="GET">
                <input type="text" name="code" class="search-input" id="claimCodeInput" placeholder="e.g. LND-8821" maxlength="20" autocomplete="off" required>
                <button type="submit" class="btn-track">Track My Laundry</button>
            </form>

            <div class="search-error" id="searchError"></div>

            <div class="help-text">
                <strong>Don't have your code?</strong><br>
                Check the receipt given to you at drop-off, or the SMS we sent you.
            </div>
        </div>
    </div>

    <!-- ACTIVE RESULT VIEW -->
    <div id="activeView" style="display: <?= (isset($order) && $order['status'] === 'active') ? 'block' : 'none' ?>;">
        <button class="back-link" onclick="window.location.href='<?= base_url('track') ?>'">← Track another order</button>

        <div class="result-card">
            <div class="result-header">
                <div class="status-label" id="statusLabel">In Progress</div>
                <div class="status-title" id="statusTitle">Washing</div>
                <div class="claim-code-pill" id="resultClaimCode"><?= isset($order) ? esc($order['claim_code']) : '' ?></div>
            </div>

            <div class="info-banner" id="infoBanner"></div>

            <div class="timeline-wrap">
                <div class="timeline" id="timeline"></div>
            </div>

            <div class="payment-footer" id="paymentFooter"></div>

            <div class="action-row">
                <button class="action-btn" onclick="window.location.reload()">
    <img 
        src="<?= base_url('images/refresh.svg') ?>" 
        alt="Completed"
        style="width: 16px; height: 16px;"
    >
Refresh Status</button>
            </div>
        </div>
    </div>

    <!-- COMPLETED VIEW -->
    <div id="completedView" style="display: <?= (isset($order) && $order['status'] === 'completed') ? 'block' : 'none' ?>;">
        <button class="back-link" onclick="window.location.href='<?= base_url('track') ?>'">← Track another order</button>

        <div class="result-card">
            <div class="completed-header">
                <div class="completed-check">
    <img 
        src="<?= base_url('images/check-box-list-circle.svg') ?>" 
        alt="Completed"
        style="width: 48px; height: 48px; object-fit: contain;"
    >
</div>
                <h2>Order Completed</h2>
                <div class="claim-code-pill"><?= isset($order) ? esc($order['claim_code']) : '' ?></div>
            </div>

            <div class="completed-details" id="completedDetails"></div>

            <div class="completed-footer">
                <p>Thank you for choosing <?= esc($settings['shop_name'] ?? 'SS Laundry') ?>!</p>
                <button class="btn-track" onclick="window.location.href='<?= base_url('track') ?>'">Track another order</button>
            </div>
        </div>
    </div>

    <!-- CANCELLED VIEW -->
    <div id="cancelledView" style="display: <?= (isset($order) && $order['status'] === 'cancelled') ? 'block' : 'none' ?>;">
        <button class="back-link" onclick="window.location.href='<?= base_url('track') ?>'">← Track another order</button>

        <div class="result-card">
            <div class="cancelled-header">
              <div class="cancelled-cross" style="color: #ef4444;">✕</div>
                <h2>Order Cancelled</h2>
                <div class="claim-code-pill"><?= isset($order) ? esc($order['claim_code']) : '' ?></div>
            </div>

            <div class="completed-details" id="cancelledDetails"></div>

            <div class="completed-footer">
                <p>If you have questions about this order, please contact us.</p>
                <a href="tel:<?= esc($settings['shop_phone'] ?? '09171234567') ?>" class="btn-track" style="background: var(--text-muted); text-decoration: none; display: inline-block; text-align: center;">
                    📞 Call <?= esc($settings['shop_phone'] ?? '0917-123-4567') ?>
                </a>
            </div>
        </div>
    </div>

</div>

<div class="page-footer">
    <?= esc($settings['shop_name'] ?? 'SS Laundry') ?> · <?= esc($settings['shop_address'] ?? '123 Main St, Manila') ?><br>
    Need help with your order? <a href="tel:<?= esc($settings['shop_phone'] ?? '09171234567') ?>">Call <?= esc($settings['shop_phone'] ?? '0917-123-4567') ?></a><br>
    <a href="#">Privacy Policy</a> 
</div>

<script>
const PIPELINES = {
    "Wash & Fold": [
        { key: "intake",  label: "Dropped Off",       desc: "We received your laundry" },
        { key: "washing", label: "Washing",           desc: "Clothes are being washed" },
        { key: "drying",  label: "Drying",            desc: "Clothes are being dried" },
        { key: "folding", label: "Folding",           desc: "Being folded and prepared" },
        { key: "ready",   label: "Ready for Pickup",  desc: "Please come pick up your laundry" }
    ],
    "Wash & Dry": [
        { key: "intake",  label: "Dropped Off",       desc: "We received your laundry" },
        { key: "washing", label: "Washing",           desc: "Clothes are being washed" },
        { key: "drying",  label: "Drying",            desc: "Clothes are being dried" },
        { key: "ready",   label: "Ready for Pickup",  desc: "Please come pick up your laundry" }
    ]
};

const orderData = <?= isset($order) ? json_encode($order) : 'null' ?>;

document.addEventListener("DOMContentLoaded", () => {
    if (!orderData) return;

    if (orderData.status === 'completed') {
        renderCompleted(orderData);
    } else if (orderData.status === 'cancelled') {
        renderCancelled(orderData);
    } else {
        renderActive(orderData);
    }
});

function renderActive(order) {
    const pipeline = PIPELINES[order.service] || PIPELINES["Wash & Fold"];
    const currentIdx = pipeline.findIndex(s => s.key === order.stage);
    const currentStage = pipeline[currentIdx];

    const isReady = order.stage === "ready";
    document.getElementById("statusLabel").textContent = isReady ? "Ready" : "In Progress";
    document.getElementById("statusTitle").textContent = currentStage.label;

    const infoBanner = document.getElementById("infoBanner");
    if (isReady) {
        infoBanner.innerHTML = `
            <div class="info-label">Status</div>
            <div class="info-value green">✓ Ready for Pickup</div>
        `;
    } else {
        infoBanner.innerHTML = `
            <div class="info-label">Current Status</div>
            <div class="info-value">Processing...</div>
        `;
    }

    const timeline = document.getElementById("timeline");
    timeline.innerHTML = pipeline.map((step, i) => {
        let cls = "";
        if (i < currentIdx) cls = "completed";
        else if (i === currentIdx) cls = "active";

        let desc = step.desc;
        if (i === currentIdx && order.stage === "ready") {
            desc = "Your laundry is ready — please come pick it up.";
        } else if (i === currentIdx) {
            desc = step.desc + "...";
        } else if (i < currentIdx) {
            desc = "Completed";
        } else {
            desc = "Pending";
        }

        return `
            <div class="t-step ${cls}">
                <div class="t-dot"></div>
                <div class="t-content">
                    <h3>${step.label}</h3>
                    <p>${desc}</p>
                </div>
            </div>
        `;
    }).join("");

    const paymentFooter = document.getElementById("paymentFooter");
    const isPaid = order.paid === true || order.paid === 't' || order.paid === 'true' || order.paid === '1';
    const amountLabel = isPaid ? "Paid" : "Amount Due";
    const amountClass = isPaid ? "paid" : "";
    const badge = isPaid
        ? `<span class="payment-badge">✓ Paid</span>`
        : `<span class="payment-badge unpaid">Pay at Pickup</span>`;

    paymentFooter.innerHTML = `
        <div>
            <div class="label">${amountLabel}</div>
            <div class="amount ${amountClass}">₱${parseFloat(order.price).toFixed(2)}</div>
        </div>
        ${badge}
    `;
}

function renderCompleted(order) {
    document.getElementById("completedDetails").innerHTML = `
        <div class="detail-row">
            <span class="label">Customer</span>
            <span class="value">${order.name}</span>
        </div>
        <div class="detail-row">
            <span class="label">Service</span>
            <span class="value">${order.service} · ${order.weight}kg</span>
        </div>
        <div class="detail-row">
            <span class="label">Picked up</span>
            <span class="value">${new Date(order.completed_at).toLocaleString()}</span>
        </div>
        <div class="detail-row highlight">
            <span class="label">Amount paid</span>
            <span class="value">₱${parseFloat(order.price).toFixed(2)}</span>
        </div>
    `;
}

function renderCancelled(order) {
    document.getElementById("cancelledDetails").innerHTML = `
        <div class="detail-row">
            <span class="label">Customer</span>
            <span class="value">${order.name}</span>
        </div>
        <div class="detail-row">
            <span class="label">Service</span>
            <span class="value">${order.service} · ${order.weight}kg</span>
        </div>
        <div class="detail-row">
            <span class="label">Status</span>
            <span class="value">Cancelled</span>
        </div>
    `;
}
</script>

</body>
</html>