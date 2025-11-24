![1](https://github.com/user-attachments/assets/8efddd28-35c2-4aaa-b661-a76cc8319e24)# ⚡ PLN Trafo Monitoring System

Sistem Informasi berbasis Web untuk memantau, mengelola, dan menganalisis beban Gardu Distribusi PLN secara *real-time* dan akurat.

![Dashboard Preview](assets/img/preview_dashboard.png)
> *Catatan: Ganti `assets/img/preview_dashboard.png` dengan screenshot aplikasi asli Anda.*

## 📖 Overview (Gambaran Umum)

**PLN Trafo Monitoring** adalah aplikasi web yang dikembangkan untuk mempermudah petugas ULP (Unit Layanan Pelanggan) dalam melakukan pendataan dan pengukuran kinerja trafo distribusi. Aplikasi ini menggantikan metode pencatatan manual dengan sistem digital yang terintegrasi.

Masalah utama yang diselesaikan oleh aplikasi ini adalah perhitungan beban manuver dan penentuan status trafo (Overload/Underload/Normal). Dengan fitur **Kalkulasi Otomatis**, petugas cukup memasukkan nilai arus (Ampere) dan tegangan (Volt), sistem akan langsung menghitung persentase pembebanan dan memberikan indikator status peringatan dini.

### 🌟 Mengapa Project Ini Dibuat?
* **Efisiensi:** Mempercepat proses input data lapangan.
* **Akurasi:** Menghindari human-error dalam perhitungan rumus beban trafo.
* **Visualisasi:** Memudahkan pemetaan lokasi gardu melalui fitur GIS (Peta Sebaran).
* **Pelaporan:** Mempermudah rekapitulasi data harian dengan fitur Export Excel.

---

## 🚀 Fitur Utama

1.  **Dashboard Interaktif & Modern:**
    * Desain UI Glassmorphism yang elegan.
    * Mendukung **Dark Mode** & **Light Mode** (Auto-save preference).
    * Statistik ringkas (Total Gardu, Kondisi Aman, Warning, Overload).

2.  **Manajemen Data Gardu (CRUD):**
    * Database master gardu lengkap (Alamat, Daya, Merk, No Seri).
    * Integrasi titik koordinat GPS.

3.  **Smart Input Pengukuran:**
    * **Rumus Otomatis:** Menghitung % Beban LWBP & WBP secara *real-time* saat mengetik.
    * **Auto-Fill:** Mengambil data pengukuran terakhir secara otomatis untuk mempercepat update data.
    * **Validasi Cerdas:** Kolom jurusan dan grounding otomatis terkunci/terbuka sesuai inputan.

4.  **Peta Sebaran (GIS):**
    * Visualisasi lokasi gardu menggunakan **Leaflet.js** (OpenStreetMap).
    * Pin berwarna sesuai status beban terakhir (Merah = Overload, Hijau = Aman).

5.  **Sistem Keamanan Bertingkat:**
    * **Guest Mode:** Akses hanya lihat (View Only) menggunakan kode akses.
    * **Admin Mode:** Akses penuh (Edit/Hapus/Input) menggunakan login terenkripsi.

6.  **Export Laporan:**
    * Unduh rekapitulasi data lengkap ke format **.XLS (Excel)** dengan satu klik.

---

## 🛠️ Teknologi yang Digunakan

* **Backend:** PHP Native (Procedural Style) - Ringan dan Cepat.
* **Frontend:** HTML5, CSS3, Bootstrap 5.
* **Scripting:** JavaScript (jQuery, AJAX).
* **Database:** MySQL / MariaDB.
* **Maps API:** Leaflet.js (Gratis & Open Source).
* **Icons:** FontAwesome 6.

---

## 📦 Cara Instalasi (Localhost)

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/username-anda/pln-trafo-monitoring.git](https://github.com/username-anda/pln-trafo-monitoring.git)
    ```
2.  **Setup Database**
    * Buka phpMyAdmin.
    * Buat database baru dengan nama `db_trafo`.
    * Import file `database/db_trafo.sql` (Pastikan Anda sudah export database Anda ke folder project).
3.  **Konfigurasi Koneksi**
    * Buka file `koneksi.php`.
    * Sesuaikan `$host`, `$user`, `$pass`, dan `$db` dengan settingan XAMPP/Laragon Anda.
4.  **Jalankan Aplikasi**
    * Buka browser dan akses: `http://localhost/pln-trafo-monitoring`

---

## 🔐 Akun Demo

Untuk pengujian, gunakan kredensial berikut:

| Akses                   | Username / Kode | Password |
| :---------------------- | :-------------- | :---     |
| **Guest (Login Awal)**  | `pln2025`       | -        |
| **Admin (Petugas)**     | `tjbalai`       | `12345`  |

---

## 📸 Galeri

*(Silakan tambahkan screenshot aplikasi di sini)*
* *Dashboard Dark Mode*
* *Input Form Pengukuran*
* *Peta Sebaran*

---

**Dikembangkan oleh [Nama Anda]**
*Web Developer & Database Enthusiast*
