
# Cucianku

Aplikasi Laundry Cucianku ini membantu pengelolaan operasional laundry, mulai dari manajemen order, pengaturan produk, hingga ringkasan pendapatan.

## Fitur Utama

-   **Orders**: Kelola dan lacak pesanan pelanggan.
-   **Customers**: Tambahkan, edit, dan hapus data pelanggan.
-   **Products**: Tambahkan, edit, dan hapus layanan laundry.
-   **Ringkasan Pendapatan**: Tampilkan laporan income yang bisa disesuaikan dengan kebutuhan.

## Teknologi yang Digunakan

-   **Laravel**: Framework PHP yang digunakan untuk membangun backend.
-   **Filament**: Digunakan untuk membangun form dan action dalam aplikasi.
-   **MySQL**: Database untuk menyimpan data.
-   **WhatsApp API**: Mengirimkan notifikasi pesanan secara otomatis (belum di implementasikan).

## Persyaratan

-   **PHP** >= 7.4
-   **Composer**
-   **MySQL**

## Instalasi

1. Clone repositori
	> git clone https://github.com/anoerman/cucianku.git
cd cucianku


2. Install dependensi
	> composer install

3. Salin file `.env` dan sesuaikan
	> cp .env.example .env


4. Generate key aplikasi
	

	> php artisan key:generate


5. Setup database dan migrasi
	

	> php artisan migrate


6. Jalankan server lokal
	

	> php artisan serve


## Kontribusi

Jika Anda ingin berkontribusi, silakan buat pull request dan sertakan penjelasan fitur atau perbaikan yang diusulkan.

## Lisensi

Aplikasi ini dirilis di bawah lisensi MIT.
