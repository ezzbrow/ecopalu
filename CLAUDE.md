# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Konteks Bisnis & Lomba

Project ini dikembangkan untuk mengikuti lomba **BRIDA (Badan Riset dan Inovasi Daerah) dan Bank Indonesia**. EcoPalu adalah **bank sampah digital** yang mendigitalkan proses bank sampah di Kota Palu (sebelumnya manual dalam input data dan transaksi).

**PENTING — koreksi terbaru**: Sistem **tidak menggunakan QRIS generate otomatis**. Pencairan poin memakai sistem manual: user ajukan pencairan nominal tertentu (dipotong dari koin) → admin EcoPalu yang mentransfer secara manual ke rekening/e-wallet user → status diverifikasi bertahap. Semua rencana fitur QRIS generator dari versi sebelumnya **dibatalkan**, diganti dengan alur ini.

---

## Alur Sistem Lengkap (Sumber Kebenaran)

1. Landing page → User/Bank Sampah daftar → login → masuk ke dashboard masing-masing
2. **User** mengajukan penyetoran sampah (hanya bisa di luar hari penjemputan, lihat aturan jadwal di bawah)
3. **Admin EcoPalu** menerima notifikasi pengajuan, menyeleksi (verifikasi terima/tolak sampah)
4. Setelah diverifikasi diterima, **Admin Bank Sampah** menerima notifikasi sampah yang harus dijemput, cek alamat & lokasi peta
5. Setelah **semua** sampah pada jadwal itu selesai dijemput fisik, **Admin Bank Sampah** menekan tombol konfirmasi "selesai" → notifikasi terkirim ke Admin EcoPalu
6. **Admin EcoPalu** memberikan poin ke user yang sampahnya sudah selesai diterima (atau menolak/membatalkan pemberian poin dengan alasan, jika ternyata gagal dalam proses penjemputan)
7. User menerima poin, tersimpan sebagai saldo koin
8. Setelah nominal cukup, user mengajukan pencairan sesuai ketentuan minimal
9. Admin EcoPalu memverifikasi & mentransfer dana secara manual
10. User menerima notifikasi pencairan berhasil

Ini adalah alur acuan utama — kalau ada bagian spesifikasi halaman di bawah yang terasa bertentangan dengan alur ini, **alur ini yang benar**.

---

## Tiga Role Pengguna

| Role | Fungsi Utama |
|---|---|
| **Admin EcoPalu** | Verifikasi pengajuan sampah, beri/tolak poin (tergantung notif selesai dari Bank Sampah), kelola & transfer manual pencairan reward, kelola pengguna |
| **Bank Sampah** (saat ini hanya 1 mitra: Bank Sampah Kabelotapura, 1 unit penjemput) | Terima notifikasi sampah yang perlu dijemput, cek lokasi, konfirmasi selesai jemput ke Admin EcoPalu |
| **User ("EcoFriend")** | Ajukan penyetoran, pantau status, kumpulkan poin, ajukan pencairan |

---

## Spesifikasi Halaman (Sudah Dikoreksi)

### Publik
Landing, Login, Register — tidak ada perubahan dari versi sebelumnya (toggle Masuk/Daftar Sebagai User/Bank Sampah).

### Dashboard User

- **Beranda**:
  - Total poin
  - **"Riwayat Penukaran"** (bukan "Setoran Berjalan") — menampilkan total akumulasi koin dan rupiah yang sudah didapat user selama ini. Desain logika query/agregasinya sendiri (join dari data transaksi poin & pencairan) supaya masuk akal dan akurat.
  - **Bukan progress bar** untuk minimal penjemputan — ganti jadi notifikasi/info teks biasa: "Minimal penjemputan 50kg, setara ± 1.600–1.700 botol plastik 1,5L"
  - Jadwal penjemputan, jenis sampah & poin per kg (tetap)

- **Ajukan Penjemputan**:
  - Form tambah field: **alamat** (input teks) dan **pemilihan titik lokasi di peta** (map picker, misal Leaflet dengan marker draggable)
  - **Aturan tanggal_jemput**: Rabu & Sabtu adalah hari jemput fisik Bank Sampah. User WAJIB memilih tanggal_jemput yang jatuh di hari Rabu atau Sabtu saat mengajukan penjemputan (divalidasi server-side via `isHariPenjemputan()` di `PenjemputanController::store()` dan client-side via JS onchange yang disable tombol submit + tampilkan pesan error kalau tanggal di luar Rabu/Sabtu). Admin EcoPalu, saat mengedit data penjemputan existing, BOLEH mengubah tanggal_jemput ke hari apapun untuk keperluan koreksi data — tidak ada pembatasan hari untuk role admin (validasi tanggal_jemput TIDAK dipasang di `PenjemputanController::update()`).

- **Status Pengajuan**: tetap tab Semua/Menunggu/Disetujui/Ditolak/Selesai

- **Notifikasi**:
  - Perilaku read/unread harus sesuai standar notifikasi pada umumnya: notifikasi **baru muncul sebagai "belum dibaca"** (ada indikator visual, misal dot/bold), dan **baru ditandai "sudah dibaca" ketika user benar-benar klik/buka notifikasi itu** — bukan otomatis semua ke-mark-read saat buka dropdown.

- **Reward & Poin** (revisi total, tidak ada QRIS):
  - User pilih nominal pencairan (potongan dari koin sesuai ketentuan minimal & kelipatan)
  - Submit pengajuan pencairan → sistem tampilkan notifikasi "berhasil mengajukan, menunggu verifikasi sukses pembayaran"
  - Update status pencairan muncul di notifikasi user (menunggu → diproses → berhasil, ditransfer manual oleh Admin EcoPalu)

- **Ubah Password**: tambahkan link/tombol **"Lupa Password?"** supaya user yang lupa password lama tetap bisa reset (lewat email/OTP — tentukan mekanisme reset yang paling sesuai dengan constraint project, tanya user kalau butuh keputusan soal ini)

- **Edukasi** (menu baru): tambahkan item navbar "Edukasi" berisi konten edukasi **nyata dan bermanfaat** — panduan memilah sampah, cara membersihkan sampah sebelum disetor, dampak lingkungan, tips memperbanyak poin, dll. Ini bagian penting untuk mendukung pemahaman masyarakat, bukan sekadar halaman kosong/placeholder.

- **Kalender**: tambahkan tampilan kalender di dashboard user yang menandai visual hari operasional (Rabu/Sabtu = hari penjemputan) berbeda dari hari biasa, selaras dengan aturan pengajuan di atas.

### Dashboard Bank Sampah

- **Logika status diperbaiki**: hanya dua status **Menunggu** dan **Selesai** (bukan status lain). Tambahkan tampilan/tombol dimana ketika **semua sampah pada jadwal itu sudah sampai secara fisik di bank sampah**, Admin Bank Sampah **menyetujui/konfirmasi selesai** → notifikasi otomatis terkirim ke Admin EcoPalu, yang lanjut memproses pemberian poin ke user.

- **Notifikasi**: **hapus** notifikasi "poin berhasil dikirim" dari sisi Bank Sampah (itu ranah Admin EcoPalu, bukan Bank Sampah). Notifikasi yang relevan untuk Bank Sampah hanya:
  - Ada permintaan penjemputan baru
  - Respon/update dari Admin EcoPalu
  - Notifikasi ganti password
  - Notifikasi lain yang selaras langsung dengan fungsi Bank Sampah

- **Kalender**: tambahkan juga di dashboard Bank Sampah.

### Dashboard Admin EcoPalu

- Card **"Disetujui Hari Ini"** → ganti jadi **"Disetujui"** saja (hilangkan filter "hari ini", tampilkan total keseluruhan — atau klarifikasi ke user kalau maksudnya beda)
- **Grafik Poin** (7 hari terakhir) → ganti jadi **Grafik Reward Dicairkan** (menampilkan tren nominal/jumlah pencairan reward, bukan poin diberikan)
- **Kalender**: tambahkan juga di dashboard Admin EcoPalu
- **Status warna berbeda**: tambahkan badge/warna status khusus untuk user yang **"menunggu pemberian poin"** (kondisi: sampah sudah diverifikasi diterima Admin EcoPalu, dan sudah dapat notifikasi selesai dari Bank Sampah, tapi poin belum di-final-kan/dicairkan ke akun user) — ini harus **berbeda visual** dari status "menunggu verifikasi sampah diterima/ditolak" supaya Admin EcoPalu bisa membedakan dengan jelas di tabel/list permintaan.
- **Popup konfirmasi**: ketika Admin klik "Setujui" pada permintaan penjemputan sampah, tampilkan **pop-up berhasil** + informasi bahwa penjemputan sudah dikirim/diteruskan ke Admin Bank Sampah.
- **Bagian Penjemputan** disederhanakan:
  - Hanya ada 1 bank sampah: **Bank Sampah Kabelotapura**
  - Hanya 1 unit penjemput
  - Tabel **"Daftar Penjemputan"**: hapus kolom **Petugas**
  - **Hapus tab "Jadwal Mendatang"** sepenuhnya
  - Tab **"Riwayat"**: isinya penjemputan yang sudah selesai dijemput, berdasarkan notifikasi konfirmasi dari Admin Bank Sampah (bukan asumsi tanggal)

---

## Cara Kerja yang Diharapkan

- Kerjakan bertahap per role/modul, jangan sekaligus semua
- Setiap selesai satu modul: tampilkan ringkasan perubahan, **tunggu konfirmasi user sebelum lanjut ke modul berikutnya**
- Kalau menemukan logika bisnis yang ambigu (termasuk poin yang masih ditandai perlu klarifikasi di bawah), tanya dulu ke user — jangan asumsi sepihak
- Ikuti gaya visual mockup: warna hijau khas EcoPalu, ikon recycle, Bootstrap Icons, card dengan border radius halus
- Semua teks UI tetap Bahasa Indonesia

---

## Requirements & Commands
- PHP 8.2+, Composer, MySQL/MariaDB
- `composer install`
- `php spark serve`
- `composer test` / `vendor/bin/phpunit --filter <nama>`
- `php spark migrate` (juga `migrate:status`, `migrate:rollback`, `migrate:refresh`)
- `php spark routes`
- `php spark make:*` untuk generator (controller, model, migration, dst)

## Arsitektur
Struktur MVC standar CodeIgniter 4:
- `public/` — web root
- `app/Controllers` — controller CRUD yang tipis (thin controllers)
- `app/Models` — pakai soft-deletes + timestamps
- `app/Views` — pakai `extend()`/`section()`/`renderSection()` di atas layout `layouts/main` (header/sidebar/footer), styling Bootstrap 5 + Bootstrap Icons dari CDN
- `app/Database/Migrations` — migration files

## Konvensi
- UI string berbahasa Indonesia
- Pola wiring CRUD seragam di semua modul
- Soft-delete saja, tidak ada hard delete
- Password di-hash saat proses store
- Bootstrap 5 + custom CSS di `public/assets/css/style.css`

## Known Issues (perlu diperbaiki)
- `TransaksiCoinController` dan `PenjemputanController`: field yang di-post dari form **tidak terdaftar** di `$allowedFields` model masing-masing → data diam-diam terbuang (silent drop) tanpa error. Perlu diperbaiki sebelum lanjut fitur baru.
- Ada sisa kode `TransactionModel` + tabel `transactions`/`transaction_items` yang kelihatannya tidak terpakai (kemungkinan sisa iterasi awal sebelum pindah ke `transaksi_coin`) — perlu diverifikasi apakah aman dihapus atau masih dipakai di tempat lain.

## Poin yang Masih Perlu Klarifikasi ke User
- Mekanisme reset password lewat "Lupa Password" (email link, OTP, atau lainnya) — sesuaikan dengan infrastruktur yang tersedia (apakah ada SMTP/email service yang sudah dikonfigurasi di project ini?)
- Definisi tepat "Disetujui" di dashboard Admin (total keseluruhan atau ada filter periode tertentu)
- Aturan minimal & kelipatan nominal pencairan reward (berapa minimal poin/rupiah per pengajuan pencairan)