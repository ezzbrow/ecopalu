<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>

    <!-- ============== EDUKASI ============== -->
    <h3 class="mb-4">Edukasi</h3>

    <p class="text-muted">Pelajari cara memilah sampah dengan benar, merawat lingkungan, dan memaksimalkan poin EcoPalu Anda.</p>

    <div class="row g-4 mt-3">
        <div class="col-md-6">
            <div class="edu-card">
                <div class="edu-icon">♻️</div>
                <h5>Panduan Memilah Sampah</h5>
                <p class="text-muted small">
                    Pisahkan sampah menjadi 5 kategori utama: botol plastik, kardus, kaleng, besi, dan botol kaca.
                    Jangan campur jenis berbeda dalam satu pengumpulan agar nilai tukar poin lebih optimal.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="edu-card">
                <div class="edu-icon">🧼</div>
                <h5>Cara Membersihkan Sampah Sebelum Disetor</h5>
                <p class="text-muted small">
                    Bilas botol, kaleng, dan wadah lainnya dengan air bersih. Keringkan sebelum disetor.
                    Sampah yang bersih mendapat verifikasi lebih cepat dari Bank Sampah.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="edu-card">
                <div class="edu-icon">🌍</div>
                <h5>Dampak Lingkungan</h5>
                <p class="text-muted small">
                    Sampah plastik membutuhkan ratusan tahun untuk terurai. Dengan mendaur ulang,
                    kita mengurangi pencemaran tanah, air, dan udara di Kota Palu.
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="edu-card">
                <div class="edu-icon">💰</div>
                <h5>Tips Memperbanyak Poin</h5>
                <p class="text-muted small">
                    Kumpulkan minimal 50kg per pengajuan (setara ± 1.600–1.700 botol plastik 1,5L)
                    agar memenuhi syarat minimal pencairan. Pisahkan jenis sampah untuk nilai tukar lebih tinggi.
                </p>
            </div>
        </div>
    </div>

    <div class="alert alert-light border mt-4 small text-muted">
        💡 <strong>Catatan:</strong> Konten edukasi masih bisa diperkaya. Versi ini adalah kerangka awal untuk memenuhi spec CLAUDE.md.
    </div>

<?= $this->endSection() ?>

<style>
    .edu-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
    .edu-icon {
        font-size: 2.5rem;
        margin-bottom: 12px;
    }
    .edu-card h5 {
        color: #0f172a;
        font-weight: 700;
        margin-bottom: 8px;
    }
</style>