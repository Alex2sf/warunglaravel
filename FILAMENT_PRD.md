# Product Requirements Document (PRD) - Warung Laravel Filament Admin

## 1. Overview
Warung Laravel Admin Panel adalah sistem manajemen POS, Inventaris, Kasir, dan Akuntansi Kas Toko/Warung berbasis Laravel Filament 4.

## 2. Authentication & Core Access
- **Login Route**: `/admin/login`
- **Dashboard Route**: `/admin`
- **Admin Credentials**: `admin@admin.com` / `password`
- **Role & Permissions**: Super Admin, Admin, Kasir, Kurir via Spatie Permission & Filament Shield.

## 3. Financial & Cash Management Modules
### 3.1 Buka & Tutup Shift Kasir (`/admin/cashier-shifts`)
- Kasir membuka shift dengan input Saldo Awal Laci (`starting_cash`).
- Status shift: `OPEN` atau `CLOSED`.
- Kasir menutup shift dengan input Uang Fisik Aktual (`actual_cash`).
- Sistem menghitung `expected_cash` dan `difference` (selisih fisik vs sistem).

### 3.2 Belanja Kasir / Pembelian Toko (`/admin/cashier-purchases`)
- Pencatatan pengeluaran langsung dari kas laci kasir.
- Kategori:
  - `STOCK`: Pembelian stok barang dagangan (terkait produk).
  - `OPERATIONAL`: Biaya operasional warung (listrik, kantong plastik, konsumsi, dll).
- Menyimpan nominal `amount`, deskripsi, dan upload foto bukti nota `proof_image`.

### 3.3 Transaksi Kas Masuk & Keluar (`/admin/cash-transactions`)
- Pencatatan transaksi kas non-penjualan.
- Tipe transaksi:
  - `DEPOSIT`: Kas masuk / setoran modal.
  - `EXPENSE`: Kas keluar / penarikan kas.

### 3.4 Saldo Kas Toko (`/admin/cash-balances`)
- Memantau akumulasi total kas warung yang tersimpan.

### 3.5 Jurnal Umum Realtime (`/admin/general-journal`)
- View gabungan (UNION) dari:
  1. Penjualan pesanan (`orders` status COMPLETE)
  2. Belanja kasir (`cashier_purchases`)
  3. Transaksi kas toko (`cash_transactions`)
- Kolom: Tanggal, Ref Number, Kategori, Metode Pembayaran, Pemasukan (Debit), Pengeluaran (Kredit), Deskripsi.

### 3.6 Laporan Pembayaran Harian (`/admin/daily-payment-summary`)
- Agregasi harian berdasarkan metode pembayaran:
  - Tunai (`tunai_total`)
  - QRIS (`qris_total`)
  - Transfer Bank (`transfer_total`)
  - Piutang / Corporate (`corporate_total`)
- Total Pendapatan Selesai (`grand_total`) vs Pendapatan Pending/Dalam Pengiriman (`pending_total`).

## 4. Product & Inventory Modules
### 4.1 Tipe Produk (`/admin/product-types`)
- Kategori tipe produk:
  - `REFILL`: Isi ulang / tukar galon kosong.
  - `NEW_UNIT`: Unit baru lengkap.
  - `CONSUMABLE`: Barang habis pakai / sembako / retail.

### 4.2 Manajemen Produk (`/admin/products`)
- Form CRUD produk: nama, kode barang, tipe produk, harga modal, harga jual, status aktif.
- Tabel dengan filter tipe produk, pencarian barcode/nama.

### 4.3 Stok & Mutasi Inventaris (`/admin/inventories`, `/admin/inventory-movements`)
- Pencatatan stok sistem vs real quantity.
- Log pergerakan stok: Masuk, Keluar, Koreksi/Penyesuaian.

## 5. Sales & Orders Modules
### 5.1 Pesanan Toko & Pengantaran (`/admin/orders`)
- Siklus status pesanan: `READY` -> `ON_DELIVERY` -> `COMPLETE` (atau `CANCELLED`).
- Metode pembayaran: `TUNAI`, `QRIS`, `TRANSFER`, `CORPORATE`.
- Alamat pengantaran dan link Google Maps (`address_link`).
- Bukti pengantaran kurir (`order_delivery_proofs`).

### 5.2 Detail Produk Pesanan (`/admin/order-products`)
- Rincian item per transaksi: jumlah, harga satuan, subtotal.

### 5.3 Pelanggan (`/admin/customers`)
- Data pelanggan: Nama, telepon/WhatsApp, alamat lengkap, link maps.

## 6. User Management (`/admin/users`)
- Manajemen akun staf warung: Admin, Kasir, Kurir, Owner.
- Pengaturan role & permission per user.
