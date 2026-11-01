<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Orders</title>
    <link rel="stylesheet" href="<?= base_url('css/admin_global.css') ?>">
    <style>
    /* =========================================
       COMPLETED ORDERS PAGE SPECIFIC STYLES
       ========================================= */

    /* Topbar Export Button */
    .btn-export { background: white; color: var(--text); border: 1px solid var(--border); padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: 0.2s; }
    .btn-export:hover { background: #f8fafc; border-color: var(--primary); color: var(--primary); }

    /* Summary Stats */
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--text); margin-top: 0.5rem; letter-spacing: -0.02em; }
    .stat-value.green { color: var(--success); }
    .stat-value.blue { color: var(--primary); }

    /* Toolbar & Filters */
    .toolbar { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); flex-wrap: wrap; gap: 0.75rem; }
    .filters { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
    .filter-select { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.85rem; outline: none; background: white; cursor: pointer; }
    .search-input { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.85rem; outline: none; width: 250px; }

    /* Table */
    .table-container { background: white; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { background: #f8fafc; padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); cursor: pointer; user-select: none; }
    th:hover { background: #f1f5f9; color: var(--primary); }
    td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }

    /* Table Elements */
    .claim-code { font-family: monospace; font-weight: 700; color: var(--primary); background: #eff6ff; padding: 0.2rem 0.5rem; border-radius: 4px; }
    .cust-info { display: flex; flex-direction: column; }
    .cust-name { font-weight: 600; }
    .cust-phone { font-size: 0.75rem; color: var(--text-muted); }
    .amount { font-weight: 700; color: var(--success); font-family: monospace; }
    .date { font-size: 0.8rem; color: var(--text-muted); }
    .date strong { color: var(--text); font-weight: 600; display: block; }
    
    .btn-view { background: none; border: 1px solid var(--border); color: var(--text); padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: 0.15s; }
    .btn-view:hover { background: var(--primary); color: white; border-color: var(--primary); }

    /* Receipt Modal */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 1000; justify-content: center; align-items: center; }
    .modal-overlay.active { display: flex; }
    .modal-content { background: white; width: 100%; max-width: 420px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.3s ease; }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-header { padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { font-size: 1.1rem; color: var(--text); }
    .btn-close { background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; line-height: 1; }
    .modal-body { padding: 1.5rem; }
    
    .receipt-row { display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9rem; border-bottom: 1px dashed var(--border); }
    .receipt-row:last-child { border-bottom: none; }
    .receipt-row .label { color: var(--text-muted); }
    .receipt-row .value { font-weight: 600; }
    
    .receipt-total { background: #ecfdf5; margin: 1rem -1.5rem -1.5rem; padding: 1.5rem; text-align: center; }
    .receipt-total .label { font-size: 0.75rem; color: #059669; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
    .receipt-total .amount { font-size: 1.75rem; color: var(--success); margin-top: 0.25rem; }
</style>
</head>
<body>

<div class="admin-layout">
   <aside class="sidebar">
    <?= $this->include('partials/admin_sidebar') ?>
  </aside>

    <div class="main-content">
        <header class="topbar">
            <h2 style="font-size: 1.25rem; color: var(--text);">Completed Orders</h2>
            <button class="btn-export" onclick="exportCompletedCsv()"><img
                                src="<?= base_url('images/download-icon-order.svg') ?>"
                                alt=""
                                class="chart-icon"
                            > Export CSV</button>
        </header>

        <div class="content-area">

            <!-- STATS -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label">Today's Revenue</div>
                    <div class="stat-value green">₱<?= number_format($todayRevenue, 2) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Orders Today</div>
                    <div class="stat-value blue"><?= $todayOrders ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?= count($orders) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Avg. Order Value</div>
                    <div class="stat-value">₱<?= number_format($avgOrder, 2) ?></div>
                </div>
            </div>

            <!-- TOOLBAR -->
            <div class="toolbar">
                <div class="filters">
                    <select class="filter-select" id="filter-date">
                        <option value="all" selected>All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                    
                    <select class="filter-select" id="filter-service">
                        <option value="">All Services</option>
                        <option value="Wash & Fold">Wash & Fold</option>
                        <option value="Wash & Dry">Wash & Dry</option>
                    </select>
                </div>
                <input type="text" class="search-input" id="search-input" placeholder="Search claim code or name...">
            </div>

            <!-- TABLE -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th>Claim Code ↕</th>
                            <th>Customer ↕</th>
                            <th>Service ↕</th>
                            <th>Amount ↕</th>
                            <th>Completed At ↕</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">No completed orders yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $index => $order): ?>
                                <tr class="order-row" 
                                    data-service="<?= esc($order['service']) ?>"
                                    data-name="<?= esc($order['name']) ?>"
                                    data-code="<?= esc($order['claim_code']) ?>"
                                    data-date="<?= date('Y-m-d', strtotime($order['completed_at'])) ?>"
                                    data-phone="<?= esc($order['phone']) ?>"
                                    data-weight="<?= esc($order['weight']) ?>"
                                    data-price="<?= esc($order['price']) ?>"
                                    data-time="<?= date('g:i A', strtotime($order['completed_at'])) ?>"
                                >
                                    <td style="text-align: center; color: var(--text-muted); font-weight: 600;"><?= $index + 1 ?></td>
                                    <td><span class="claim-code"><?= esc($order['claim_code']) ?></span></td>
                                    <td>
                                        <div class="cust-info">
                                            <span class="cust-name"><?= esc($order['name']) ?></span>
                                            <span class="cust-phone"><?= esc($order['phone']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem; color: <?= $order['service'] === 'Wash & Dry' ? '#d97706' : '#2563eb' ?>;">
                                            <?= esc($order['service']) ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= esc($order['weight']) ?> kg</div>
                                    </td>
                                    <td><span class="amount">₱<?= number_format($order['price'], 2) ?></span></td>
                                    <td>
                                        <div class="date">
                                            <strong><?= date('M j, Y', strtotime($order['completed_at'])) ?></strong>
                                            <?= date('g:i A', strtotime($order['completed_at'])) ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- The View Button passes the whole row into the JS -->
                                        <button class="btn-view" onclick="openReceipt(this)">View</button>
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

<!-- RECEIPT MODAL -->
<div class="modal-overlay" id="receiptModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Order Receipt</h3>
            <button class="btn-close" onclick="closeModal('receiptModal')">&times;</button>
        </div>
        <div class="modal-body" id="receipt-body">
            <!-- Content injected by JS -->
        </div>
    </div>
</div>

<script>
    // =========================================
// EXPORT CSV (respects active filters)
// =========================================
function exportCompletedCsv() {
    // Grab only the rows that are currently visible (not filtered out)
    const rows = Array.from(document.querySelectorAll('.order-row'))
        .filter(row => row.style.display !== 'none');

    if (rows.length === 0) {
        alert('No orders to export with the current filters.');
        return;
    }

    // Header row
    const csvData = [
        ['Claim Code', 'Customer', 'Phone', 'Service', 'Weight (kg)', 'Amount (PHP)', 'Date', 'Time']
    ];

    // Map each visible row's data-* attributes into a row
    rows.forEach(row => {
        csvData.push([
            row.dataset.code,
            row.dataset.name,
            row.dataset.phone,
            row.dataset.service,
            row.dataset.weight,
            parseFloat(row.dataset.price).toFixed(2),
            row.dataset.date,
            row.dataset.time,
        ]);
    });

    // Build CSV string with proper escaping (handles commas/quotes in names)
    const csv = csvData
        .map(r => r.map(c => `"${String(c ?? '').replace(/"/g, '""')}"`).join(','))
        .join('\r\n');

    // \uFEFF BOM so Excel reads ₱ and accents correctly
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `completed-orders-${new Date().toISOString().slice(0, 10)}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

    // =========================================
    // 1. LIVE FILTERING (Date, Service, Search)
    // =========================================
    function filterTable() {
        const searchTerm = document.getElementById("search-input").value.toLowerCase().trim();
        const serviceFilter = document.getElementById("filter-service").value;
        const dateFilter = document.getElementById("filter-date").value;

        // Set "Today" to exactly midnight (00:00:00) for accurate comparison
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Get tomorrow's date to ensure we don't accidentally block today due to timezone hours
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        document.querySelectorAll('.order-row').forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const code = row.dataset.code.toLowerCase();
            const service = row.dataset.service;
            
            // Parse the row's date (YYYY-MM-DD)
            const orderDate = new Date(row.dataset.date);
            orderDate.setHours(0, 0, 0, 0); 

            const matchSearch = name.includes(searchTerm) || code.includes(searchTerm);
            const matchService = serviceFilter === "" || service === serviceFilter;
            
            // Strict Date Logic
            let matchDate = true;
            
            if (dateFilter === "today") {
                // Must be exactly today
                matchDate = orderDate.getTime() === today.getTime();
            } 
            else if (dateFilter === "week") {
                const oneWeekAgo = new Date();
                oneWeekAgo.setDate(today.getDate() - 7);
                oneWeekAgo.setHours(0, 0, 0, 0);
                
                // Must be >= 7 days ago AND < tomorrow (blocks future dates)
                matchDate = orderDate >= oneWeekAgo && orderDate < tomorrow;
            } 
            else if (dateFilter === "month") {
                const oneMonthAgo = new Date();
                oneMonthAgo.setMonth(today.getMonth() - 1);
                oneMonthAgo.setHours(0, 0, 0, 0);
                
                // Must be >= 1 month ago AND < tomorrow (blocks future dates)
                matchDate = orderDate >= oneMonthAgo && orderDate < tomorrow;
            }

            // Show/Hide the row based on all 3 filters
            if (matchSearch && matchService && matchDate) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    document.getElementById("search-input").addEventListener("input", filterTable);
    document.getElementById("filter-service").addEventListener("change", filterTable);
    document.getElementById("filter-date").addEventListener("change", filterTable);

    // =========================================
    // 2. VIEW RECEIPT MODAL
    // =========================================
    function openModal(id) { document.getElementById(id).classList.add("active"); }
    function closeModal(id) { document.getElementById(id).classList.remove("active"); }

    function openReceipt(btn) {
        const row = btn.closest('.order-row');
        
        document.getElementById('receipt-body').innerHTML = `
            <div style="text-align: center; margin-bottom: 1.5rem;">
        <img 
            src="<?= base_url('images/laundry-icon.png') ?>" 
            alt="LMlogo" 
            style="width: 48px; height: 48px; object-fit: contain; margin-bottom: 0.5rem;"
        >
        <h2 style="font-size: 1.25rem; color: var(--text);">LaundryMate</h2>
        <p style="font-size: 0.8rem; color: var(--text-muted);">123 Main St, Manila</p>
    </div>
            
            <div class="receipt-row">
                <span class="label">Receipt No.</span>
                <span class="value" style="font-family: monospace; color: var(--primary);">${row.dataset.code}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Customer</span>
                <span class="value">${row.dataset.name}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Phone</span>
                <span class="value">${row.dataset.phone}</span>
            </div>
            
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--border);"></div>
            
            <div class="receipt-row">
                <span class="label">Service</span>
                <span class="value">${row.dataset.service} (${row.dataset.weight} kg)</span>
            </div>
            <div class="receipt-row">
                <span class="label">Date Paid</span>
                <span class="value">${row.querySelector('.date strong').textContent} · ${row.dataset.time}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Payment Status</span>
                <span class="value" style="color: var(--success);">✓ Paid in Full</span>
            </div>

            <div class="receipt-total">
                <div class="label">Total Amount Paid</div>
                <div class="amount">₱${parseFloat(row.dataset.price).toFixed(2)}</div>
            </div>
        `;

        openModal('receiptModal');
    }
</script>

</body>
</html>