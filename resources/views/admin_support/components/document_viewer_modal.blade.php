{{-- MODAL: Interactive Document PDF & Sheet Viewer --}}
<div x-show="isDocPreviewOpen" 
     x-cloak 
     class="fixed inset-0 z-[60] overflow-y-auto bg-black/75 flex items-center justify-center p-3 sm:p-6 backdrop-blur-xs">
    
    <div class="bg-gray-100 rounded-2xl max-w-4xl w-full max-h-[92vh] flex flex-col border border-gray-300 shadow-2xl animate-fade-in-up overflow-hidden" 
         @click.outside="isDocPreviewOpen = false">
        
        {{-- Viewer Control Bar --}}
        <div class="p-4 px-6 bg-[#17151C] text-white flex items-center justify-between flex-shrink-0 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#C81E2C] text-white flex items-center justify-center font-bold text-xs">
                    PDF
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-white tracking-wide" x-text="activeDoc ? activeDoc.doc_number : 'Dokumen'"></h3>
                        <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-white/10 text-gray-300" x-text="activeDoc ? activeDoc.doc_type : ''"></span>
                    </div>
                    <p class="text-[11px] text-gray-400">Pratinjau Berkas Dokumen Resmi (Signed & Stamped Copy)</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak</span>
                </button>
                <button type="button" @click="isDocPreviewOpen = false" class="text-gray-400 hover:text-white p-1 rounded-lg text-lg transition">
                    &times;
                </button>
            </div>
        </div>

        {{-- Document Sheet Content (A4 Simulation) --}}
        <div class="flex-1 overflow-y-auto p-4 sm:p-8 bg-[#E5E5E5] flex justify-center">
            
            <div class="bg-white max-w-3xl w-full p-8 sm:p-12 rounded-lg shadow-lg border border-gray-200 text-gray-900 space-y-6 text-xs font-sans min-h-[850px] relative">
                
                {{-- Official Letterhead / Kop Surat --}}
                <div class="flex items-start justify-between border-b-2 border-gray-900 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#C81E2C] text-white font-extrabold text-sm flex items-center justify-center tracking-tighter shadow-sm">
                            IP
                        </div>
                        <div>
                            <h1 class="text-base font-extrabold tracking-tight text-gray-900 leading-none">PT IP-NET SOLUSINDO</h1>
                            <p class="text-[10px] text-gray-500 font-semibold tracking-wider mt-1 uppercase">Enterprise Network, Fiber Optic & Cloud Infrastructure</p>
                        </div>
                    </div>
                    <div class="text-right text-[10.5px] text-gray-500 leading-tight">
                        <p class="font-bold text-gray-800">Gedung Cyber 2 Tower Lantai 18</p>
                        <p>Jl. HR Rasuna Said Blok X-5 No. 13, Jakarta 12950</p>
                        <p>Telp: (021) 5290-8800 | legal@ipnetsolusindo.com</p>
                    </div>
                </div>

                {{-- Document Title & Reference --}}
                <div class="text-center py-2 space-y-1">
                    <h2 class="text-sm sm:text-base font-extrabold uppercase tracking-wide text-gray-900 underline underline-offset-4" x-text="activeDoc ? activeDoc.doc_type + ' — ' + activeDoc.title : 'DOKUMEN RESMI'"></h2>
                    <p class="text-xs font-mono font-bold text-[#C81E2C]">NOMOR: <span x-text="activeDoc ? activeDoc.doc_number : ''"></span></p>
                </div>

                {{-- Parties Information --}}
                <div class="grid grid-cols-2 gap-4 p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Pihak Pertama (Penyedia):</span>
                        <p class="font-bold text-gray-900 text-xs">PT IP-NET SOLUSINDO</p>
                        <p class="text-gray-500 text-[11px]">Divisi Project Delivery & Managed Service</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Pihak Kedua (Klien / Mitra):</span>
                        <p class="font-bold text-gray-900 text-xs" x-text="activeDoc ? (activeDoc.client_name || activeDoc.vendor_name || 'Agung Sedayu Group - IT DEPT') : '-'"></p>
                        <p class="text-gray-500 text-[11px]">Departemen Pengadaan & Operasional IT</p>
                    </div>
                </div>

                {{-- Terms & Summary Table --}}
                <div class="space-y-3">
                    <div class="font-bold text-gray-800 text-xs uppercase tracking-wider border-b border-gray-200 pb-1">
                        I. Ruang Lingkup & Nilai Perjanjian
                    </div>
                    
                    <table class="w-full border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-100 border border-gray-200 text-gray-700 font-semibold">
                                <th class="p-2 text-left w-12">No</th>
                                <th class="p-2 text-left">Uraian / Spesifikasi Berkas</th>
                                <th class="p-2 text-center w-28">Versi / Status</th>
                                <th class="p-2 text-right w-40">Nilai Komersial (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border border-gray-200">
                                <td class="p-2.5 text-center font-bold">1</td>
                                <td class="p-2.5">
                                    <div class="font-bold text-gray-900" x-text="activeDoc ? activeDoc.title : ''"></div>
                                    <div class="text-[11px] text-gray-500 mt-0.5" x-text="'Kategori: ' + (activeDoc ? activeDoc.category : 'General') + ' • Lokasi Arsip: ' + (activeDoc ? (activeDoc.physical_archive_location || 'Lemari Legal Box 02') : '-')"></div>
                                </td>
                                <td class="p-2.5 text-center font-mono font-bold text-gray-700" x-text="activeDoc ? activeDoc.version : 'v1.0'"></td>
                                <td class="p-2.5 text-right font-bold text-emerald-700" x-text="activeDoc && activeDoc.value > 0 ? 'Rp ' + Number(activeDoc.value).toLocaleString('id-ID') : 'Rp 0 (SOW Non-Komersial)'"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Timeline & Validity --}}
                <div class="space-y-2">
                    <div class="font-bold text-gray-800 text-xs uppercase tracking-wider border-b border-gray-200 pb-1">
                        II. Masa Berlaku & Ketentuan
                    </div>
                    <div class="text-[11.5px] text-gray-600 space-y-1 leading-relaxed">
                        <p>1. Dokumen ini sah dan mengikat kedua belah pihak terhitung sejak tanggal <strong class="text-gray-900" x-text="formatReadableDate(activeDoc ? activeDoc.effective_date : '')"></strong> sampai dengan <strong class="text-gray-900" x-text="formatReadableDate(activeDoc ? activeDoc.expiry_date : '')"></strong>.</p>
                        <p>2. Seluruh lampiran teknis (SOW, BoQ, dan Berita Acara UAT) merupakan satu kesatuan utuh yang tidak terpisahkan.</p>
                        <p>3. Berkas digital ini telah melewati proses registrasi dan verifikasi kepatuhan administratif di Unit Admin Support & Central Vault PT IP-Net Solusindo.</p>
                    </div>
                </div>

                {{-- Signatures & Digital Stamp / E-Meterai --}}
                <div class="pt-6 border-t border-gray-200 grid grid-cols-2 gap-8 items-end">
                    
                    {{-- Pihak Pertama Signature --}}
                    <div class="text-center space-y-2">
                        <p class="text-[11px] font-bold text-gray-600 uppercase">Pihak Pertama</p>
                        <p class="text-[11px] text-gray-500">PT IP-NET SOLUSINDO</p>
                        
                        <div class="h-24 flex items-center justify-center relative my-1">
                            {{-- Circular Official Red Stamp --}}
                            <div class="w-20 h-20 rounded-full border-2 border-red-600 text-red-600 flex flex-col items-center justify-center p-1 transform -rotate-12 opacity-85 shadow-xs select-none">
                                <span class="text-[7.5px] font-black uppercase text-center leading-tight">PT IP-NET SOLUSINDO</span>
                                <span class="text-[6.5px] font-extrabold text-[#C81E2C] border-t border-b border-red-500 py-0.5 my-0.5">LEGAL VERIFIED</span>
                                <span class="text-[6px] font-bold">JAKARTA</span>
                            </div>
                            
                            {{-- Signature simulation --}}
                            <div class="absolute font-serif italic text-base font-bold text-blue-900 tracking-wider">
                                Budi Santoso, S.T.
                            </div>
                        </div>

                        <div class="border-t border-gray-400 pt-1">
                            <p class="font-bold text-gray-900 text-xs">Budi Santoso, S.T.</p>
                            <p class="text-[10px] text-gray-500">VP Operations & Governance</p>
                        </div>
                    </div>

                    {{-- Pihak Kedua Signature --}}
                    <div class="text-center space-y-2">
                        <p class="text-[11px] font-bold text-gray-600 uppercase">Pihak Kedua / Klien</p>
                        <p class="text-[11px] text-gray-500" x-text="activeDoc ? (activeDoc.client_name || activeDoc.vendor_name || 'Agung Sedayu Group') : 'Perwakilan Mitra'"></p>
                        
                        <div class="h-24 flex items-center justify-center relative my-1">
                            {{-- E-Meterai Box --}}
                            <div class="w-14 h-16 border border-emerald-600 bg-emerald-50 text-emerald-800 flex flex-col items-center justify-center p-1 rounded text-[7px] font-bold shadow-xs absolute left-2 select-none">
                                <span>METERAI</span>
                                <span class="text-[8px] font-black">10000</span>
                                <span class="text-[5px]">DIGITAL</span>
                            </div>

                            {{-- Client Signature simulation --}}
                            <div class="font-serif italic text-base font-bold text-blue-900 tracking-wider ml-8">
                                Ir. Hendra Gunawan
                            </div>
                        </div>

                        <div class="border-t border-gray-400 pt-1">
                            <p class="font-bold text-gray-900 text-xs">Ir. Hendra Gunawan</p>
                            <p class="text-[10px] text-gray-500">Authorized Client Representative</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- Footer Controls --}}
        <div class="p-3.5 px-6 bg-white border-t border-gray-200 flex items-center justify-between flex-shrink-0">
            <span class="text-xs text-gray-500 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Dokumen Digital Resmi Terproteksi & Bersertifikat</span>
            </span>

            <div class="flex items-center gap-2">
                <button type="button" @click="isDocPreviewOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition cursor-pointer">
                    Kembali ke Panel Inspeksi
                </button>
            </div>
        </div>

    </div>
</div>
