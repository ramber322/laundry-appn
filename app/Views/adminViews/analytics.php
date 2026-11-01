<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link rel="stylesheet" href="<?= base_url('css/admin_global.css') ?>">
    <style>
    /* =========================================
       ANALYTICS PAGE SPECIFIC STYLES
       ========================================= */

    /* Date Filter Buttons */
    .date-filter { display: flex; gap: 0.25rem; background: #f8fafc; padding: 0.25rem; border: 1px solid var(--border); border-radius: 8px; }
    .date-filter button { padding: 0.4rem 0.85rem; border-radius: 6px; border: none; background: transparent; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: 0.15s; }
    .date-filter button.active { background: var(--primary); color: white; }
    .date-filter button:hover:not(.active) { color: var(--text); }

    /* KPI Cards */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
    .stat-icon { width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center; font-size: 0.9rem; }
    .stat-icon.blue { background: #eff6ff; } .stat-icon.green { background: #ecfdf5; } .stat-icon.amber { background: #fffbeb; } .stat-icon.red { background: #fef2f2; }
    .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--text); margin-top: 0.75rem; letter-spacing: -0.02em; }
    .stat-trend { font-size: 0.75rem; margin-top: 0.35rem; display: flex; align-items: center; gap: 0.35rem; }
    .stat-trend.up { color: var(--success); } .stat-trend.down { color: var(--danger); } .stat-trend.neutral { color: var(--text-muted); }

    /* Charts */
    .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem; }
    .card { background: white; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
    .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 0.95rem; font-weight: 700; }
    .card-subtitle { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; }

    /* Bar Chart */
    .bar-chart { padding: 1.5rem; display: flex; align-items: flex-end; justify-content: space-between; gap: 0.5rem; height: 240px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; height: 100%; justify-content: flex-end; }
    .bar { width: 100%; background: linear-gradient(to top, var(--primary), var(--primary-hover)); border-radius: 6px 6px 0 0; min-height: 4px; transition: all 0.3s; position: relative; }
    .bar-label { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

    /* Donut Chart */
    .donut-wrap { padding: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 2rem; min-height: 240px; }
    .donut { width: 140px; height: 140px; border-radius: 50%; position: relative; flex-shrink: 0; }
    .donut-hole { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90px; height: 90px; background: white; border-radius: 50%; display: grid; place-items: center; text-align: center; }
    .donut-hole .total { font-size: 1.1rem; font-weight: 700; color: var(--text); }
    .donut-hole .label { font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }
    .donut-legend { display: flex; flex-direction: column; gap: 0.65rem; }
    .legend-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
    .legend-label { color: var(--text-muted); }
    .legend-value { margin-left: auto; font-weight: 700; font-size: 0.85rem; color: var(--text); }

    /* Bottom Grid (Snapshot & Unpaid) */
    .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    
    .snapshot-list { padding: 1rem 1.5rem 1.5rem; }
    .snapshot-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
    .snapshot-row:last-child { border-bottom: none; }
    .snapshot-row .snap-label { color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem; }
    .snapshot-row .snap-value { font-weight: 700; color: var(--text); font-family: monospace; font-size: 0.95rem; }
    .snapshot-row .snap-value.green { color: var(--success); } .snapshot-row .snap-value.amber { color: var(--warning); } .snapshot-row .snap-value.red { color: var(--danger); } .snapshot-row .snap-value.blue { color: var(--primary); }
    .snapshot-divider { height: 1px; background: var(--border); margin: 0.5rem 0; }

    .unpaid-list { padding: 0.5rem 0; max-height: 300px; overflow-y: auto; }
    .unpaid-item { display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 1.5rem; border-bottom: 1px solid var(--border); }
    .unpaid-item:last-child { border-bottom: none; }
    .unpaid-info { display: flex; flex-direction: column; gap: 0.15rem; }
    .unpaid-code { font-family: monospace; font-weight: 700; color: var(--primary); font-size: 0.8rem; }
    .unpaid-customer { font-size: 0.8rem; color: var(--text-muted); }
    .unpaid-amount { font-weight: 700; color: var(--danger); font-family: monospace; font-size: 0.95rem; }
    
    .empty-state { text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.85rem; }
</style>
</head>
<body>

<div class="admin-layout">
   <aside class="sidebar">
    <?= $this->include('partials/admin_sidebar') ?>
  </aside>

    <div class="main-content">
        <header class="topbar">
            <h2 style="font-size: 1.25rem; color: var(--text);">Analytics</h2>
            <div class="date-filter">
                <button onclick="setRange(this, 'today')">Today</button>
                <button onclick="setRange(this, 'week')">This Week</button>
                <button onclick="setRange(this, 'month')">This Month</button>
                <button onclick="setRange(this, 'all')" class="active">All Time</button>
            </div>
        </header>

        <div class="content-area">
            <!-- STAT CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label"><span id="label-revenue">Total Revenue</span><div class="stat-icon green"> <img
                                src="<?= base_url('images/dollar-currency.svg') ?>"
                                alt=""
                                class="chart-icon"
                            ></div></div>
                    <div class="stat-value" id="stat-revenue">₱0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><span id="label-orders">Total Orders</span><div class="stat-icon blue"> <img
                                src="<?= base_url('images/cube-box.svg') ?>"
                                alt=""
                                class="chart-icon"
                            ></div></div>
                    <div class="stat-value" id="stat-orders">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Avg Order Value<div class="stat-icon amber"> <img
                                src="<?= base_url('images/chart-analytics-graph.svg') ?>"
                                alt=""
                                class="chart-icon"
                            ></div></div>
                    <div class="stat-value" id="stat-avg">₱0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Unpaid Balance<div class="stat-icon red"> <img
                                src="<?= base_url('images/alert-error.svg') ?>"
                                alt=""
                                class="chart-icon"
                            ></div></div>
                    <div class="stat-value" id="stat-unpaid">₱0</div>
                    <div class="stat-trend down"><span id="unpaid-count">0</span> orders unpaid</div>
                </div>
            </div>

            <!-- CHARTS -->
            <div class="charts-grid">
                <div class="card">
                    <div class="card-header">
                        <div><div class="card-title">Revenue Trend</div><div class="card-subtitle" id="chart-subtitle">Monthly revenue</div></div>
                    </div>
                    <div class="bar-chart" id="barChart"></div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div><div class="card-title">Service Breakdown</div><div class="card-subtitle" id="donut-subtitle">All time</div></div>
                    </div>
                    <div class="donut-wrap">
                        <div class="donut" id="donut">
                            <div class="donut-hole"><div class="total" id="donut-total">0</div><div class="label">Orders</div></div>
                        </div>
                        <div class="donut-legend" id="donut-legend"></div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM ROW -->
            <div class="bottom-grid">
                <div class="card">
                    <div class="card-header">
                        <div><div class="card-title">Today's Snapshot</div><div class="card-subtitle">Always shows today's live data</div></div>
                    </div>
                    <div class="snapshot-list" id="snapshot-list"></div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div><div class="card-title">Unpaid Orders</div><div class="card-subtitle">All outstanding debt</div></div>
                        <span style="font-size:0.8rem; color: var(--danger); font-weight: 700;" id="unpaid-total">₱0</span>
                    </div>
                    <div class="unpaid-list" id="unpaid-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    // 1. PHP INJECTS THE REAL SUPABASE DATA HERE
    const orders = <?= json_encode($orders) ?>;
    
    // 2. STATE
    let currentRange = "all";

    function isToday(dateStr) {
        if (!dateStr) return false;
        const d = new Date(dateStr);
        const today = new Date();
        return d.toDateString() === today.toDateString();
    }

    function getCutoffDate(range) {
        const now = new Date();
        const cutoff = new Date();
        if (range === "today") { cutoff.setHours(0, 0, 0, 0); } 
        else if (range === "week") { cutoff.setDate(now.getDate() - 7); cutoff.setHours(0, 0, 0, 0); } 
        else if (range === "month") { cutoff.setDate(now.getDate() - 30); cutoff.setHours(0, 0, 0, 0); } 
        else { return null; }
        return cutoff;
    }

    function filterByRange(orders, range) {
        if (range === "all") return orders;
        const cutoff = getCutoffDate(range);
        return orders.filter(o => {
            const date = o.completed_at || o.created_at;
            return date && new Date(date) >= cutoff;
        });
    }

    function calculateAnalytics(filteredOrders) {
        const completed = filteredOrders.filter(o => o.status === "completed");
        const totalRevenue = completed.reduce((sum, o) => sum + parseFloat(o.price), 0);
        const avgOrder = completed.length > 0 ? totalRevenue / completed.length : 0;
        const unpaid = filteredOrders.filter(o => o.paid === false || o.paid === 'f' || o.paid === 'false');
        const unpaidTotal = unpaid.reduce((sum, o) => sum + parseFloat(o.price), 0);
        
        const serviceCount = {};
        completed.forEach(o => { serviceCount[o.service] = (serviceCount[o.service] || 0) + 1; });

        return { totalRevenue, totalOrders: completed.length, avgOrder, unpaidTotal, unpaidCount: unpaid.length, unpaidOrders: unpaid, serviceCount };
    }

    function getBarChartData(filteredOrders, range) {
        const completed = filteredOrders.filter(o => o.status === "completed" && o.completed_at);
        if (range === "today") {
            const buckets = [{label:"6AM", s:6, e:9},{label:"9AM",s:9,e:12},{label:"12PM",s:12,e:15},{label:"3PM",s:15,e:18},{label:"6PM",s:18,e:21},{label:"9PM",s:21,e:24}];
            const values = buckets.map(b => completed.filter(o => isToday(o.completed_at) && new Date(o.completed_at).getHours() >= b.s && new Date(o.completed_at).getHours() < b.e).reduce((sum, o) => sum + parseFloat(o.price), 0));
            return { labels: buckets.map(b => b.label), values };
        }
        if (range === "week") {
            const labels = []; const values = [];
            for (let i = 6; i >= 0; i--) {
                const day = new Date(); day.setDate(day.getDate() - i); day.setHours(0,0,0,0);
                const dayEnd = new Date(day); dayEnd.setDate(dayEnd.getDate() + 1);
                const rev = completed.filter(o => new Date(o.completed_at) >= day && new Date(o.completed_at) < dayEnd).reduce((sum, o) => sum + parseFloat(o.price), 0);
                labels.push(day.toLocaleDateString('en-US', { weekday: 'short' })); values.push(rev);
            }
            return { labels, values };
        }
        if (range === "month") {
            const labels = ["Week 1", "Week 2", "Week 3", "Week 4"]; const values = [0,0,0,0];
            completed.forEach(o => {
                const daysAgo = Math.floor((new Date() - new Date(o.completed_at)) / (1000 * 60 * 60 * 24));
                if (daysAgo < 7) values[3] += parseFloat(o.price);
                else if (daysAgo < 14) values[2] += parseFloat(o.price);
                else if (daysAgo < 21) values[1] += parseFloat(o.price);
                else if (daysAgo < 28) values[0] += parseFloat(o.price);
            });
            return { labels, values };
        }
        // All time
        const labels = []; const values = [];
        for (let i = 5; i >= 0; i--) {
            const m = new Date(new Date().getFullYear(), new Date().getMonth() - i, 1);
            const mEnd = new Date(new Date().getFullYear(), new Date().getMonth() - i + 1, 1);
            const rev = completed.filter(o => new Date(o.completed_at) >= m && new Date(o.completed_at) < mEnd).reduce((sum, o) => sum + parseFloat(o.price), 0);
            labels.push(m.toLocaleDateString('en-US', { month: 'short' })); values.push(rev);
        }
        return { labels, values };
    }

    function renderAnalytics() {
        const range = currentRange;
        const filtered = filterByRange(orders, range);
        const data = calculateAnalytics(filtered);
        const allData = calculateAnalytics(orders); // For unpaid (always total)

        // 1. Stat Cards
        document.getElementById("label-revenue").textContent = range === "all" ? "Total Revenue" : "Revenue";
        document.getElementById("label-orders").textContent = range === "all" ? "Total Orders" : "Orders";
        document.getElementById("stat-revenue").textContent = `₱${data.totalRevenue.toLocaleString('en-US', {minimumFractionDigits:0})}`;
        document.getElementById("stat-orders").textContent = data.totalOrders;
        document.getElementById("stat-avg").textContent = `₱${data.avgOrder.toFixed(0)}`;
        
        // Unpaid is always all-time
        document.getElementById("stat-unpaid").textContent = `₱${allData.unpaidTotal.toLocaleString('en-US', {minimumFractionDigits:0})}`;
        document.getElementById("unpaid-count").textContent = allData.unpaidCount;

        // 2. Bar Chart
        const barData = getBarChartData(filtered, range);
        const maxRev = Math.max(...barData.values, 1);
        document.getElementById("barChart").innerHTML = barData.values.map((rev, i) => {
            return `<div class="bar-col"><div class="bar" style="height: ${Math.max((rev/maxRev)*100, 2)}%;"></div><div class="bar-label">${barData.labels[i]}</div></div>`;
        }).join("");
        
        const subtitles = { today: "Revenue by hour (today)", week: "Daily revenue (last 7 days)", month: "Weekly revenue (last 4 weeks)", all: "Monthly revenue (last 6 months)" };
        document.getElementById("chart-subtitle").textContent = subtitles[range];

        // 3. Donut Chart
        const colors = { "Wash & Fold": "#3b82f6", "Wash & Dry": "#f59e0b" };
        const totalSrv = Object.values(data.serviceCount).reduce((a, b) => a + b, 0);
        document.getElementById("donut-total").textContent = totalSrv;
        document.getElementById("donut-subtitle").textContent = range === 'all' ? 'All time' : 'Selected range';

        if (totalSrv === 0) {
            document.getElementById("donut").style.background = "#e2e8f0";
            document.getElementById("donut-legend").innerHTML = `<div style="color: var(--text-muted); font-size: 0.85rem;">No orders</div>`;
        } else {
            let grad = []; let angle = 0;
            Object.entries(data.serviceCount).forEach(([srv, cnt]) => {
                const deg = (cnt / totalSrv) * 360;
                grad.push(`${colors[srv] || '#94a3b8'} ${angle}deg ${angle + deg}deg`);
                angle += deg;
            });
            document.getElementById("donut").style.background = `conic-gradient(${grad.join(", ")})`;
            document.getElementById("donut-legend").innerHTML = Object.entries(data.serviceCount).map(([srv, cnt]) => {
                return `<div class="legend-item"><div class="legend-dot" style="background: ${colors[srv]}"></div><span class="legend-label">${srv}</span><span class="legend-value">${cnt} (${Math.round((cnt/totalSrv)*100)}%)</span></div>`;
            }).join("");
        }

        // 4. Today's Snapshot
        const todayComp = orders.filter(o => o.status === "completed" && isToday(o.completed_at));
        const todayAct = orders.filter(o => o.status === "active" && isToday(o.created_at));
        const todayRev = todayComp.reduce((s, o) => s + parseFloat(o.price), 0);
        const todayUnpaid = orders.filter(o => o.paid === false && isToday(o.created_at)).reduce((s, o) => s + parseFloat(o.price), 0);
        const todayAvg = todayComp.length > 0 ? todayRev / todayComp.length : 0;

        document.getElementById("snapshot-list").innerHTML = `
            <div class="snapshot-row"><span class="snap-label"><img src="<?= base_url('images/dollar-currency.svg') ?>" 
           alt="" 
           style="width: 16px; height: 16px; vertical-align: middle; margin-right: 1px;"> Revenue today</span><span class="snap-value green">₱${todayRev.toFixed(0)}</span></div>
            <div class="snapshot-row"><span class="snap-label"><img src="<?= base_url('images/cube-box.svg') ?>" 
           alt="" 
           style="width: 16px; height: 16px; vertical-align: middle; margin-right: 1px;"> Orders processed</span><span class="snap-value">${todayComp.length}</span></div>
            <div class="snapshot-row"><span class="snap-label"><img src="<?= base_url('images/clock-time-countdown-arrow.svg') ?>" 
           alt="" 
           style="width: 16px; height: 16px; vertical-align: middle; margin-right: 1px;"> Orders in progress</span><span class="snap-value blue">${todayAct.filter(o=>o.stage!=='ready').length}</span></div>
            <div class="snapshot-row"><span class="snap-label"><img src="<?= base_url('images/check-box-list-square.svg') ?>" 
           alt="" 
           style="width: 16px; height: 16px; vertical-align: middle; margin-right: 1px;"> Orders ready</span><span class="snap-value green">${todayAct.filter(o=>o.stage==='ready').length}</span></div>
            <div class="snapshot-divider"></div>
            <div class="snapshot-row"><span class="snap-label"><img src="<?= base_url('images/alert-error.svg') ?>" 
           alt="" 
           style="width: 16px; height: 16px; vertical-align: middle; margin-right: 1px;"> Unpaid today</span><span class="snap-value ${todayUnpaid>0?'red':''}">₱${todayUnpaid.toFixed(0)}</span></div>
            <div class="snapshot-row"><span class="snap-label"><img src="<?= base_url('images/bar-chart-analytics.svg') ?>" 
           alt="" 
           style="width: 16px; height: 16px; vertical-align: middle; margin-right: 1px;"> Avg order value</span><span class="snap-value">₱${todayAvg.toFixed(0)}</span></div>
        `;

        // 5. Unpaid Orders List
        document.getElementById("unpaid-total").textContent = `₱${allData.unpaidTotal.toFixed(0)}`;
        if (allData.unpaidOrders.length === 0) {
            document.getElementById("unpaid-list").innerHTML = `<div class="empty-state">No unpaid orders </div>`;
        } else {
            document.getElementById("unpaid-list").innerHTML = allData.unpaidOrders.map(o => `
                <div class="unpaid-item">
                    <div class="unpaid-info">
                        <div class="unpaid-code">${o.claim_code}</div>
                        <div class="unpaid-customer">${o.name} · ${o.service}</div>
                    </div>
                    <div class="unpaid-amount">₱${parseFloat(o.price).toFixed(0)}</div>
                </div>
            `).join("");
        }
    }

    function setRange(btn, range) {
        document.querySelectorAll(".date-filter button").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        currentRange = range;
        renderAnalytics();
    }

    // Initialize on load
    renderAnalytics();
</script>

</body>
</html>