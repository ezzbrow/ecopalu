<?= $this->include('layouts/header') ?>

<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <?= $this->renderSection('content') ?>
</div>

<?= $this->include('layouts/footer') ?>