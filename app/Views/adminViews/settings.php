<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="<?= base_url('css/admin_global.css') ?>">
  <style>
    /* =========================================
       SETTINGS PAGE SPECIFIC STYLES
       ========================================= */

    /* Topbar Save Button */
    .btn-save { background: var(--primary); color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s; }
    .btn-save:hover { background: var(--primary-hover); }

    /* Layout */
    .settings-layout { display: grid; grid-template-columns: 220px 1fr; gap: 1.5rem; align-items: start; }

    /* Settings Sidebar Nav */
    .settings-nav { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 1rem 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); position: sticky; top: 1.5rem; }
    .settings-nav-title { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 700; padding: 0.5rem 1rem; margin-bottom: 0.25rem; }
    .settings-nav-link { display: flex; align-items: center; gap: 0.65rem; padding: 0.65rem 1rem; border-radius: 8px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: 0.15s; cursor: pointer; }
    .settings-nav-link:hover { background: #f8fafc; color: var(--text); }
    .settings-nav-link.active { background: #eff6ff; color: var(--primary); font-weight: 600; }

    /* Settings Content */
    .settings-content { display: flex; flex-direction: column; gap: 1.5rem; }
    .settings-card { background: white; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); scroll-margin-top: 1.5rem; }
    .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 0.95rem; font-weight: 700; color: var(--text); }
    .card-subtitle { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; }

    /* Forms */
    .settings-form { padding: 1.5rem; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group:last-child { margin-bottom: 0; }
    .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text); margin-bottom: 0.4rem; }
    .form-control { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.9rem; outline: none; transition: border-color 0.15s; font-family: inherit; background: white; }
    .form-control:focus { border-color: var(--primary); }
    textarea.form-control { resize: vertical; min-height: 80px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .helper-text { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem; line-height: 1.4; }
    .helper-text code { background: #f1f5f9; padding: 0.1rem 0.3rem; border-radius: 3px; font-size: 0.7rem; color: var(--primary); font-family: monospace; }

    /* Input with Currency Prefix */
    .input-with-prefix { position: relative; display: flex; align-items: center; }
    .input-prefix { position: absolute; left: 0.75rem; font-weight: 700; color: var(--text-muted); pointer-events: none; font-size: 0.9rem; }
    .input-with-prefix .form-control { padding-left: 1.75rem; }

    /* Toast */
    .toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--success); color: white; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); z-index: 2000; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; animation: slideInRight 0.3s ease; }
    @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
</head>
<body>

<div class="admin-layout">
   <aside class="sidebar">
    <?= $this->include('partials/admin_sidebar') ?>
  </aside>

    <div class="main-content">
        <header class="topbar">
            <h2 style="font-size: 1.25rem; color: var(--text);">Settings</h2>
            <!-- Form submit button outside the form using the 'form' attribute -->
            <button type="submit" form="settingsForm" class="btn-save"><img src="<?= base_url('images/save-alt.svg') ?>" 
           alt="" 
           style="width: 14px; height: 14px;  "> Save Changes</button>
        </header>

        <div class="content-area">
            
            <?php if (session()->getFlashdata('success')): ?>
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600;">
                    ✓ <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form id="settingsForm" action="<?= base_url('admin/settings/save') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="settings-layout">
                    <!-- LEFT NAV -->
                    <aside class="settings-nav">
                        <div class="settings-nav-title">Settings</div>
                        <a href="#shop" class="settings-nav-link active" onclick="scrollToSection('shop', this)"><img src="<?= base_url('images/shop-store.svg') ?>" 
           alt="" 
           style="width: 15px; height: 15px;  "> Shop Information</a>
                        <a href="#pricing" class="settings-nav-link" onclick="scrollToSection('pricing', this)"><img src="<?= base_url('images/dollar-currency.svg') ?>" 
           alt="" 
           style="width: 15px; height: 15px;  "> Service Pricing</a>
                        <a href="#sms" class="settings-nav-link" onclick="scrollToSection('sms', this)"><img src="<?= base_url('images/sms-icon.svg') ?>" 
           alt="" 
           style="width: 14px; height: 14px;  "> SMS Templates</a>
                    </aside>

                    <!-- RIGHT CONTENT -->
                    <div class="settings-content">
                        
                        <!-- SHOP INFO -->
                        <div class="settings-card" id="shop">
                            <div class="card-header">
                                <div>
                                    <div class="card-title"><img src="<?= base_url('images/shop-store.svg') ?>" 
           alt="" 
           style="width: 18px; height: 18px; vertical-align: middle; margin-bottom: 4px; margin-right: 6px;"> Shop Information</div>
                                    <div class="card-subtitle">Basic details about your laundry business</div>
                                </div>
                            </div>
                            <div class="settings-form">
                                <div class="form-group">
                                    <label>Shop Name</label>
                                    <input type="text" name="shop_name" class="form-control" value="<?= esc($settings['shop_name'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Shop Address</label>
                                    <input type="text" name="shop_address" class="form-control" value="<?= esc($settings['shop_address'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="shop_phone" class="form-control" value="<?= esc($settings['shop_phone'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- PRICING -->
                        <div class="settings-card" id="pricing">
                            <div class="card-header">
                                <div>
                                  <div class="card-title">
  <img src="<?= base_url('images/dollar-currency.svg') ?>" 
       alt="" 
       style="width: 18px; height: 18px; vertical-align: middle; margin-bottom: 4px; margin-right: 6px;">
  Service Pricing
</div>
                                    <div class="card-subtitle">Price per kg for each service type</div>
                                </div>
                            </div>
                            <div class="settings-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Wash & Fold (per kg)</label>
                                        <div class="input-with-prefix">
                                            <span class="input-prefix">₱</span>
                                            <input type="number" name="rate_wash_fold" class="form-control" value="<?= esc($settings['rate_wash_fold'] ?? '50') ?>" step="1" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Wash & Dry (per kg)</label>
                                        <div class="input-with-prefix">
                                            <span class="input-prefix">₱</span>
                                            <input type="number" name="rate_wash_dry" class="form-control" value="<?= esc($settings['rate_wash_dry'] ?? '50') ?>" step="1" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SMS TEMPLATES -->
                        <div class="settings-card" id="sms">
                            <div class="card-header">
                                <div>
                                    <div class="card-title"><img src="<?= base_url('images/sms-icon.svg') ?>" 
           alt="" 
           style="width: 18px; height: 18px; vertical-align: middle; margin-bottom: 4px; margin-right: 6px;"> SMS Templates</div>
                                    <div class="card-subtitle">Messages sent automatically to customers</div>
                                </div>
                            </div>
                        <div class="settings-form">
    <div class="form-group">
        <label>Intake Confirmation</label>
        <textarea name="sms_intake" class="form-control" rows="3"><?= esc($settings['sms_intake'] ?? '') ?></textarea>
        <div class="helper-text">
            Tags: <code>{name}</code>, <code>{code}</code>, <code>{weight}</code>, <code>{tracking_link}</code>, <code>{shop_name}</code>
        </div>
    </div>
    <div class="form-group">
        <label>Ready for Pickup</label>
        <textarea name="sms_ready" class="form-control" rows="2"><?= esc($settings['sms_ready'] ?? '') ?></textarea>
        <div class="helper-text">
            Tags: <code>{name}</code>, <code>{code}</code>, <code>{shop_name}</code>
        </div>
    </div>
</div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Smooth scroll for the left navigation
    function scrollToSection(id, link) {
        event.preventDefault();
        document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.settings-nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
    }
</script>

</body>
</html>