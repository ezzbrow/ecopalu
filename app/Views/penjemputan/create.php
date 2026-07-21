<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2>Ajukan Penjemputan Sampah</h2>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<form action="<?= base_url('penjemputan/store') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Kategori Sampah</label>
        <select name="kategori_sampah_id" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id'] ?>">
                    <?= esc($k['nama_kategori']) ?> (<?= $k['coin_value'] ?> coin/kg)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Berat Sampah (Kg)</label>
        <input type="number" step="0.01" name="berat" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Jemput</label>
        <input type="date" name="tanggal_jemput"
               id="tanggal_jemput"
               class="form-control"
               required>
        <small id="tanggal_jemput_feedback" class="text-danger d-none">
            Tanggal penjemputan hanya bisa dipilih pada hari Rabu atau Sabtu.
        </small>
        <small class="text-muted">
            Pilih hari <strong>Rabu</strong> atau <strong>Sabtu</strong> (hari penjemputan Bank Sampah).
        </small>
    </div>

    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="2" required
                  placeholder="Alamat lengkap penjemputan"></textarea>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Latitude</label>
            <input type="number" step="0.0000001" name="latitude" class="form-control"
                   placeholder="Akan diisi dari peta (Leaflet)">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Longitude</label>
            <input type="number" step="0.0000001" name="longitude" class="form-control"
                   placeholder="Akan diisi dari peta (Leaflet)">
        </div>
    </div>

    <button type="submit" id="submitBtn" class="btn btn-eco">Kirim Pengajuan</button>
    <a href="<?= base_url('penjemputan') ?>" class="btn btn-secondary">Batal</a>

</form>

<script>
(function () {
    const input  = document.getElementById('tanggal_jemput');
    const fb     = document.getElementById('tanggal_jemput_feedback');
    const submit = document.getElementById('submitBtn');

    function validate() {
        const v = input.value;
        if (!v) {
            fb.classList.add('d-none');
            submit.disabled = false;
            return;
        }
        const d = new Date(v + 'T00:00:00');
        const day = d.getDay(); // 0=Min ... 3=Rabu ... 6=Sabtu
        const ok = (day === 3 || day === 6);
        if (ok) {
            fb.classList.add('d-none');
            submit.disabled = false;
        } else {
            fb.classList.remove('d-none');
            submit.disabled = true;
        }
    }

    input.addEventListener('change', validate);
    input.addEventListener('input', validate);
})();
</script>

<?= $this->endSection() ?>