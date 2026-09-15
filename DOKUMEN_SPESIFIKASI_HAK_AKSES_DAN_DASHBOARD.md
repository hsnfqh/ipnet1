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

### 3. STRUKTUR ORGANISASI DAN PEMBAGIAN PERAN RESMI

```
                              +-----------------------+
                              |        DIRECTOR       |
                              +-----------+-----------+
                                          |
                              +-----------+-----------+
                              |     DIVISION HEAD     |
                              +-----------+-----------+
                                          |
                +-------------------------+-------------------------+
                |                                                   |
+---------------+---------------+                   +---------------+---------------+
|     Commercial & Solution     |                   |      Delivery & Operation     |
|         (Group Leader)        |                   |         (Group Leader)        |
+---------------+---------------+                   +---------------+---------------+
| - SALES (Account Manager)     |                   | - Technical Engineering:      |
| - BUSDEV (Business Dev)       |                   |    * NETWORK Engineering      |
| - CRO (Customer Relation)     |                   |    * SECURITY Engineering     |
| - Pre-Sales                   |                   | - Managed Service             |
| - Solution Architect (Expert) |                   |    * Field Support (EOS)      |
| - Tech.Develop (R&D)          |                   | - PMO (Project Manager)       |
+-------------------------------+                   +-------------------------------+
                                                    | * Admin (Support Function)    |
                                                    +-------------------------------+
```

| ID | Cabang / Entitas | Posisi / Peran Resmi | Penanggung Jawab Demo | Cakupan Kompetensi dan Ruang Lingkup |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Eksekutif** | Director | Hariyadi | Kepemimpinan strategis korporasi, performa finansial, dan audit portofolio global. |
| 2 | **Divisi Induk** | Division Head | Susanto Djaya | Pengarah operasional terpadu lintas cabang (Commercial & Solution, Delivery & Operation). |
| 3 | **Commercial & Solution** | Group Leader Commercial | Farhan Ramadhan | Koordinasi strategi akuisisi pasar, rekayasa solusi, dan kemitraan prinsipal vendor. |
| 4 | **Commercial & Solution** | BusDev (Business Dev) | Erie | Identifikasi peluang pasar B2B, kualifikasi lelang tender, dan kemitraan strategis. |
| 5 | **Commercial & Solution** | Sales (Account Manager) | Raiza, Ribka, Widodo | Hubungan klien, kualifikasi kebutuhan, negosiasi, dan penerbitan kontrak/PO. |
| 6 | **Commercial & Solution** | CRO (Customer Relation) | Taufik Hidayat | Keterikatan pelanggan pasca implementasi (*customer retention & satisfaction*). |
| 7 | **Commercial & Solution** | Pre-Sales Specialist | Akbar | Penerjemahan kebutuhan teknis, survei lokasi/PoC, dan proposal teknis penawaran. |
| 8 | **Commercial & Solution** | Solution Architect (Expert)| Aris Sadewo | Desain arsitektur (HLD/LLD), formulasi BoQ & SKU, dan kajian kelayakan teknologi. |
| 9 | **Commercial & Solution** | Tech.Develop (R&D) | Bagas Pratama | Riset teknologi baru, integrasi sistem cerdas, dan standardisasi arsitektur. |
| 10 | **Delivery & Operation** | Group Leader Delivery | Susanto Djaya | Pengawasan tata kelola eksekusi rekayasa proyek dan SLA operasional lapangan. |
| 11 | **Delivery & Operation** | PMO (Project Manager) | Kuncoro, Rizki | Tata kelola Stage-Gate, persetujuan gerbang tahapan, validasi BAUT/BAST & mandays. |
| 12 | **Delivery & Operation** | Team Leader Engineering | Nugraha (Net), Ignatius (Sec)| Koordinasi alokasi beban kerja tim teknis dan review kualitas konfigurasi lapangan. |
| 13 | **Delivery & Operation** | Network Engineer | Rorik, Shiamsyah, Dedy, Syaiful| Implementasi routing, switching, SD-WAN, firewall, fiber optic, dan pengujian. |
| 14 | **Delivery & Operation** | Security Engineer | Eka Kurnia | Hardening perangkat, audit keamanan siber, mitigasi kerentanan, dan konfigurasi SIEM. |
| 15 | **Delivery & Operation** | Managed Service Coordinator| Doris | Koordinasi pemeliharaan berkala, helpdesk tiket insiden 24/7, dan kepatuhan SLA. |
| 16 | **Delivery & Operation** | Field Support (EOS) | Mario, Eris | Eksekusi langsung di lapangan, penggantian suku cadang (RMA), dan bukti kegiatan (Evidence). |

---

### 4. MATRIKS OTORISASI DAN HAK AKSES GLOBAL (RBAC MATRIX)

> **Keterangan Singkatan Hak Akses:**
> * **FA (Full Access):** Hak penuh mencakup melihat, menambah data, menyunting, menghapus, dan memvalidasi.
> * **MO (Manage / Operate):** Hak mengelola operasional seperti penugasan tiket, pembaruan status, dan persetujuan tim.
> * **RO (Read Only):** Hak melihat dan membaca data sebagai rujukan kajian teknis.
> * **NO (No Access):** Tidak memiliki izin akses terhadap modul terkait.

| Modul / Menu Sistem | Rute Sistem (URL) | Director & Division Head | PMO / Project Manager | Solution Architect / Presales / R&D | Sales / BusDev / CRO | Team Leader & Managed Service | Field / Network / Sec Engineer |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **Dashboard** | `/dashboard/*` | FA (Eksekutif) | FA (Tata Kelola) | FA (Plan & Design) | FA (Pipeline) | MO (Monitoring Tim) | MO (Tugas Mandiri) |
| **Peluang & Kontrak (Acquire)** | `/acquire` | FA | FA | RO | FA | NO | NO |
| **Peluang Tender** | `/sales-projects` | FA | FA | RO | FA | NO | NO |
| **Desain & SOW (Proposal)** | `/presales/proposals` | FA | FA | FA | RO | RO | NO |
| **Manajemen Project** | `/projects` | FA | FA | RO | RO | MO (Divisi) | NO |
| **Task Management** | `/tasks` | FA | RO (Monitoring Delivery) | NO | NO | FA (Divisi / Lead) | MO (Tugas Mandiri) |
| **Inventory & SKU** | `/inventory` | FA | FA | MO | RO | RO | NO |
| **Vendor Partner** | `/vendors` | FA | FA | MO | FA | RO | NO |
| **Client Database** | `/clients` | FA | FA | RO | FA | RO | NO |
| **Jadwal Kerja & PoC** | `/schedules` | FA | FA | MO (Sesi PoC) | NO | FA (Divisi) | MO (Jadwal Mandiri) |
| **Timesheet & Log Aktivitas**| `/timesheets` | FA | FA | MO (Log Mandiri) | MO (Log Mandiri) | FA (Persetujuan Divisi)| MO (Input Mandiri) |
| **Presensi GPS & Rekap** | `/attendance/*` | FA | FA | MO (Presensi Mandiri) | MO (Presensi Mandiri) | MO (Rekap Divisi) | MO (Presensi Mandiri) |
| **Manajemen Pengguna** | `/users` | FA | FA | NO | NO | RO (Anggota Tim) | NO |

---

### 5. KLASIFIKASI 7 KATEGORI DASHBOARD UTAMA

| No | Klasifikasi Dashboard | Alamat Tautan (Route) | Peran Pengguna Utama (Roles) | Fungsi dan Ruang Lingkup Utama |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Dashboard Project Management Office (PMO)** | `/pmo/dashboard` | PMO, Project Manager, Director, Division Head | Pemantauan tata kelola tahapan proyek (Stage-Gate Governance), verifikasi dokumen kepatuhan (PO, SOW, BAUT, BAST), dan alokasi divisi pelaksana. |
| 2 | **Dashboard Solution Architect & R&D** | `/dashboard/solution-architect` | Solution Architect, Tech Develop (R&D), GL Commercial | Pengelolaan Tahap 2 (Plan & Design), penyusunan cetak biru arsitektur HLD/LLD, formulasi Bill of Quantity (BoQ), dan estimasi beban kerja (Mandays). |
| 3 | **Dashboard Sales & Account Manager** | `/dashboard/sales` | Sales (AM), CRO, GL Commercial, Director | Pengelolaan Tahap 1 (Acquire), pemantauan prospek klien, akumulasi nilai kontrak komersial, dan pencapaian target penjualan. |
| 4 | **Dashboard Business Development (BD)** | `/dashboard/bdm` | BusDev (BD), BDM, GL Commercial | Kualifikasi tender sektor B2B, ekspansi pasar strategis, dan pemantauan prospek tender baru. |
| 5 | **Dashboard Presales Engineering** | `/dashboard/presales` | Pre-Sales Specialist, Solution Architect | Evaluasi kesiapan dokumen proposal teknis, antrean sizing ruang lingkup kerja (SOW), dan estimasi kebutuhan perangkat keras. |
| 6 | **Dashboard Executive & Team Leader** | `/dashboard/lead` | Director, Division Head, GL Delivery, Team Leader Engineering, Managed Service Lead | Pemantauan kapasitas beban kerja tim (Workload Capacity), delegasi tiket penugasan, presensi live tim, dan pengawasan tenggat waktu kritis divisi. |
| 7 | **Dashboard Field & Technical Engineer** | `/dashboard/engineer` | Network Engineer, Security Engineer, Field Support (EOS), Maintenance | Pelaksanaan tugas operasional harian (Task Saya), pemantauan jadwal kerja pribadi, pelaporan lembar waktu (Timesheet), dan pencatatan presensi GPS. |

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

### 7. PROTOKOL SERAH TERIMA PROYEK INTERNAL (SOP GATEKEEPER HANDOVER)

Berdasarkan *Formulir Check List Serah Terima Proyek* dan SOP Tata Kelola Internal (halaman 1–6), sistem menerapkan mekanisme **Gatekeeper** untuk memvalidasi kelayakan proyek dari tim Komersial ke tim Delivery, mencegah terjadinya *Scope Creep* (pelebaran lingkup tanpa kompensasi), serta memastikan kepatuhan administrasi dan keuangan.

```
[TAHAP 1: SALES/COMMERCIAL]   --> [TAHAP 2: INVENTARISASI DOKUMEN (4 KATEGORI)]
             |                                    |
             v                                    v
[TAHAP 3: PERSIAPAN HANDOVER] --> [TAHAP 4: RAPAT KLARIFIKASI & REVIEW GATEKEEPER]
                                                  |
                                                  v
                                      +-----------------------+
                                      | Validasi PM & Kadiv   |
                                      +-----------------------+
                                     /                         \
                      (Lengkap / Valid)                    (Ada Catatan / Kurang)
                             |                                       |
                             v                                       v
               [TAHAP 5A: HANDOVER APPROVED]           [TAHAP 5B: HANDOVER CONDITIONAL]
               - Proyek resmi masuk "Deliver"          - Tenggat perbaikan: Max 2x24 Jam (48 Jam)
               - Tanggung jawab teknis beralih ke PM   - Status: "Menunggu Handover"
                             |                                       |
                             +------------------<--------------------+ (Setelah diperbaiki)
                             |
                             v
               [TAHAP 6: TRANSISI PIC TEKNIS KE PM]
               - Notifikasi resmi ke Klien bahwa PIC Teknis beralih ke Project Manager
```

---

#### 7.1. 4 Kategori Formulir Checklist Serah Terima (Internal Handover)
1. **I. Legal & Komersial:**
   * Salinan Kontrak / SPK / Surat Penunjukan / Dokumen SLA resmi ditandatangani.
   * BoM & BoW Proposal Final yang telah disepakati bersama Klien.
   * Notulen Negosiasi & Addendum Perubahan Lingkup (jika ada penyesuaian komersial).
2. **II. Kebutuhan & Lingkup (Scope of Work):**
   * Dokumen *Scope of Work* (SoW) terperinci dengan batasan pekerjaan (*in-scope* vs *out-of-scope*) yang tegas.
   * *User Requirement Specification* (URS) atau lembar kebutuhan spesifik klien.
   * Topologi / Diagram Desain / Arsitektur Jaringan Awal.
   * Ketentuan Garansi & Kontrak Pemeliharaan Operasional.
3. **III. Administrasi & Finansial:**
   * Bukti Pembayaran Uang Muka (DP) / Konfirmasi Faktur Termin 1 dari tim Finance.
   * Jadwal Penagihan (*Billing Milestone Schedule*) yang diselaraskan dengan tahapan deliverable.
4. **IV. Kontak PIC Klien & Catatan Lapangan:**
   * Kontak PIC Teknis Klien (Nama, Jabatan, HP, Email).
   * Kontak PIC Bisnis / Owner Klien (Nama, Jabatan, HP, Email).
   * Kontak PIC Finance / Invoicing Klien (Nama, Jabatan, HP, Email).
   * Catatan Khusus Sales: Komitmen lisan pra-deal, aturan jam kerja gedung, prosedur perizinan akses data center/lokasi klien.

---

#### 7.2. Ketentuan Status Pengesahan Gatekeeper
* **Handover Approved:**
  Disahkan oleh PMO / Kadiv apabila seluruh 4 kategori checklist terpenuhi dan SoW valid tanpa potensi *scope creep*. Proyek secara otomatis berpindah ke tahap **Deliver**, proses menjadi *In Progress*, serta Project Manager dan Divisi Pelaksana resmi ditugaskan.
* **Handover Conditional (Bersyarat):**
  Diberlakukan jika terdapat dokumen minor yang belum lengkap atau komitmen lisan yang belum tertuang dalam dokumen tertulis. Tim Sales diberikan waktu maksimal **2 x 24 Jam (48 Jam)** untuk melengkapi dokumen atau membuat addendum tertulis sebelum implementasi fisik lapangan dimulai.

---

#### 7.3. Matriks RACI Serah Terima (Handover RACI Matrix)
| Aktivitas / Tahapan | Sales / BDM | Presales / SA | PMO / Kadiv | Project Manager | Lead Engineer / Teknisi | Finance |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **1. Registrasi PO & Berkas Peluang** | **A / R** | C | I | I | I | C |
| **2. Pengisian 4 Kategori Checklist** | **A / R** | C | C | I | I | C |
| **3. Verifikasi Gatekeeper & Validasi SoW** | C | C | **A** | **R** | I | I |
| **4. Pengesahan Handover (Approved/Conditional)** | I | I | **A** | **R** | I | I |
| **5. Penugasan PM & Alokasi Divisi Teknis** | I | I | **A** | **R** | C | I |
| **6. Transisi PIC Teknis ke Klien (Kick-off)** | C | I | I | **A / R** | C | I |

*Keterangan:* **A** = Accountable (Pengambil Keputusan Utama), **R** = Responsible (Pelaksana), **C** = Consulted (Konsultasi), **I** = Informed (Menerima Notifikasi).
