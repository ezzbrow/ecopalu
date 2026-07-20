<!DOCTYPE html>
<html>
<head>

    <title>EcoPalu Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">

</head>

<body>

<!-- SIDEBAR -->
<?= $this->include('layouts/sidebar') ?>

<!-- MAIN CONTENT -->
<div class="main-content" style="margin-left:250px; padding:20px;">

    <?= $this->renderSection('content') ?>

</div>

<!-- FOOTER -->
<?= $this->include('layouts/footer') ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>