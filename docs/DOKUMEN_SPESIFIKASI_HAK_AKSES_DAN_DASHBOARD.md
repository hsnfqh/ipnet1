# DOKUMEN SPESIFIKASI TEKNIS DAN TATA KELOLA HAK AKSES SISTEM
## Enterprise Field & Project Management System (FPMS)
**PT IP Network Solusindo**

---

### DAFTAR ISI DOKUMEN
1. **Ringkasan Eksekutif dan Prinsip Arsitektur Sistem**
2. **Model Tata Kelola Siklus Proyek (Stage-Gate Project Governance)**
3. **Struktur Organisasi dan Pembagian Divisi Teknis**
4. **Matriks Otorisasi dan Hak Akses Global (RBAC Matrix)**
5. **Klasifikasi dan Spesifikasi Lengkap 8 Kategori Dashboard**
6. **Rincian Struktur Menu Bilah Samping (Sidebar) Berdasarkan Peran**
7. **Protokol Serah Terima Dokumen Antar-Divisi (Handover Protocol)**

---

### 1. RINGKASAN EKSEKUTIF DAN PRINSIP ARSITEKTUR SISTEM

Sistem **IP Network Solusindo Field & Project Management System (FPMS)** dirancang sebagai platform terpadu dari hulu ke hilir (*end-to-end*) guna mengelola siklus hidup portofolio proyek teknologi informasi, infrastruktur jaringan telekomunikasi, keamanan siber, dan layanan pemeliharaan berkala (*managed services*).

Sistem menerapkan prinsip **Role-Based Access Control (RBAC)** dan **Multi-Tier Stage-Gate Governance**, yang memastikan bahwa:
1. Setiap pengguna hanya memiliki visibilitas dan wewenang operasional sesuai fungsi jabatannya (*Principle of Least Privilege*).
2. Proyek tidak dapat berpindah ke fase berikutnya tanpa kelengkapan dokumen kepatuhan (*Compliance Documents*) dan persetujuan formal gerbang tahapan (*Gate Approval*).
3. Seluruh metrik operasional (nilai kontrak, *mandays*, kapasitas personil, log waktu, dan kepatuhan SLA) tersinkronisasi secara waktu nyata (*real-time*).

---

### 2. MODEL TATA KELOLA SIKLUS PROYEK (STAGE-GATE GOVERNANCE)

```
+---------------------------+      +---------------------------+      +---------------------------+      +---------------------------+
|     TAHAP 1: ACQUIRE      | ---> |   TAHAP 2: PLAN & DESIGN  | ---> |     TAHAP 3: DELIVER      | ---> |     TAHAP 4: OPERATE      |
|     (Sales & BusDev)      |      | (Solution Architect & Pre)|      |  (PMO & Lead/Field Eng.)  |      | (Maintenance & Helpdesk)  |
+---------------------------+      +---------------------------+      +---------------------------+      +---------------------------+
              |                                  |                                  |                                  |
     [Gate 1: PO & TOR]                 [Gate 2: HLD, SOW, BoQ]           [Gate 3: BAUT & BAST]             [Gate 4: SLA & Renewal]
```

#### Rincian Gerbang Tahapan (Stage-Gate Phases):
1. **Tahap 1: Acquire (Inisiasi & Komersial)**
   * **Penanggung Jawab:** Sales / Business Development Manager (BDM).
   * **Aktivitas:** Identifikasi peluang pasar, kualifikasi dokumen tender (RFP/TOR), negosiasi komersial, hingga penerbitan *Purchase Order* (PO) resmi dari klien.
   * **Keluaran (Deliverables):** Akun Klien Terdaftar, Nilai Kontrak Terkunci, Berkas PO Tervalidasi.
2. **Tahap 2: Plan & Design (Rekayasa Solusi & Sizing)**
   * **Penanggung Jawab:** Solution Architect & Presales Engineer.
   * **Aktivitas:** Kajian kelayakan teknis (*Technical Assessment*), survei lapangan/PoC, perancangan cetak biru topologi (*HLD/LLD*), formulasi rincian material (*BoQ & SKU*), dan penetapan batasan pekerjaan (*SOW*).
   * **Keluaran (Deliverables):** Dokumen Proposal Teknis, Cetak Biru HLD/LLD, Bill of Quantity (BoQ), Alokasi Estimasi *Mandays*.
3. **Tahap 3: Deliver (Eksekusi & Implementasi Lapangan)**
   * **Penanggung Jawab:** Project Management Office (PMO), Lead Engineer, dan Field Engineer.
   * **Aktivitas:** Alokasi divisi teknis, delegasi *Work Order / Task*, instalasi perangkat fisik, penarikan kabel, konfigurasi sistem, pengujian (*UAT*), hingga penerbitan Berita Acara.
   * **Keluaran (Deliverables):** Berita Acara Uji Terima (BAUT), Berita Acara Serah Terima (BAST), Log Timesheet Tervalidasi.
4. **Tahap 4: Operate (Operasional, Pemeliharaan & SLA)**
   * **Penanggung Jawab:** Divisi Maintenance & Helpdesk.
   * **Aktivitas:** Pemeliharaan preventif berkala, monitoring kesehatan perangkat, penanganan tiket insiden SLA 24/7, dan proses peremajaan lisensi/garansi (*Renewal*).
   * **Keluaran (Deliverables):** Laporan Maintenance Berkala, Rekap Tiket SLA, Kontrak Perpanjangan Layanan.

---

### 3. STRUKTUR ORGANISASI DAN PEMBAGIAN DIVISI TEKNIS

| ID | Entitas / Divisi Resmi | Pimpinan / Penanggung Jawab | Cakupan Kompetensi dan Ruang Lingkup Kerja |
| :---: | :--- | :--- | :--- |
| - | **Direksi & Manajemen Eksekutif** | Direktur Utama / HD | Pengawasan strategis, performa finansial portofolio, dan kebijakan tata kelola korporat. |
| - | **Head of Division / Group Leader** | Susanto Djaya | Pengarah teknis lintas divisi dan pengawas kepatuhan eksekusi proyek rekayasa. |
| - | **Project Management Office (PMO)** | Kuntjoro (PMO Manager) | Tata kelola kepatuhan tahapan Stage-Gate, validasi dokumen kontrak, dan audit mandays. |
| - | **Senior Project Manager** | Rizki Toni (Senior PM) | Eksekusi & delivery proyek strategis, manajemen WBS/timeline, dan koordinasi lapangan. |
| 1 | **Divisi Security** | Ignatius (Lead Engineer) | Next-Gen Firewall (NGFW), SIEM Monitoring, WAF, Endpoint Detection & Response (EDR), Audit Kepatuhan PCI-DSS, Vulnerability Assessment & Penetration Testing. |
| 2 | **Divisi Network** | Nugraha (Lead Engineer) | Core/Distribution Switching, Enterprise Routing, SD-WAN Multi-Branch, Backbone Fiber Optic, High-Density WiFi 6, Data Center Structured Cabling. |
| 3 | **Divisi Maintenance & Helpdesk** | Doris (Lead Engineer) | Preventive Maintenance terjadwal, SLA 24/7 Ticketing Support, On-Site Troubleshooting, RMA Replacement, Service Contract Renewals. |
| - | **Tim Komersial & Solusi** | Sales, BDM, Solution Architect | Akuisisi pasar B2B, formulasi proposal tender, sizing BoQ hardware/software, kemitraan prinsipal vendor. |

---

### 4. MATRIKS OTORISASI DAN HAK AKSES GLOBAL (RBAC MATRIX)

> **Keterangan Singkatan Hak Akses:**
> * **FA (Full Access):** Hak penuh mencakup melihat, menambah data, menyunting, menghapus, dan memvalidasi.
> * **MO (Manage / Operate):** Hak mengelola operasional seperti penugasan tiket, pembaruan status, dan persetujuan tim.
> * **RO (Read Only):** Hak melihat dan membaca data sebagai rujukan kajian teknis.
> * **NO (No Access):** Tidak memiliki izin akses terhadap modul terkait.

| Modul / Menu Sistem | Rute Sistem (URL) | Direktur & Group Leader | PMO / Project Manager | Solution Architect / Presales | Sales & BDM | Lead Engineer (Divisi) | Field Engineer |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **Dashboard** | `/dashboard/*` | FA (Eksekutif) | FA (Tata Kelola) | FA (Plan & Design) | FA (Pipeline) | MO (Monitoring Tim) | MO (Tugas Mandiri) |
| **Peluang & Kontrak (Acquire)** | `/acquire` | FA | FA | RO | FA | NO | NO |
| **Peluang Tender** | `/sales-projects` | FA | FA | RO | FA | NO | NO |
| **Desain & SOW (Proposal)** | `/presales/proposals` | FA | FA | FA | RO | RO | NO |
| **Manajemen Project** | `/projects` | FA | FA | RO | RO | MO (Divisi) | NO |
| **Task Management** | `/tasks` | FA | FA | NO | NO | FA (Divisi) | MO (Tugas Mandiri) |
| **Inventory & SKU** | `/inventory` | FA | FA | MO | RO | RO | NO |
| **Vendor Partner** | `/vendors` | FA | FA | MO | FA | RO | NO |
| **Client Database** | `/clients` | FA | FA | RO | FA | RO | NO |
| **Jadwal Kerja & PoC** | `/schedules` | FA | FA | MO (Sesi PoC) | NO | FA (Divisi) | MO (Jadwal Mandiri) |
| **Timesheet & Log Aktivitas**| `/timesheets` | FA | FA | MO (Log Mandiri) | MO (Log Mandiri) | FA (Persetujuan Divisi)| MO (Input Mandiri) |
| **Presensi GPS & Rekap** | `/attendance/*` | FA | FA | MO (Presensi Mandiri) | MO (Presensi Mandiri) | MO (Rekap Divisi) | MO (Presensi Mandiri) |
| **Manajemen Pengguna** | `/users` | FA | FA | NO | NO | RO (Anggota Divisi) | NO |

---

### 5. KLASIFIKASI DAN SPESIFIKASI LENGKAP 8 KATEGORI DASHBOARD

| No | Klasifikasi Dashboard | Alamat Tautan (Route) | Peran Pengguna (Role) | Fungsi dan Ruang Lingkup Utama |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Dashboard Project Management Office (PMO)** | `/pmo/dashboard` | PMO, Project Manager | Pemantauan tata kelola tahapan proyek (Stage-Gate Governance), verifikasi dokumen kepatuhan (PO, SOW, BAUT, BAST), dan alokasi divisi pelaksana. |
| 2 | **Dashboard Solution Architect** | `/dashboard/solution-architect` | Solution Architect | Pengelolaan Tahap 2 (Plan & Design), penyusunan cetak biru arsitektur HLD/LLD, formulasi Bill of Quantity (BoQ), dan estimasi beban kerja (Mandays). |
| 3 | **Dashboard Sales** | `/dashboard/sales` | Sales, Account Executive | Pengelolaan Tahap 1 (Acquire), pemantauan prospek klien, akumulasi nilai kontrak komersial, dan pencapaian target penjualan. |
| 4 | **Dashboard Business Development (BDM)** | `/dashboard/bdm` | Business Development | Kualifikasi tender sektor B2B, ekspansi pasar strategis, dan pemantauan prospek tender baru. |
| 5 | **Dashboard Presales Engineering** | `/dashboard/presales` | Presales Engineer | Evaluasi kesiapan dokumen proposal teknis, antrean sizing ruang lingkup kerja (SOW), dan estimasi kebutuhan perangkat keras. |
| 6 | **Dashboard Lead Engineer** | `/dashboard/lead` | Team Leader / Lead Divisi | Pemantauan kapasitas beban kerja tim (Workload Capacity), delegasi tiket penugasan, dan pengawasan tenggat waktu kritis divisi. |
| 7 | **Dashboard Field Engineer** | `/dashboard/engineer` | Engineer L1, L2, Maintenance | Pelaksanaan tugas operasional harian (Task Saya), pemantauan jadwal kerja pribadi, pelaporan lembar waktu (Timesheet), dan pencatatan presensi GPS. |
| 8 | **Dashboard Direktur & Group Leader** | `/dashboard/lead` & `/pmo/dashboard` | Direktur, Head of Division (GL) | Pengawasan eksekutif terhadap performa finansial portofolio, kapasitas sumber daya lintas divisi, dan audit kepatuhan korporat. |
| 9 | **Dashboard Managed Service & Operate** | `/managed-service` | Lead Maintenance, Maintenance, PMO, Direktur | Pengelolaan Tahap 4 (Operate), monitoring kepatuhan SLA 24/7, antrean tiket insiden P1–P4, inventaris aset CI klien, dan jadwal Preventive Maintenance. |

---

### 6. RINCIAN STRUKTUR MENU BILAH SAMPING (SIDEBAR) BERDASARKAN PERAN

Berikut adalah uraian lengkap struktur navigasi menu bilah samping (*sidebar*) yang tampil pada masing-masing Dashboard peran:

#### 1. Dashboard Project Management Office (PMO)
* **Kategori Pengguna:** PMO dan Project Manager
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan ringkasan portofolio proyek multi-divisi, matriks kelengkapan dokumen serah terima proyek, filter divisi pelaksana, dan antarmuka transisi tahapan *Stage-Gate*.
  2. **Peluang & Kontrak:** Digunakan untuk mengaudit keabsahan berkas *Purchase Order* (PO) dan mencocokkan nilai komersial yang didaftarkan oleh tim Sales sebelum proyek disetujui.
  3. **Project:** Memuat basis data induk seluruh proyek implementasi yang sedang berjalan di perusahaan beserta rincian jadwal, nilai kontrak, dan dokumen rujukan teknis.
  4. **Task:** Menyediakan visibilitas menyeluruh terhadap seluruh tiket pekerjaan lapangan yang sedang dikerjakan oleh teknisi dari seluruh divisi.
  5. **Jadwal:** Menampilkan kalender ketersediaan dan penugasan seluruh personil teknis untuk mencegah terjadinya tumpang tindih jadwal pengerjaan (*schedule clash*).
  6. **Timesheet:** Digunakan untuk mengaudit dan mencocokkan realisasi jam kerja personil terhadap alokasi anggaran hari kerja (*Mandays Budget*) pada setiap proyek.
  7. **Presensi:** Menyajikan rekapitulasi data kehadiran harian dan bulanan seluruh karyawan untuk kebutuhan operasional dan administrasi sumber daya manusia.
  8. **Pengguna:** Digunakan untuk mengelola hak akses akun, pembuatan pengguna baru, penetapan kata sandi, dan penempatan divisi karyawan.

#### 2. Dashboard Solution Architect
* **Kategori Pengguna:** Solution Architect (Aris Sadewo)
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan grafik akumulasi nilai proyek yang sedang dalam tahap perancangan, estimasi beban kerja teknis (*Mandays*), antrean *sizing BoQ*, dan komposisi portofolio domain teknologi.
  2. **Desain & SOW:** Digunakan untuk mengunggah dokumen cetak biru arsitektur (*High Level Design & Low Level Design*), merumuskan batasan ruang lingkup kerja (*Scope of Work*), dan menetapkan alokasi hari kerja teknisi.
  3. **Peluang Tender:** Menampilkan daftar peluang proyek yang didaftarkan oleh tim komersial untuk dilakukan kajian kelayakan teknis (*Technical Feasibility Assessment*).
  4. **Inventory:** Menyediakan katalog spesifikasi teknis perangkat keras, modul jaringan, dan ketersediaan stok fisik untuk formulasi rincian material (*Bill of Quantity*).
  5. **Vendor:** Memuat direktori mitra distributor resmi dan prinsipal teknologi (seperti Cisco, Fortinet, Mikrotik, dan Ruijie) beserta kontak dukungan teknis terkait.
  6. **Client:** Digunakan untuk mempelajari profil perusahaan klien, riwayat infrastruktur teknologi yang terpasang, serta kontak penanggung jawab teknis di pihak klien.
  7. **Jadwal:** Digunakan untuk menyusun jadwal agenda pengujian konsep (*Proof of Concept / PoC*) maupun sesi demonstrasi teknis di laboratorium atau lokasi klien.
  8. **Timesheet:** Digunakan untuk mencatat alokasi jam kerja harian yang digunakan dalam penyusunan dokumen rekayasa solusi arsitektur.

#### 3. Dashboard Sales
* **Kategori Pengguna:** Sales dan Account Executive
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan ringkasan pipa pendapatan (*revenue pipeline*), total pencapaian kontrak berhasil (*deal value*), dan status kesiapan dokumen proposal tender.
  2. **Peluang & Kontrak:** Digunakan untuk mendaftarkan prospek klien baru, memasukkan nilai kesepakatan komersial, mengunggah dokumen *Purchase Order* (PO), dan meneruskan proyek ke tahap perancangan teknis (*Handover to Design*).
  3. **Proposal & SOW:** Digunakan untuk memantau apakah dokumen solusi teknis dari Solution Architect telah rampung dan siap dilampirkan ke dalam dokumen penawaran harga.
  4. **Client:** Digunakan untuk membuat dan memperbarui data profil akun korporasi klien, alamat kantor operasional, dan data kontak penanggung jawab pengadaan.
  5. **Inventory:** Menyediakan informasi rujukan mengenai ketersediaan tipe produk saat melakukan negosiasi awal dengan calon pelanggan.
  6. **Vendor:** Menampilkan daftar prinsipal resmi untuk memastikan dukungan diskon kemitraan dan garansi resmi.
  7. **Timesheet:** Digunakan untuk mencatat log waktu operasional yang dialokasikan dalam kegiatan pertemuan klien (*client meeting*), survei lokasi, dan presentasi penawaran.

#### 4. Dashboard Business Development (BDM)
* **Kategori Pengguna:** Business Development Manager (BDM)
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan indikator kinerja pengembangan pasar B2B, volume peluang tender yang sedang aktif, dan target ekspansi segmen industri baru.
  2. **Peluang Tender:** Digunakan untuk memantau, mengkualifikasi dokumen kerangka acuan kerja (TOR/RFP), dan mengelompokkan tahapan tender berdasarkan statusnya (*Draft, Opportunity, In Progress, Pending*).
  3. **Proposal & SOW:** Memantau ketersediaan proposal teknis yang dibutuhkan sebagai prasyarat administratif dalam partisipasi proses lelang/tender formal.
  4. **Client:** Basis data direktori mitra bisnis B2B dan instansi rekanan.
  5. **Vendor:** Informasi kemitraan strategis dengan prinsipal teknologi global.
  6. **Inventory:** Katalog perangkat teknologi rujukan.
  7. **Timesheet:** Pencatatan log waktu kerja terkait aktivitas riset pasar dan penjajakan kemitraan strategis baru.

#### 5. Dashboard Presales Engineering
* **Kategori Pengguna:** Presales Engineer
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan ringkasan permintaan dokumen proposal yang masuk, daftar antrean estimasi perangkat, dan status penyusunan rancangan teknis awal.
  2. **Proposal & SOW:** Pusat pengunggahan dokumen teknis, pengisian formulasi *Bill of Quantity*, dan penentuan spesifikasi perangkat per proyek tender.
  3. **Peluang Tender:** Daftar proyek komersial yang membutuhkan pendampingan teknis dalam proses penawaran.
  4. **Inventory:** Pengecekan spesifikasi barang di gudang untuk pencocokan kebutuhan penawaran.
  5. **Vendor:** Informasi kontak dukungan prinsipal teknologi.
  6. **Client:** Data profil perusahaan klien yang dituju.
  7. **Jadwal:** Penjadwalan agenda survei teknis lapangan dan sesi demonstrasi produk.
  8. **Timesheet:** Pencatatan jam kerja yang dialokasikan dalam perumusan dokumen proposal teknis.

#### 6. Dashboard Lead Engineer (Team Leader Divisi)
* **Kategori Pengguna:** Lead Engineer Divisi Network, Divisi Security, dan Divisi Maintenance & Helpdesk
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan grafik kapasitas beban kerja anggota tim (*Workload Capacity Bar*), jumlah tiket pekerjaan yang sedang berjalan, dan daftar tenggat waktu (*deadline*) terdekat.
  2. **Project:** Menampilkan rincian proyek implementasi yang telah didelegasikan secara resmi oleh PMO ke divisi bersangkutan beserta dokumen SOW dan HLD rujukan.
  3. **Task:** Digunakan untuk menerbitkan perintah kerja (*Work Order*), menetapkan derajat prioritas (*High, Medium, Low*), menentukan tenggat waktu, dan membagi tugas kepada para teknisi lapangan.
  4. **Jadwal:** Digunakan untuk mengelola penataan jadwal penugasan harian, mingguan, dan bulanan seluruh teknisi di bawah koordinasi divisinya.
  5. **Timesheet:** Menyediakan antarmuka evaluasi dan persetujuan resmi (*Approve/Reject*) terhadap laporan jam kerja harian yang diserahkan oleh para teknisi divisi.
  6. **Presensi:** Menampilkan rekapitulasi status kehadiran kerja (hadir, izin, sakit, dinas luar) para teknisi di divisinya.
  7. **Pengguna:** Menampilkan daftar susunan personil teknisi yang terdaftar di divisinya.

#### 7. Dashboard Field Engineer (Teknisi Lapangan)
* **Kategori Pengguna:** Field Engineer L1, Field Engineer L2, dan Teknisi Maintenance
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan ringkasan tugas pribadi hari ini, status progres pekerjaan lapangan, dan agenda penugasan kerja yang akan datang.
  2. **Task Saya:** Menampilkan rincian instruksi kerja teknis yang didelegasikan oleh Team Leader, pembaruan status pengerjaan (*Pending, In Progress, Completed*), serta pengunggahan catatan teknis lapangan.
  3. **Jadwal:** Menampilkan kalender agenda kerja pribadi dalam mode Harian (*Daily*), Mingguan (*Weekly*), dan Bulanan (*Monthly*).
  4. **Timesheet:** Digunakan untuk menginput rincian waktu mulai dan selesai pengerjaan, uraian aktivitas teknis, kendala lapangan, dan proyek yang bersangkutan pada setiap hari kerja.
  5. **Presensi:** Digunakan untuk melakukan absensi masuk (*Check-in*) dan absensi keluar (*Check-out*) dengan verifikasi koordinat lokasi GPS resmi kantor (radius 500 meter) serta sinkronisasi waktu nyata (WIB).

#### 8. Dashboard Direktur & Group Leader (Manajemen Eksekutif)
* **Kategori Pengguna:** Direktur Utama dan Head of Division / Group Leader
* **Rincian Menu Navigasi:**
  1. **Dashboard:** Menampilkan ikhtisar eksekutif terkait kapasitas seluruh divisi teknis, kinerja operasional personil, dan matriks pemantauan proyek tingkat tinggi.
  2. **Dashboard PMO:** Menyediakan akses pemantauan penuh terhadap status tata kelola tahapan *Stage-Gate* seluruh proyek yang aktif di korporasi.
  3. **Peluang & Kontrak:** Digunakan untuk memantau volume dan total nilai perolehan kontrak yang berhasil dibukukan oleh tim komersial.
  4. **Project:** Basis data master seluruh proyek implementasi di seluruh divisi.
  5. **Task:** Pemantauan terhadap seluruh aktivitas penugasan kerja operasional teknis secara komprehensif.
  6. **Jadwal:** Kalender master ketersediaan dan agenda penugasan seluruh personil perusahaan.
  7. **Timesheet:** Audit komprehensif terhadap tingkat produktivitas dan jam kerja seluruh karyawan.
  8. **Presensi:** Rekapitulasi absensi karyawan perusahaan secara global.
  9. **Pengguna:** Pengawasan terhadap master data akun, struktur organisasi, dan hak otorisasi sistem.

---

### 7. PROTOKOL SERAH TERIMA DOKUMEN ANTAR-DIVISI (HANDOVER PROTOCOL)

Guna menjamin kepatuhan tata kelola proyek dan standar mutu (*Quality Assurance*), sistem mewajibkan pemenuhan 4 dokumen utama pada setiap gerbang serah terima:

```
[Sales / BDM]                [Solution Architect]                [PMO & Engineering]              [Maintenance & Helpdesk]
      |                               |                                   |                                   |
      +---- Dokumen PO Klien -------->|                                   |                                   |
      |                               +---- HLD/LLD, SOW & BoQ ---------->|                                   |
      |                               |                                   +---- BAUT, BAST & Konfigurasi ---->|
      |                               |                                   |                                   +---- SLA Report & RMA
```

1. **Dokumen Purchase Order (PO):**
   Diterbitkan oleh Klien dan diunggah oleh Sales pada Modul *Acquire*. Menjadi prasyarat mutlak sebelum Solution Architect mengunci estimasi BoQ.
2. **Dokumen Desain HLD/LLD dan SOW:**
   Disusun oleh Solution Architect dan diunggah pada Modul *Proposal & SOW*. Menjadi prasyarat mutlak sebelum PMO menerbitkan persetujuan gerbang tahapan (*Gate Approval*) ke divisi eksekusi.
3. **Berita Acara Uji Terima (BAUT) dan Serah Terima (BAST):**
   Ditandatangani bersama Klien setelah pengujian (*UAT*) berhasil dan diunggah pada Modul PMO. Menjadi dasar penutupan tahap *Deliver* dan penerbitan faktur pembayaran.
4. **Laporan Pemeliharaan dan Tiket SLA:**
   Dikelola oleh Divisi Maintenance & Helpdesk sebagai dasar pelaporan evaluasi kualitas layanan dan perpanjangan kontrak pemeliharaan tahunan (*Service Contract Renewal*).
