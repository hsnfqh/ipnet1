# DOKUMEN ALUR BISNIS (BUSINESS WORKFLOW) & PANDUAN PERAN (ROLES)
## Sistem Manajemen Proyek & Operasional — PT IP Network Solusindo (FPMS)

> **Catatan Dokumen:**  
> File ini disusun sebagai panduan lengkap alur bisnis sistem dari awal proyek didapatkan hingga operasional pemeliharaan selesai. Dokumen ini bebas Anda sunting atau sesuaikan jika ada kebijakan internal yang ingin diselaraskan.

---

## DAFTAR ISI
1. [Struktur Organisasi & Daftar Seluruh Peran (Roles)](#1-struktur-organisasi--daftar-seluruh-peran-roles)
2. [Peta Alur Bisnis Global (End-to-End Big Picture)](#2-peta-alur-bisnis-global-end-to-end-big-picture)
3. [Alur Tahap 1: Inisiasi, Desain, & Deal (Sales & Presales)](#3-alur-tahap-1-inisiasi-desain--deal-sales--presales)
4. [Alur Tahap 2: Serah Terima Komersial (Commercial Handover)](#4-alur-tahap-2-serah-terima-komersial-commercial-handover)
5. [JALUR A: Proyek Implementasi Baru (Delivery & PMO)](#5-jalur-a-proyek-implementasi-baru-delivery--pmo)
6. [JALUR B: Layanan Managed Service & SLA (Helpdesk & Operasional)](#6-jalur-b-layanan-managed-service--sla-helpdesk--operasional)
7. [Detail Khusus: Alur Tiket Managed Service ke Lead Engineer](#7-detail-khusus-alur-tiket-managed-service-ke-lead-engineer)
8. [Matriks Menu Sistem Berdasarkan Peran Pengguna](#8-matriks-menu-sistem-berdasarkan-peran-pengguna)

---

## 1. STRUKTUR ORGANISASI & DAFTAR SELURUH PERAN (ROLES)

Sistem membagi tim ke dalam 3 cabang besar di bawah jajaran Eksekutif:

```
                            +-----------------------+
                            |       DIREKTUR        | (Hariyadi)
                            +-----------+-----------+
                                        |
                            +-----------+-----------+
                            |     DIVISION HEAD     | (Susanto Djaya)
                            +-----------+-----------+
                                        |
            +---------------------------+---------------------------+
            |                                                       |
+-----------+-----------+                               +-----------+-----------+
| COMMERCIAL & SOLUTION |                               |  DELIVERY & OPERATION |
+-----------+-----------+                               +-----------+-----------+
| - Sales (Account Mgr) |                               | - PMO (Project Manager)
| - BusDev (BDM)        |                               | - Team Leader Network
| - Pre-Sales Specialist|                               | - Team Leader Security
| - Solution Architect  |                               | - Managed Service Coord.
| - CRO (Customer Rel.) |                               | - Field Support (EOS)
+-----------------------+                               | - Network Engineer L1/L2
                                                        | - Security Engineer
                                                        +-----------------------+
```

### Rincian Peran Resmi & Tanggung Jawabnya:

| No | Nama Peran | Penanggung Jawab (Akun) | Divisi / Cabang | Tanggung Jawab Utama |
|:--:|:---|:---|:---|:---|
| 1 | **Director / Direktur** | Hariyadi | Eksekutif | Meninjau performa finansial global, menyetujui proyek bernilai besar, dan memantau pipeline eksekutif. |
| 2 | **Division Head / Group Leader** | Susanto Djaya | Operasional Induk | Mengarahkan koordinasi lintas divisi komersial & delivery, pengawas tertinggi alokasi sumber daya. |
| 3 | **Sales (Account Manager)** | Raiza, Ribka, Widodo | Commercial | Mencari prospek, negosiasi harga, mengunggah kontrak PO/SPK, dan mengajukan serah terima (*Handover*). |
| 4 | **Business Development (BDM)** | Erie | Commercial | Kualifikasi lelang tender B2B, kemitraan strategis, dan peluang pasar baru. |
| 5 | **Solution Architect** | Aris Sadewo | Commercial | Perancangan cetak biru topologi arsitektur (HLD/LLD), formulasi rincian material (BoQ & SKU), dan estimasi mandays. |
| 6 | **Pre-Sales Specialist** | Akbar | Commercial | Pembuatan proposal penawaran teknis, survei kelayakan lapangan, dan sesi uji coba (*PoC*). |
| 7 | **CRO (Customer Relation)** | Taufik Hidayat | Commercial | Menjaga kepuasan klien pasca-implementasi, survei CSAT, dan pemantauan perpanjangan kontrak (*Renewal*). |
| 8 | **PMO (Project Manager)** | Kuncoro, Rizki | Delivery | Verifikasi kelayakan serah terima komersial, tata kelola tahapan proyek (Stage-Gate), validasi BAST, dan timeline. |
| 9 | **Team Leader Network** | Nugraha | Delivery (Network) | Membagi beban teknisi jaringan, memantau konfigurasi switch/router/BGP, dan review kendala teknis. |
| 10 | **Team Leader Security** | Ignatius | Delivery (Security) | Membagi beban teknisi keamanan siber, konfigurasi firewall/VPN, audit keamanan, dan review ancaman. |
| 11 | **Managed Service / Lead Maintenance** | Doris | Delivery & Operation | Bertindak sebagai **Service Desk / Customer Service SPOC**, menerima keluhan klien, membuat tiket SLA, dan memantau kepatuhan SLA. |
| 12 | **Field Support (EOS)** | Mario, Eris | Delivery & Operation | Teknisi lapangan untuk penanganan insiden hardware, penggantian modul/kabel, dan kunjungan Preventive Maintenance. |
| 13 | **Network Engineer** | Dedy, Rorik, Syaiful, Shiamsyah | Delivery (Network) | Eksekutor konfigurasi teknis jaringan fisik/logikal lapangan dan remote. |
| 14 | **Security Engineer** | Eka Kurnia | Delivery (Security) | Eksekutor konfigurasi firewall, hardening sistem, dan mitigasi insiden siber. |

---

## 2. PETA ALUR BISNIS GLOBAL (END-TO-END BIG PICTURE)

Secara garis besar, sebuah proyek di PT IP Network Solusindo bergerak dalam **4 Tahapan Utama**:

```
[ TAHAP 1: ACQUIRE ]       -->  [ TAHAP 2: PLAN & DESIGN ]  -->  [ TAHAP 3: DELIVER ]      -->  [ TAHAP 4: OPERATE ]
(Sales & BusDev)                (Solution Architect & Pre)       (PMO, Lead, & Engineer)        (Managed Service / CS)
- Prospek & Tender              - Kajian Kelayakan               - Instalasi & Konfigurasi      - Helpdesk 24/7 & Tiket
- PO / SPK Terbit               - Cetak Biru HLD/LLD             - Pengujian (UAT)              - Preventive Maintenance
- Closed Won                    - BoQ & Proposal Teknis          - Serah Terima Klien (BAST)    - Perpanjangan Kontrak
```

Saat kontrak berhasil didapat (**Closed Won**), ada **2 Pilihan Jalur Handover**:
1. **Jalur A: Proyek Implementasi Baru** $\rightarrow$ Diserahkan ke **PMO & Delivery**.
2. **Jalur B: Kontrak Managed Service & SLA** $\rightarrow$ Diserahkan ke **Managed Service**.

---

## 3. ALUR TAHAP 1: INISIASI, DESAIN, & DEAL (SALES & PRESALES)

1. **Pendaftaran Prospek (Sales / BDM):**
   * Sales mendaftarkan data klien baru di menu `Client` dan mencatat peluang di `Peluang & Pipeline` (`/acquire` atau `/sales-projects`).
2. **Bantuan Rekayasa Teknis (Presales & Solution Architect):**
   * Jika tender/prospek membutuhkan proposal teknis atau survei, Presales & Solution Architect menyusun dokumen di menu `Desain & SOW / Proposal` (`/presales/proposals`).
   * Mengunggah dokumen HLD (High Level Design), batasan pekerjaan (Scope of Work - SoW), dan perkiraan Bill of Quantity (BoQ).
3. **Kesepakatan Komersial (Closed Won):**
   * Penawaran disetujui klien.
   * Klien menerbitkan dokumen resmi: **Purchase Order (PO)**, **Surat Pesanan Kerja (SPK)**, atau **Kontrak Perjanjian Kerjasama**.
   * Status penjualan berubah menjadi **Closed Won**.

---

## 4. ALUR TAHAP 2: SERAH TERIMA KOMERSIAL (COMMERCIAL HANDOVER)

Setelah deal, Sales wajib melakukan serah terima resmi di menu:  
👉 **`Peluang & Kontrak -> Serah Terima Proyek` (`/sales/handover`)**

Sales mengisi formulir serah terima dan memilih **Target Serah Terima**:

```
                                [ CLOSED WON (PO TERBIT) ]
                                             │
                                             ▼
                               [ Menu: /sales/handover ]
                                             │
                      ┌──────────────────────┴──────────────────────┐
                      ▼                                             ▼
           [ OPSI 1: TARGET PMO ]                       [ OPSI 2: TARGET MANAGED SERVICE ]
        Untuk Proyek Pengadaan /                     Untuk Kontrak Pemeliharaan SLA /
        Pemasangan Fisik Baru dari Nol               Langganan Operasional Jaringan
```

---

## 5. JALUR A: PROYEK IMPLEMENTASI BARU (DELIVERY & PMO)

Jalur ini digunakan untuk proyek instalasi fisik (misal: pengadaan router di 50 cabang, penarikan kabel fiber optic, implementasi firewall data center baru).

```
1. Sales kirim Handover Tipe 1 (PMO Delivery).
                      │
                      ▼
2. PMO (Kuncoro/Rizki) membuka menu PMO Dashboard (/pmo).
   - Memeriksa dokumen legal (PO, SOW, BoQ).
   - Menyetujui Handover (Handover Approved).
   - Menunjuk Project Manager (PM).
   - Memilih ALOKASI DIVISI TEKNIS:
       * Divisi Network
       * Divisi Security
       * Keduanya (Network & Security)
                      │
                      ▼
3. Proyek otomatis masuk ke Dashboard Lead Engineer divisi yang dipilih:
   - Lead Network (Nugraha) dan/atau Lead Security (Ignatius).
                      │
                      ▼
4. Lead Engineer membagi beban kerja ke Teknisi Lapangan:
   - Membuka menu Task (/tasks) -> Delegasi Tugas Baru.
   - Menugaskan Network Engineer (Dedy, Rorik, dll.) atau Security Engineer (Eka).
                      │
                      ▼
5. Teknisi mengeksekusi instalasi fisik, konfigurasi, dan pengujian.
                      │
                      ▼
6. Klien melakukan UAT & menandatangani Berita Acara Serah Terima (BAST).
   (Proyek tahap implementasi resmi SELESAI).
```

---

## 6. JALUR KHUSUS: LAYANAN MANAGED SERVICE & SLA (OPERASIONAL & PEMELIHARAAN)

Jalur ini khusus menangani operasional rutin, kontrak pemeliharaan berkala (*Maintenance Support & SLA*), serta penanganan insiden/gangguan jaringan dan keamanan klien.

### Karakteristik & Peran Tim Managed Service:
> **Managed Service bertindak sebagai "Customer Service / Helpdesk / Single Point of Contact (SPOC)"**  
> 1. **Garda Terdepan Klien:** Tim Managed Service (Doris, Mario, Eris) adalah pintu utama komunikasi dengan klien (layanan pelanggan).  
> 2. **PIC Pendamping Lapangan:** Tim Managed Service menunjuk personil internalnya untuk mendampingi langsung di lapangan (*Client Liaison & Field Companion*).  
> 3. **Pengawal SLA:** Memantau durasi penanganan agar tidak melewati batas waktu perjanjian kontrak (*SLA Compliance*).  
> 4. **Bukan Eksekutor Utama Konfigurasi:** Untuk troubleshooting teknis mendalam (konfigurasi switch, BGP, core routing, firewall security), eksekusinya didelegasikan oleh **Lead Engineer** ke teknisi spesialis.

---

## 7. DETAIL LENGKAP: ALUR PROSES FLOW MANAGED SERVICE (END-TO-END)

Berikut adalah urutan alur kerja Managed Service secara detail dari hulu ke hilir:

```
+---------------------------------------------------------------------------------------------------+
| TAHAP 1: KONTRAK / ORDER MASUK DARI SALES & PRESALES                                              |
|                                                                                                   |
| • Sales & Presales telah mengunci kesepakatan kontrak Managed Service / SLA dengan Klien.         |
| • Tipe Proyek / Domain Teknis SUDAH DITENTUKAN sejak awal oleh Sales & Presales:                  |
|     - Divisi Network                                                                              |
|     - Divisi Security                                                                             |
|     - Keduanya (Network & Security)                                                               |
| • Pekerjaan ini MASUK LANGSUNG ke Managed Service (tidak melalui PMO).                            |
+---------------------------------------------------------------------------------------------------+
                                                  │
                                                  ▼
+---------------------------------------------------------------------------------------------------+
| TAHAP 2: MANAGED SERVICE MENERIMA & MENENTUKAN PIC PENDAMPING                                     |
|                                                                                                   |
| • Tim Managed Service (Doris / Helpdesk) menerima serah terima order atau tiket komplain.         |
| • Managed Service membuka menu "Tickets & SLA" (/managed-service/tickets).                        |
| • Managed Service MENENTUKAN PIC PENDAMPING dari tim Managed Service sendiri:                     |
|     -> Misal: Menunjuk Si A (Mario atau Eris) sebagai PIC Pendamping Lapangan / Client Liaison.   |
| • Tipe Proyek / Divisi Penanganan Teknis (Network / Security / Keduanya) diteruskan ke Lead:      |
|     (Hanya ada opsi Network, Security, atau Keduanya. Tidak ada opsi Maintenance lagi             |
|      karena Managed Service sendiri yang memproses tiket ini).                                    |
+---------------------------------------------------------------------------------------------------+
                                                  │
                                                  ▼
+---------------------------------------------------------------------------------------------------+
| TAHAP 3: TIKET / PEKERJAAN MASUK KE DASHBOARD LEAD ENGINEER TERKAIT                               |
|                                                                                                   |
| • Berdasarkan Tipe Proyek yang sudah ditentukan, tiket otomatis muncul di radar Lead Engineer:    |
|     * Jika Tipe "Network"  --> Masuk ke Dashboard Team Leader Network (Nugraha)                   |
|     * Jika Tipe "Security" --> Masuk ke Dashboard Team Leader Security (Ignatius)                 |
|     * Jika "Keduanya"      --> Masuk ke Nugraha & Ignatius secara bersamaan                       |
| • Lead Engineer menerima notifikasi pekerjaan baru yang membutuhkan alokasi tim teknisnya.       |
+---------------------------------------------------------------------------------------------------+
                                                  │
                                                  ▼
+---------------------------------------------------------------------------------------------------+
| TAHAP 4: LEAD ENGINEER MEMBAGI BEBAN & MENUGASKAN ENGINEER SPESIALIS (ASSIGNMENT)                 |
|                                                                                                   |
| • Lead Engineer (Nugraha / Ignatius) mengecek beban kerja timnya di menu Workload & Task.         |
| • Lead Engineer memilih dan menugaskan Engineer Teknis yang berkompeten dan tersedia:            |
|     * Divisi Network  --> Lead menugaskan Dedy, Rorik, Syaiful, atau Shiamsyah                    |
|     * Divisi Security --> Lead menugaskan Eka Kurnia                                              |
| • Engineer yang dipilih menerima rincian pekerjaan teknis dan target batas waktu (SLA).           |
+---------------------------------------------------------------------------------------------------+
                                                  │
                                                  ▼
+---------------------------------------------------------------------------------------------------+
| TAHAP 5: EKSEKUSI TANDEM / KOLABORASI DI LAPANGAN & REMOTE                                        |
|                                                                                                   |
| • Personil Pendamping Managed Service (Si A: misal Mario) TURUN BERSAMA / MENDAMPINGI             |
|   Engineer Teknis yang ditunjuk Lead Engineer (misal: Dedy).                                      |
| • Pembagian Peran Nyata di Lokasi:                                                                |
|     1. PIC Pendamping Managed Service (Mario / Eris):                                             |
|        - Bertindak sebagai Customer Service di hadapan klien (senyum, salam, komunikasi).         |
|        - Menjaga kepuasan klien dan mengawal ketepatan waktu SLA.                                 |
|        - Membantu penanganan fisik ringan / cek kabel & kelengkapan perangkat.                    |
|     2. Engineer Teknis (Dedy / Rorik / Eka Kurnia):                                               |
|        - Fokus penuh pada eksekusi teknis (konfigurasi CLI, routing, firewall policy, dsb).       |
|        - Memastikan perangkat dan jaringan kembali up normal.                                     |
+---------------------------------------------------------------------------------------------------+
                                                  │
                                                  ▼
+---------------------------------------------------------------------------------------------------+
| TAHAP 6: SELESAI PENANGANAN, VERIFIKASI KLIEN, & CLOSING TIKET                                    |
|                                                                                                   |
| • Engineer Teknis memperbarui status tiket teknis menjadi "Resolved" dan mencatat log kerja.      |
| • PIC Pendamping Managed Service meminta tanda tangan Service Report / Berita Acara Kunjungan     |
|   kepada pihak Klien sebagai bukti pekerjaan lapangan telah tuntas dengan baik.                   |
| • Tim Managed Service Helpdesk (Doris) melakukan konfirmasi akhir ke Klien:                       |
|     - Memastikan komplain klien 100% beres.                                                       |
|     - Mengubah status tiket menjadi "Closed".                                                     |
|     - Sistem mencatat skor kepatuhan SLA (SLA Met) untuk rekap laporan bulanan.                   |
+---------------------------------------------------------------------------------------------------+
```

### Rangkuman Pembagian Peran di Managed Service:
| Pelaku | Nama PIC | Peran Konkret dalam Alur Managed Service |
|:---|:---|:---|
| **Sales & Presales** | Raiza / Akbar / Aris | Mengunci kontrak & sudah menetapkan tipe domain teknis (Network / Security / Keduanya). |
| **Managed Service Helpdesk** | Doris | Penerima tiket, Customer Service SPOC, pengawal waktu SLA, dan penunjuk personil pendamping internal. |
| **PIC Pendamping (Managed Service)** | Mario / Eris | **Si A yang ditunjuk** mendampingi di lapangan, berkomunikasi langsung dengan klien, mengurus administrasi & form kunjungan. |
| **Lead Engineer** | Nugraha (Net) / Ignatius (Sec) | Memantau dashboard divisi, mengecek kapasitas tim, dan memilih teknisi spesialis yang bertugas. |
| **Engineer Teknis Spesialis** | Dedy, Rorik, Syaiful, Eka Kurnia | Melakukan troubleshooting dan konfigurasi teknis perangkat bersama pendamping di lokasi. |

---

## 8. MATRIKS MENU SISTEM BERDASARKAN PERAN PENGGUNA

Tabel rujukan menu yang digunakan oleh masing-masing peran saat menjalankan tugasnya sehari-hari:

| Peran Pengguna | Menu Utama yang Diakses | Apa yang Dilakukan di Menu Tersebut? |
|:---|:---|:---|
| **Sales** | `Peluang & Pipeline`, `Serah Terima Proyek` | Mencatat deal, unggah berkas PO, submit serah terima ke PMO atau Managed Service. |
| **Solution Architect / Presales** | `Desain & SOW / Proposal`, `Inventory` | Merancang HLD/LLD, BoQ perangkat, menghitung estimasi mandays teknisi. |
| **PMO (Project Manager)** | `PMO Dashboard`, `Project Master`, `Task` | Verifikasi dokumen serah terima, menunjuk alokasi divisi teknis (Network/Security), audit timeline. |
| **Managed Service (Helpdesk)** | `Tickets & SLA`, `Assets & Devices`, `Maintenance` | Menerima komplain klien, buka tiket insiden, arahkan alokasi ke divisi (Network/Security), pantau SLA. |
| **Lead Engineer (Network / Sec)** | `Dashboard Lead`, `Project`, `Task Management` | Memantau grafik beban tim, menerima delegasi proyek/tiket, menugaskan teknisi spesialis (*Assign PIC*). |
| **Field & Technical Engineer** | `Task Saya`, `Work Schedule`, `Timesheet` | Menerima instruksi kerja, melihat jadwal harian/mingguan, update status tiket, isi absensi GPS & log kerja. |
| **Direktur & Division Head** | `Executive Dashboard`, `PMO Dashboard`, `Laporan` | Melihat ringkasan menyeluruh portofolio proyek, pendapatan pipeline, dan performa SLA korporasi. |
| **Seluruh Role** | `Digital Signature` | **Arsip Privat:** Mengajukan tanda tangan dokumen elektronik. Dokumen hanya terlihat oleh pengaju dokumen sendiri, KECUALI jika menunjuk personil lain sebagai penandatangan internal (TTD Internal). |

### Aturan Privasi Digital Signature (E-Signature):
> **Prinsip Utama: Riwayat Pribadi & Privasi Total**  
> 1. Setiap pengguna (Sales, Lead Engineer, PMO, Engineer) hanya dapat melihat riwayat berkas dokumen yang **diajukan oleh dirinya sendiri**.  
> 2. Dokumen **TIDAK AKAN PERNAH** bocor atau muncul di menu pengguna lain (misal: Lead Engineer Nugraha tidak bisa melihat dokumen Sales Raiza).  
> 3. **PENGECUALIAN:** Jika pembuat dokumen secara spesifik menunjuk pengguna lain sebagai **Penandatangan Internal (TTD Internal)**, maka dokumen tersebut secara sah akan terhubung dan muncul di menu Digital Signature milik penandatangan yang ditunjuk agar dapat diperiksa dan ditandatangani.

---

> **Dokumen ini dibuat secara otomatis dan tersimpan di repositori kerja Anda.**  
> Anda dapat membuka file ini kapan saja di:  
> `file:///c:/Users/ahmad/web-ipnet/FLOW_BISNIS_PROYEK_DAN_ROLE.md`  
> Silakan dibaca, ditinjau, atau diubah sesuai dengan preferensi istilah dan alur kerja internal tim Anda.
