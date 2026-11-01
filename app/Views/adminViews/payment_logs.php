<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Logs</title>
    <link rel="stylesheet" href="<?= base_url('css/admin_global.css') ?>">
  <style>
    /* =========================================
       PAYMENT LOGS PAGE SPECIFIC STYLES
       ========================================= */

    /* Topbar Export Button */
    .btn-export { background: white; color: var(--text); border: 1px solid var(--border); padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-export:hover { background: #f8fafc; border-color: var(--primary); color: var(--primary); }

    /* Summary Cards */
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .summary-card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .summary-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .summary-value { font-size: 1.75rem; font-weight: 700; margin-top: 0.5rem; letter-spacing: -0.02em; }
    .summary-value.green { color: var(--success); } 
    .summary-value.blue { color: var(--primary); }
    .summary-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem; }

    /* Toolbar & Filters */
    .toolbar { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); flex-wrap: wrap; gap: 0.75rem; }
    .filters { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
    .filter-select { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.85rem; outline: none; background: white; cursor: pointer; }
    .search-input { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.85rem; outline: none; width: 280px; }

    /* Table */
    .table-container { background: white; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { background: #f8fafc; padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); cursor: pointer; user-select: none; }
    td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }

    /* Table Elements */
    .claim-code { font-family: monospace; font-weight: 700; color: var(--primary); background: #eff6ff; padding: 0.2rem 0.5rem; border-radius: 4px; }
    .cust-info { display: flex; flex-direction: column; }
    .cust-name { font-weight: 600; }
    .cust-phone { font-size: 0.75rem; color: var(--text-muted); }
    .amount { font-weight: 700; font-family: monospace; font-size: 0.95rem; color: var(--success); }
    .date-cell { font-size: 0.8rem; color: var(--text-muted); }
    .date-cell strong { color: var(--text); display: block; font-weight: 600; margin-bottom: 2px; }
    
    .empty-state { text-align: center; padding: 3rem; color: var(--text-muted); font-size: 0.9rem; }
</style>
</head>
<body>

<div class="admin-layout">
   <aside class="sidebar">
    <?= $this->include('partials/admin_sidebar') ?>
  </aside>

    <div class="main-content">
        <header class="topbar">
            <h2 style="font-size: 1.25rem; color: var(--text);">Payment Logs</h2>
            <button class="btn-export">📥 Export CSV</button>
        </header>

        <div class="content-area">

            <!-- SUMMARY CARDS -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Total Collected</div>
                    <div class="summary-value green">₱<?= number_format($totalCollected, 2) ?></div>
                    <div class="summary-sub"><?= $totalTransactions ?> payments received</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Total Transactions</div>
                    <div class="summary-value blue"><?= $totalTransactions ?></div>
                    <div class="summary-sub">Completed orders</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Average Payment</div>
                    <div class="summary-value">₱<?= number_format($avgPayment, 2) ?></div>
                    <div class="summary-sub">Per transaction</div>
                </div>
            </div>

            <!-- TOOLBAR -->
            <div class="toolbar">
                <div class="filters">
                    <select class="filter-select" id="filter-range">
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
                <input type="text" class="search-input" id="search-input" placeholder="Search code, name, or phone...">
            </div>

            <!-- TABLE -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th>Claim Code</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Weight</th>
                            <th>Amount Paid</th>
                            <th>Paid On</th>
                        </tr>
                    </thead>
                    <tbody id="payment-body">
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="7" class="empty-state">No payments recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($payments as $index => $p): ?>
                                <tr class="payment-row" 
                                    data-date="<?= date('Y-m-d', strtotime($p['completed_at'])) ?>"
                                    data-service="<?= esc($p['service']) ?>"
                                    data-name="<?= esc($p['name']) ?>"
                                    data-code="<?= esc($p['claim_code']) ?>"
                                    data-phone="<?= esc($p['phone']) ?>"
                                >
                                    <td style="text-align: center; color: var(--text-muted); font-weight: 600;"><?= $index + 1 ?></td>
                                    <td><span class="claim-code"><?= esc($p['claim_code']) ?></span></td>
                                    <td>
                                        <div class="cust-info">
                                            <span class="cust-name"><?= esc($p['name']) ?></span>
                                            <span class="cust-phone"><?= esc($p['phone']) ?></span>
                                        </div>
                                    </td>
                                    <td style="font-size: 0.85rem; font-weight: 600; color: <?= $p['service'] === 'Wash & Dry' ? '#d97706' : '#2563eb' ?>;">
                                        <?= esc($p['service']) ?>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);"><?= esc($p['weight']) ?> kg</td>
                                    <td><span class="amount">₱<?= number_format($p['price'], 2) ?></span></td>
                                    <td>
                                        <div class="date-cell">
                                            <strong><?= date('M j, Y', strtotime($p['completed_at'])) ?></strong>
                                            <?= date('g:i A', strtotime($p['completed_at'])) ?>
                                        </div>
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

<script>
    // =========================================
    // LIVE FILTERING (Date, Service, Search)
    // =========================================
    function filterPayments() {
        const searchTerm = document.getElementById("search-input").value.toLowerCase().trim();
        const serviceFilter = document.getElementById("filter-service").value;
        const rangeFilter = document.getElementById("filter-range").value;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        document.querySelectorAll('.payment-row').forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const code = row.dataset.code.toLowerCase();
            const phone = row.dataset.phone;
            const service = row.dataset.service;
            
            const orderDate = new Date(row.dataset.date);
            orderDate.setHours(0, 0, 0, 0);

            // 1. Search Match
            const matchSearch = name.includes(searchTerm) || code.includes(searchTerm) || phone.includes(searchTerm);
            
            // 2. Service Match
            const matchService = serviceFilter === "" || service === serviceFilter;
            
            // 3. Date Match
            let matchDate = true;
            if (rangeFilter === "today") {
                matchDate = orderDate.getTime() === today.getTime();
            } else if (rangeFilter === "week") {
                const oneWeekAgo = new Date();
                oneWeekAgo.setDate(today.getDate() - 7);
                oneWeekAgo.setHours(0, 0, 0, 0);
                matchDate = orderDate >= oneWeekAgo && orderDate < tomorrow;
            } else if (rangeFilter === "month") {
                const oneMonthAgo = new Date();
                oneMonthAgo.setMonth(today.getMonth() - 1);
                oneMonthAgo.setHours(0, 0, 0, 0);
                matchDate = orderDate >= oneMonthAgo && orderDate < tomorrow;
            }

            // Show/Hide
            if (matchSearch && matchService && matchDate) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    document.getElementById("search-input").addEventListener("input", filterPayments);
    document.getElementById("filter-service").addEventListener("change", filterPayments);
    document.getElementById("filter-range").addEventListener("change", filterPayments);
</script>

</body>
</html>