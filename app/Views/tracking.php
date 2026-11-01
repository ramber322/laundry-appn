<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Laundry - <?= esc($settings['shop_name'] ?? 'SS Laundry') ?></title>
    <style>
        :root {
            --bg: #f4f7f9;
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --success: #10b981;
            --warning: #f59e0b;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --card-bg: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ---- TOP BAR (brand) ---- */
        .brand-bar {
            width: 100%;
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: center;
        }
        .brand-inner {
            width: 100%;
            max-width: 520px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }
        .brand-icon {
            width: 34px; height: 34px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            display: grid; place-items: center;
            font-size: 1.1rem;
        }

        /* ---- MAIN CONTAINER ---- */
        .container {
            width: 100%;
            max-width: 520px;
            padding: 1.5rem;
            flex: 1;
        }

        /* ---- SEARCH VIEW ---- */
        .search-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem 1.75rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            text-align: center;
            animation: fadeIn 0.3s ease;
        }
        .search-icon { font-size: 3rem; margin-bottom: 1rem; }
        .search-card h1 { font-size: 1.4rem; margin-bottom: 0.5rem; color: var(--text); }
        .search-card p { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.5rem; }
        .search-form { display: flex; flex-direction: column; gap: 0.75rem; }
        .search-input {
            width: 100%; padding: 0.9rem 1rem; border: 2px solid var(--border); border-radius: 10px;
            font-size: 1rem; font-family: monospace; text-transform: uppercase; letter-spacing: 1.5px;
            text-align: center; outline: none; transition: border-color 0.15s;
        }
        .search-input:focus { border-color: var(--primary); }
        .search-input::placeholder { text-transform: none; letter-spacing: 0; font-family: 'Segoe UI', sans-serif; color: #cbd5e1; }
        .btn-track {
            width: 100%; padding: 0.9rem 1rem; background: var(--primary); color: white;
            border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: background 0.15s;
        }
        .btn-track:hover { background: var(--primary-dark); }

        .search-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; margin-top: 1rem; display: none; }
        .search-error.show { display: block; }

        .help-text { font-size: 0.75rem; color: var(--text-muted); margin-top: 1.5rem; line-height: 1.5; }
        .help-text strong { color: var(--text); }

        /* ---- RESULT VIEW ---- */
        .result-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); overflow: hidden; animation: slideUp 0.35s ease; }
        @keyframes slideUp { from { transform: translateY(15px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .result-header { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; padding: 1.75rem 1.5rem; text-align: center; position: relative; }
        .result-header .status-label { font-size: 0.75rem; opacity: 0.85; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 0.4rem; }
        .result-header .status-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .result-header .claim-code-pill { display: inline-block; background: rgba(255,255,255,0.2); padding: 0.4rem 1rem; border-radius: 20px; font-family: monospace; font-size: 0.95rem; font-weight: 700; letter-spacing: 1.5px; margin-top: 0.5rem; }

        .info-banner { background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); text-align: center; }
        .info-banner .info-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
        .info-banner .info-value { font-size: 1.15rem; font-weight: 700; color: var(--text); margin-top: 0.25rem; }
        .info-banner .info-value.green { color: var(--success); }

        .timeline-wrap { padding: 1.75rem 1.5rem; }
        .timeline { position: relative; padding-left: 2.25rem; }
        .timeline::before { content: ''; position: absolute; left: 9px; top: 8px; bottom: 8px; width: 2px; background: var(--border); }
        .t-step { position: relative; margin-bottom: 1.75rem; }
        .t-step:last-child { margin-bottom: 0; }
        .t-dot { position: absolute; left: -2.25rem; top: 2px; width: 20px; height: 20px; border-radius: 50%; background: white; border: 3px solid var(--border); display: grid; place-items: center; font-size: 0.65rem; font-weight: 900; color: white; transition: all 0.3s; }
        .t-step.completed .t-dot { background: var(--success); border-color: var(--success); }
        .t-step.completed .t-dot::before { content: "✓"; }
        .t-step.active .t-dot { background: white; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(59,130,246,0.15); }
        .t-step.active .t-dot::before { content: ""; width: 8px; height: 8px; border-radius: 50%; background: var(--primary); }

        .t-content h3 { font-size: 1rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.2rem; }
        .t-step.completed .t-content h3 { color: var(--text); }
        .t-step.active .t-content h3 { color: var(--primary); font-weight: 700; }
        .t-content p { font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; }
        .t-step.active .t-content p { color: var(--primary); font-weight: 500; }

        .payment-footer { background: #f8fafc; padding: 1.25rem 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .payment-footer .label { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.15rem; }
        .payment-footer .amount { font-size: 1.25rem; font-weight: 700; color: var(--text); }
        .payment-footer .amount.paid { color: var(--success); }
        .payment-badge { background: #ecfdf5; color: #059669; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid #a7f3d0; }
        .payment-badge.unpaid { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }

        .action-row { padding: 1rem 1.5rem 1.5rem; display: flex; flex-direction: column; gap: 0.5rem; }
        .action-btn { width: 100%; padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid var(--border); background: white; color: var(--text); font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none; transition: all 0.15s; }
        .action-btn:hover { background: #f8fafc; border-color: var(--primary); color: var(--primary); }

        .page-footer { text-align: center; padding: 1.5rem; font-size: 0.75rem; color: var(--text-muted); line-height: 1.6; }
        .page-footer a { color: var(--primary); text-decoration: none; }
        .page-footer a:hover { text-decoration: underline; }

        .back-link { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; color: var(--text-muted); text-decoration: none; margin-bottom: 1rem; cursor: pointer; background: none; border: none; padding: 0.25rem 0; font-family: inherit; }
        .back-link:hover { color: var(--primary); }

        /* COMPLETED & CANCELLED VIEWS */
        .completed-header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 2rem 1.5rem; text-align: center; }
        .completed-check { font-size: 3rem; margin-bottom: 0.75rem; line-height: 1; }
        .completed-header h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; }
        .completed-header .claim-code-pill { display: inline-block; background: rgba(255,255,255,0.2); padding: 0.4rem 1rem; border-radius: 20px; font-family: monospace; font-size: 0.95rem; font-weight: 700; letter-spacing: 1.5px; }
        .completed-details { padding: 1.5rem; }
        .completed-details .detail-row { display: flex; justify-content: space-between; padding: 0.85rem 0; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .completed-details .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
        .completed-details .detail-row .label { color: var(--text-muted); }
        .completed-details .detail-row .value { font-weight: 600; color: var(--text); text-align: right; }
        .completed-details .detail-row.highlight { background: #ecfdf5; margin: 0.5rem -1.5rem 0; padding: 1rem 1.5rem; border-bottom: none; }
        .completed-details .detail-row.highlight .value { color: var(--success); font-size: 1.15rem; font-weight: 700; }
        .completed-footer { background: #f8fafc; padding: 1.5rem; text-align: center; border-top: 1px solid var(--border); }
        .completed-footer p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.5; }
        .completed-footer .btn-track { display: inline-block; width: auto; padding: 0.75rem 1.5rem; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.9rem; transition: background 0.15s; }
        .completed-footer .btn-track:hover { background: var(--primary-dark); }

        .cancelled-header { background: linear-gradient(135deg, #64748b, #475569); color: white; padding: 2rem 1.5rem; text-align: center; }
        .cancelled-cross { font-size: 3rem; margin-bottom: 0.75rem; line-height: 1; font-weight: 700; }
        .cancelled-header h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; }
        .cancelled-header .claim-code-pill { display: inline-block; background: rgba(255,255,255,0.2); padding: 0.4rem 1rem; border-radius: 20px; font-family: monospace; font-size: 0.95rem; font-weight: 700; letter-spacing: 1.5px; }
    </style>
</head>
<body>

<!-- Brand bar -->
<div class="brand-bar">
    <div class="brand-inner">
        <div class="brand-icon">🧺</div>
        <span><?= esc($settings['shop_name'] ?? 'SS Laundry') ?></span>
    </div>
</div>

<div class="container">
    
    <!-- ERROR MESSAGE (If redirect back with error) -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="search-error show" style="margin-bottom: 1rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- SEARCH VIEW -->
    <div id="searchView" style="display: <?= isset($order) ? 'none' : 'block' ?>;">
        <div class="search-card">
            <div class="search-icon">🔍</div>
            <h1>Track Your Laundry</h1>
            <p>Enter the claim code from your receipt or SMS to see your order's live status.</p>

            <!-- Form submits to the controller via GET -->
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
                <button class="action-btn" onclick="window.location.reload()">🔄 Refresh Status</button>
            </div>
        </div>
    </div>

    <!-- COMPLETED VIEW -->
    <div id="completedView" style="display: <?= (isset($order) && $order['status'] === 'completed') ? 'block' : 'none' ?>;">
        <button class="back-link" onclick="window.location.href='<?= base_url('track') ?>'">← Track another order</button>

        <div class="result-card">
            <div class="completed-header">
                <div class="completed-check">✅</div>
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
                <div class="cancelled-cross">✕</div>
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
/* =========================================
   SERVICE PIPELINES
   ========================================= */
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

// PHP INJECTS THE REAL ORDER DATA HERE
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