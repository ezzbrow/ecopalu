<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2>Edit Penjemputan</h2>

<form action="<?= base_url('penjemputan/update/'.$penjemputan['id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Kategori Sampah</label>
        <select name="kategori_sampah_id" class="form-control" required>
            <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id'] ?>"
                    <?= $k['id'] === $penjemputan['kategori_sampah_id'] ? 'selected' : '' ?>>
                    <?= esc($k['nama_kategori']) ?> (<?= $k['coin_value'] ?> coin/kg)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Berat (Kg)</label>
        <input type="number" step="0.01" name="berat"
               value="<?= $penjemputan['berat'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Jemput</label>
        <input type="date" name="tanggal_jemput"
               value="<?= $penjemputan['tanggal_jemput'] ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2" required><?= esc($penjemputan['alamat']) ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Latitude</label>
            <input type="number" step="0.0000001" name="latitude"
                   value="<?= $penjemputan['latitude'] ?>" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Longitude</label>
            <input type="number" step="0.0000001" name="longitude"
                   value="<?= $penjemputan['longitude'] ?>" class="form-control">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <?php foreach (['menunggu','disetujui','menunggu_pemberian_poin','selesai','ditolak'] as $opt): ?>
                <option value="<?= $opt ?>"
                    <?= $penjemputan['status'] === $opt ? 'selected' : '' ?>>
                    <?= ucfirst(str_replace('_', ' ', $opt)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-eco">Update</button>
    <a href="<?= base_url('penjemputan?role=admin') ?>" class="btn btn-secondary">Kembali</a>

</form>

<?= $this->endSection() ?>