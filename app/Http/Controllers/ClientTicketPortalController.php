<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManagedServiceTicket;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientTicketPortalController extends Controller
{
    /**
     * Tampilan Utama Portal Aduan & Helpdesk Klien Eksternal
     */
    public function index(Request $request)
    {
        $trackingTicket = null;
        $trackNumber = $request->input('track');

        if ($trackNumber) {
            $trackingTicket = ManagedServiceTicket::with('assignedEngineer')
                ->where('ticket_number', trim($trackNumber))
                ->first();
        }

        return view('public.helpdesk.index', compact('trackingTicket', 'trackNumber'));
    }

    /**
     * Simpan Tiket Aduan Baru dari Klien Eksternal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'reported_by'   => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:50',
            'incident_date' => 'required|date',
            'category'      => 'required|string|max:100',
            'priority'      => 'required|string|in:Low,Medium,High,Critical',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'attachment'    => 'nullable|file|max:10240|mimes:jpeg,png,jpg,pdf,zip,doc,docx',
        ], [
            'client_name.required'   => 'Nama Perusahaan / Klien (PT) wajib diisi.',
            'reported_by.required'   => 'Nama Customer / Pelapor wajib diisi.',
            'contact_email.required' => 'Email kontak wajib diisi.',
            'contact_email.email'    => 'Format email tidak valid.',
            'contact_phone.required' => 'Nomor WhatsApp / Telepon wajib diisi.',
            'incident_date.required' => 'Tanggal & waktu kejadian wajib diisi.',
            'category.required'      => 'Kategori kendala wajib dipilih.',
            'priority.required'      => 'Tingkat urgensi wajib dipilih.',
            'title.required'         => 'Subjek / Judul kendala wajib diisi.',
            'description.required'   => 'Deskripsi masalah wajib diisi dengan lengkap.',
            'attachment.max'         => 'Ukuran file lampiran maksimal 10MB.',
            'attachment.mimes'       => 'Format file lampiran harus JPG, PNG, PDF, DOCX, atau ZIP.',
        ]);

        // 1. Generate Nomor Tiket Otomatis Unik: TCK-YYYYMM-XXXX
        $yearMonth = date('Ym');
        $prefix = "TCK-{$yearMonth}-";
        $latest = ManagedServiceTicket::where('ticket_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->ticket_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        }
        $ticketNumber = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        // 2. Upload Lampiran jika ada
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('client_tickets', $filename, 'public');
        }

        // 3. Mapping Priority ke format Managed Service & hitung SLA Deadline
        $incidentTime = Carbon::parse($validated['incident_date']);
        $priorityLabel = match($validated['priority']) {
            'Critical' => 'P1 - Critical',
            'High'     => 'P2 - Major',
            'Medium'   => 'P3 - Minor',
            'Low'      => 'P4 - Low',
            default    => 'P3 - Minor',
        };

        $slaHours = match($validated['priority']) {
            'Critical' => 2,
            'High'     => 4,
            'Medium'   => 12,
            'Low'      => 24,
            default    => 12,
        };
        $slaDeadline = $incidentTime->copy()->addHours($slaHours);

        // 4. Simpan ke database managed_service_tickets
        $ticket = ManagedServiceTicket::create([
            'ticket_number'   => $ticketNumber,
            'client_name'     => $validated['client_name'],
            'reported_by'     => $validated['reported_by'],
            'contact_email'   => $validated['contact_email'],
            'contact_phone'   => $validated['contact_phone'],
            'incident_date'   => $validated['incident_date'],
            'category'        => $validated['category'],
            'type'            => 'Incident',
            'priority'        => $priorityLabel,
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'status'          => 'Open',
            'source'          => 'External Portal',
            'sla_deadline'    => $slaDeadline,
            'sla_met'         => true,
            'attachment_path' => $attachmentPath,
        ]);

        // 5. Buat Notifikasi Internal untuk Tim Maintenance & Leader
        try {
            $recipients = User::where(function($q) {
                $q->where('email', 'like', '%doris%')
                  ->orWhere('email', 'like', '%susanto%')
                  ->orWhereHas('roles', fn($r) => $r->whereIn('name', [
                      'Lead Maintenance', 'Maintenance', 'Managed Service', 'Group Leader Delivery & Operation', 'Division Head'
                  ]));
            })->get();

            foreach ($recipients as $recipient) {
                Notification::create([
                    'user_id' => $recipient->id,
                    'title'   => 'Tiket Aduan Baru dari Klien Eksternal (' . $validated['client_name'] . ')',
                    'message' => "Tiket [{$ticketNumber}] {$validated['title']} dilaporkan oleh {$validated['reported_by']}.",
                    'url'     => route('ms.tickets.index', ['search' => $ticketNumber]),
                    'is_read' => false,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning("Client ticket notification error: " . $e->getMessage());
        }

        // 6. Return response dengan flash data
        return redirect()->route('public.helpdesk.index', ['track' => $ticketNumber])
            ->with('ticket_created', true)
            ->with('ticket_number', $ticketNumber)
            ->with('ticket_title', $validated['title'])
            ->with('client_name', $validated['client_name'])
            ->with('priority', $validated['priority'])
            ->with('sla_deadline', $slaDeadline->translatedFormat('d F Y, H:i') . ' WIB')
            ->with('success', "Tiket aduan resmi Anda berhasil diterbitkan dengan nomor {$ticketNumber}. Tim teknis IP-NET sedang memproses penanganan kendala Anda.");
    }

    /**
     * Tracking / Lacak Status Tiket oleh Klien Eksternal
     */
    public function track(Request $request)
    {
        $ticketNumber = trim($request->input('ticket_number', ''));
        if (empty($ticketNumber)) {
            return redirect()->route('public.helpdesk.index')->with('error', 'Silakan masukkan nomor tiket yang valid.');
        }

        return redirect()->route('public.helpdesk.index', ['track' => $ticketNumber]);
    }

    /**
     * Detail Spesifik Tiket (JSON atau View Langsung)
     */
    public function show($ticketNumber)
    {
        $ticket = ManagedServiceTicket::with(['assignedEngineer', 'project'])
            ->where('ticket_number', trim($ticketNumber))
            ->firstOrFail();

        return view('public.helpdesk.show', compact('ticket'));
    }
}
