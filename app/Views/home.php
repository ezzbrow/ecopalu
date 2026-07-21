<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoPalu — Ubah Sampah Menjadi Nilai yang Bermanfaat</title>

    <!-- Bootstrap 5 + Icons + Poppins (konsisten dengan layout utama) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <style>
        /* Brand & global */
        :root {
            --eco-green: #22C55E;
            --eco-green-light: #DCFCE7;
            --eco-green-dark: #16A34A;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            scroll-behavior: smooth;
        }

        /* Navbar */
        .navbar-eco {
            background: white;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }
        .navbar-eco .navbar-brand {
            color: var(--eco-green);
            font-weight: 700;
            font-size: 1.4rem;
        }
        .navbar-eco .nav-link {
            color: #334155;
            font-weight: 500;
            margin: 0 6px;
            border-radius: 8px;
            padding: 6px 12px !important;
            transition: 0.2s;
        }
        .navbar-eco .nav-link:hover {
            color: var(--eco-green);
            background: var(--eco-green-light);
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, #DCFCE7 0%, #f8fafc 100%);
            padding: 100px 0 80px;
        }
        .hero h1 {
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .hero .lead {
            color: #475569;
            font-size: 1.15rem;
        }
        .hero-icon {
            font-size: 12rem;
            color: var(--eco-green);
            opacity: 0.85;
        }
        .btn-eco {
            background: var(--eco-green);
            border: none;
            color: white;
            font-weight: 500;
            padding: 12px 28px;
            border-radius: 12px;
        }
        .btn-eco:hover {
            background: var(--eco-green-dark);
            color: white;
        }
        .btn-outline-eco {
            border: 2px solid var(--eco-green);
            color: var(--eco-green);
            font-weight: 500;
            padding: 10px 26px;
            border-radius: 12px;
        }
        .btn-outline-eco:hover {
            background: var(--eco-green);
            color: white;
        }

        /* Section umum */
        section {
            padding: 80px 0;
        }
        .section-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .section-subtitle {
            color: #64748b;
            max-width: 700px;
            margin: 0 auto 48px;
        }

        /* Card */
        .card-eco {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        .card-eco:hover {
            transform: translateY(-4px);
        }

        /* Cara kerja (5 langkah) */
        .step-card {
            position: relative;
            padding: 32px 24px 24px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            text-align: center;
            height: 100%;
        }
        .step-number {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 48px;
            height: 48px;
            background: var(--eco-green);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
        .step-icon {
            font-size: 2.4rem;
            color: var(--eco-green);
            margin: 12px 0 16px;
        }

        /* Jenis sampah */
        .sampah-icon {
            font-size: 2.4rem;
            color: var(--eco-green);
            margin-bottom: 8px;
        }
        .coin-badge {
            display: inline-block;
            background: var(--eco-green-light);
            color: var(--eco-green-dark);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 8px;
        }

        /* Mitra */
        .mitra-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            padding: 32px 24px;
            text-align: center;
        }
        .mitra-icon {
            font-size: 3rem;
            color: var(--eco-green);
            margin-bottom: 16px;
        }

        /* Footer */
        .footer-eco {
            background: #0f172a;
            color: #cbd5e1;
            padding: 48px 0 24px;
        }
        .footer-eco a {
            color: #cbd5e1;
            text-decoration: none;
        }
        .footer-eco a:hover {
            color: var(--eco-green);
        }
        .footer-bottom {
            border-top: 1px solid #1e293b;
            padding-top: 20px;
            margin-top: 32px;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- ============== NAVBAR ============== -->
    <nav class="navbar navbar-expand-lg navbar-eco sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">♻ EcoPalu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="#cara-kerja">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link" href="#jenis-sampah">Jenis Sampah</a></li>
                    <li class="nav-item"><a class="nav-link" href="#mitra">Mitra</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('login') ?>" class="btn btn-outline-eco">Masuk</a>
                    <a href="<?= base_url('register') ?>" class="btn btn-eco">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============== HERO ============== -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="display-4 mb-4">Ubah Sampah Menjadi Nilai yang Bermanfaat</h1>
                    <p class="lead mb-4">
                        Platform bank sampah digital untuk warga Kota Palu.
                        Kumpulkan poin dari setiap penyetoran sampah, tukarkan
                        menjadi saldo yang ditransfer langsung ke rekening Anda.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="<?= base_url('register') ?>" class="btn btn-eco btn-lg">
                            <i class="bi bi-person-plus"></i> Daftar Sekarang
                        </a>
                        <a href="#cara-kerja" class="btn btn-outline-eco btn-lg">
                            Pelajari Cara Kerja
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <i class="bi bi-recycle hero-icon"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== TENTANG KAMI ============== -->
    <section id="tentang">
        <div class="container">
            <h2 class="section-title text-center">Tentang EcoPalu</h2>
            <p class="section-subtitle text-center">
                EcoPalu adalah bank sampah digital yang dikembangkan untuk
                lomba BRIDA &amp; Bank Indonesia. Kami mendigitalkan proses
                bank sampah di Kota Palu agar transparan, terukur, dan
                memberi dampak ekonomi langsung bagi masyarakat.
            </p>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-eco p-4 text-center">
                        <i class="bi bi-shield-check sampah-icon"></i>
                        <h5>Transparan</h5>
                        <p class="text-muted small mb-0">
                            Setiap transaksi penjemputan dan penukaran sampah
                            tercatat jelas dan bisa dipantau nasabah secara
                            real-time.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-eco p-4 text-center">
                        <i class="bi bi-cash-coin sampah-icon"></i>
                        <h5>Menghasilkan</h5>
                        <p class="text-muted small mb-0">
                            Sampah yang Anda setor dikonversi menjadi poin
                            dan saldo yang bisa dicairkan langsung ke rekening
                            atau e-wallet.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-eco p-4 text-center">
                        <i class="bi bi-tree sampah-icon"></i>
                        <h5>Berkelanjutan</h5>
                        <p class="text-muted small mb-0">
                            Mendukung pengelolaan sampah warga dan UMKM
                            secara rutin, sehingga menciptakan lingkungan
                            yang lebih bersih dan ramah.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== CARA KERJA (5 LANGKAH) ============== -->
    <section id="cara-kerja" style="background: white;">
        <div class="container">
            <h2 class="section-title text-center">Cara Kerja EcoPalu</h2>
            <p class="section-subtitle text-center">
                Lima langkah mudah untuk mengubah sampahmu menjadi nilai
            </p>

            <div class="row g-4 mt-4">
                <!-- Step 1: Daftar -->
                <div class="col-md-6 col-lg-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <i class="bi bi-person-plus step-icon"></i>
                        <h5>Daftar</h5>
                        <p class="text-muted small mb-0">
                            Buat akun gratis sebagai User (EcoFriend)
                            melalui halaman pendaftaran. Isi nama, email,
                            dan password.
                        </p>
                    </div>
                </div>

                <!-- Step 2: Setor -->
                <div class="col-md-6 col-lg-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <i class="bi bi-truck step-icon"></i>
                        <h5>Setor Sampah</h5>
                        <p class="text-muted small mb-0">
                            Ajukan penyetoran sampah di hari
                            <strong>Rabu &amp; Sabtu</strong> (hari operasional
                            penjemputan Bank Sampah Kabelotapura).
                            Isi jenis sampah, berat, alamat, dan titik
                            lokasi di peta.
                        </p>
                    </div>
                </div>

                <!-- Step 3: Verifikasi -->
                <div class="col-md-6 col-lg-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <i class="bi bi-clipboard-check step-icon"></i>
                        <h5>Verifikasi</h5>
                        <p class="text-muted small mb-0">
                            Admin EcoPalu memverifikasi pengajuan Anda.
                            Pengajuan yang disetujui akan diteruskan ke
                            Bank Sampah untuk dijemput.
                        </p>
                    </div>
                </div>

                <!-- Step 4: Jemput -->
                <div class="col-md-6 col-lg-4">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <i class="bi bi-arrow-down-circle step-icon"></i>
                        <h5>Jemput Sampah</h5>
                        <p class="text-muted small mb-0">
                            Bank Sampah menjemput sampah ke alamat Anda
                            di hari penjemputan (Rabu atau Sabtu).
                            Setelah diterima, mereka konfirmasi via
                            sistem.
                        </p>
                    </div>
                </div>

                <!-- Step 5: Poin -->
                <div class="col-md-6 col-lg-4">
                    <div class="step-card">
                        <div class="step-number">5</div>
                        <i class="bi bi-coin step-icon"></i>
                        <h5>Dapat Poin</h5>
                        <p class="text-muted small mb-0">
                            Setelah sampah diterima, Admin EcoPalu
                            menambahkan poin ke akun Anda. Kumpulkan
                            poin lalu ajukan pencairan saldo ke
                            rekening/e-wallet Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== JENIS SAMPAH YANG DITERIMA (dari DB kategori_sampah) ============== -->
    <section id="jenis-sampah">
        <div class="container">
            <h2 class="section-title text-center">Jenis Sampah yang Diterima</h2>
            <p class="section-subtitle text-center">
                Berikut jenis sampah yang kami terima beserta nilai poin per kilogramnya.
            </p>

            <div class="row g-4">
                <?php
                // Data $kategori dikirim dari Home::index() dengan DISTINCT nama_kategori
                // (lihat Home.php). Tampilan tidak akan duplikat walau DB ada 10 row.
                if (! empty($kategori)):
                    // Mapping nama → icon Bootstrap Icons (UX-friendly)
                    $iconMap = [
                        'botol_plastik' => 'bi-cup-straw',
                        'kardus'        => 'bi-box',
                        'kaleng'        => 'bi-circle',
                        'besi'          => 'bi-tools',
                        'botol_kaca'    => 'bi-cup',
                    ];
                    foreach ($kategori as $k):
                        $namaLower = strtolower($k['nama_kategori']);
                        $icon = $iconMap[$namaLower] ?? 'bi-trash';
                        $namaDisplay = ucwords(str_replace('_', ' ', $k['nama_kategori']));
                ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card-eco p-4 text-center">
                            <i class="bi <?= $icon ?> sampah-icon"></i>
                            <h6 class="mb-0"><?= esc($namaDisplay) ?></h6>
                            <span class="coin-badge"><?= (int) $k['coin_value'] ?> coin/kg</span>
                        </div>
                    </div>
                <?php
                    endforeach;
                else:
                ?>
                    <div class="col-12 text-center text-muted">
                        <em>Belum ada data jenis sampah.</em>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ============== MITRA BANK SAMPAH (statis) ============== -->
    <section id="mitra" style="background: white;">
        <div class="container">
            <h2 class="section-title text-center">Mitra Bank Sampah</h2>
            <p class="section-subtitle text-center">
                Kami bermitra dengan bank sampah resmi untuk memastikan sampah Anda dikelola dengan benar.
            </p>

            <div class="row justify-content-center g-4">
                <div class="col-md-6 col-lg-5">
                    <div class="mitra-card">
                        <i class="bi bi-building mitra-icon"></i>
                        <h4>Bank Sampah Kabelotapura</h4>
                        <p class="text-muted small mb-2">
                            Mitra resmi tunggal EcoPalu untuk wilayah
                            Kota Palu.
                        </p>
                        <ul class="list-unstyled text-start small text-muted mt-3 mb-0">
                            <li><i class="bi bi-geo-alt"></i> Alamat: Jalan Juang II, Kelurahan Tondo, Kecamatan Mantikulore, Kota Palu, Sulawesi Tengah (94148)</li>
                            <li><i class="bi bi-clock"></i> Jam operasional: Setiap hari</li>
                            <li><i class="bi bi-calendar-week"></i> Hari jemput: Rabu &amp; Sabtu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== FOOTER ============== -->
    <footer class="footer-eco">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-5">
                    <h5 style="color: white;">♻ EcoPalu</h5>
                    <p class="small">Bank sampah digital untuk Kota Palu. Dikembangkan untuk lomba BRIDA &amp; Bank Indonesia.</p>
                </div>
                <div class="col-md-3">
                    <h6 style="color: white;">Tautan</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="#cara-kerja">Cara Kerja</a></li>
                        <li><a href="#jenis-sampah">Jenis Sampah</a></li>
                        <li><a href="#mitra">Mitra</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 style="color: white;">Kontak</h6>
                    <ul class="list-unstyled small">
                        <li><i class="bi bi-envelope"></i> dylanyuan12@gmail.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                © 2026 EcoPalu. Dikembangkan untuk lomba BRIDA &amp; Bank Indonesia.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>