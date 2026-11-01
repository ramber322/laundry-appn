<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'SS Laundry Staff') ?></title>
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="<?= base_url('css/admin_global.css') ?>">
    
    <!-- Page-Specific CSS -->
    <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <?= $this->include('partials/admin_sidebar') ?>
    </aside>

    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <h2 style="font-size: 1.25rem; color: var(--text);"><?= esc($pageTitle ?? 'Dashboard') ?></h2>
            <?= $this->renderSection('topbar_actions') ?>
        </header>

        <div class="content-area">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<!-- Page-Specific JS -->
<?= $this->renderSection('scripts') ?>

</body>
</html>