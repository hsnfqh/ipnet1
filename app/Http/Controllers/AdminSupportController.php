<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminDocument;
use App\Models\AdminDocumentChecklist;
use App\Models\AdminLogisticsDispatch;
use App\Models\AdminDispatchItem;
use App\Models\AdminSerialNumber;
use App\Models\AdminEquipmentAsset;
use App\Models\AdminProjectHandover;
use App\Models\Project;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminSupportController extends Controller
{
    /**
     * Dashboard Admin Support (Control Tower & Governance Overview)
     */
    public function index()
    {
        $totalDocs = AdminDocument::count();
        $verifiedDocs = AdminDocument::where('status', 'Verified / Complete')->count();
        $pendingVerifyDocs = AdminDocument::whereIn('status', ['Under Review', 'Draft'])->count();
        $clarificationDocs = AdminDocument::where('status', 'Clarification Requested')->count();
        
        $activeDispatches = AdminLogisticsDispatch::whereIn('status', ['Ready to Dispatch', 'In Transit'])->count();
        $totalSerialNumbers = AdminSerialNumber::count();
        $totalEquipmentAssets = AdminEquipmentAsset::count();
        $borrowedAssets = AdminEquipmentAsset::where('status', 'Borrowed by Engineer')->count();

        // Documents needing verification or clarification
        $pendingDocuments = AdminDocument::with(['project', 'client', 'vendor'])
            ->whereIn('status', ['Under Review', 'Clarification Requested', 'Draft'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Dispatches
        $recentDispatches = AdminLogisticsDispatch::with(['project', 'items'])
            ->latest()
            ->take(4)
            ->get();

        // Incomplete / Outstanding Project Checklists (Gatekeeper Alert)
        $outstandingChecklists = AdminDocumentChecklist::with(['project'])
            ->where('is_mandatory', true)
            ->where('is_verified', false)
            ->latest()
            ->take(5)
            ->get();

        // Recent Serial Numbers
        $recentSerialNumbers = AdminSerialNumber::latest()->take(5)->get();

        $projects = Project::select('id', 'name')->latest()->take(30)->get();
        $clients = Client::select('id', 'name')->latest()->take(30)->get();
        $vendors = Vendor::select('id', 'name')->latest()->take(30)->get();
        $engineers = User::all();

        return view('admin_support.dashboard', compact(
            'totalDocs',
            'verifiedDocs',
            'pendingVerifyDocs',
            'clarificationDocs',
            'activeDispatches',
            'totalSerialNumbers',
            'totalEquipmentAssets',
            'borrowedAssets',
            'pendingDocuments',
            'recentDispatches',
            'outstandingChecklists',
            'recentSerialNumbers',
            'projects',
            'clients',
            'vendors',
            'engineers'
        ));
    }

    /**
     * Master Document Register
     */
    public function documentsIndex(Request $request)
    {
        $query = AdminDocument::with(['project', 'client', 'vendor', 'verifier'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('doc_number', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%")
                  ->orWhere('client_name', 'like', "%{$s}%")
                  ->orWhere('vendor_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('doc_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $documents = $query->paginate(15)->withQueryString();
        $projects = Project::select('id', 'name')->latest()->get();
        $clients = Client::select('id', 'name')->latest()->get();
        $vendors = Vendor::select('id', 'name')->latest()->get();

        return view('admin_support.documents.index', compact('documents', 'projects', 'clients', 'vendors'));
    }

    /**
     * Store Document in Register
     */
    public function documentsStore(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'doc_type'  => 'required|string',
            'category'  => 'required|string',
        ]);

        $prefix = match($request->doc_type) {
            'SPK'       => 'SPK',
            'PO Client' => 'PO-CL',
            'PO Vendor' => 'PO-VN',
            'Contract'  => 'CTR',
            'WO'        => 'WO',
            'BAST'      => 'BAST',
            'Tender'    => 'TND',
            'Proposal'  => 'PRP',
            default     => 'DOC',
        };

        $year = date('Y');
        $count = AdminDocument::whereYear('created_at', $year)->count() + 1;
        $docNumber = sprintf('DOC-%s-%s-%04d', $prefix, $year, $count);

        AdminDocument::create([
            'doc_number'                => $request->doc_number ?: $docNumber,
            'title'                     => $request->title,
            'doc_type'                  => $request->doc_type,
            'category'                  => $request->category,
            'project_id'                => $request->project_id ?: null,
            'client_id'                 => $request->client_id ?: null,
            'vendor_id'                 => $request->vendor_id ?: null,
            'client_name'               => $request->client_name,
            'vendor_name'               => $request->vendor_name,
            'version'                   => $request->version ?: 'v1.0',
            'status'                    => 'Under Review',
            'verification_status'       => 'Pending',
            'effective_date'            => $request->effective_date,
            'expiry_date'               => $request->expiry_date,
            'value'                     => $request->value ?: 0,
            'physical_archive_location' => $request->physical_archive_location,
            'notes'                     => $request->notes,
            'created_by'                => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil didaftarkan ke Central Register.');
    }

    /**
     * Verify & Approve Document
     */
    public function documentsVerify(Request $request, $id)
    {
        $doc = AdminDocument::findOrFail($id);
        $doc->update([
            'status'              => 'Verified / Complete',
            'verification_status' => 'Approved',
            'rejection_notes'     => null,
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
        ]);

        // If linked to a checklist item, mark as verified
        AdminDocumentChecklist::where('admin_document_id', $doc->id)->update([
            'is_verified' => true,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', "Dokumen {$doc->doc_number} telah diverifikasi & berstatus Complete.");
    }

    /**
     * Return Document / Request Clarification (Reject with Notes)
     */
    public function documentsRejectClarify(Request $request, $id)
    {
        $request->validate([
            'rejection_notes' => 'required|string',
        ]);

        $doc = AdminDocument::findOrFail($id);
        $doc->update([
            'status'              => 'Clarification Requested',
            'verification_status' => 'Clarification Requested',
            'rejection_notes'     => $request->rejection_notes,
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
        ]);

        return redirect()->back()->with('success', "Dokumen {$doc->doc_number} dikembalikan ke unit terkait dengan catatan klarifikasi.");
    }

    /**
     * Gatekeeper Checklist Matrix
     */
    public function checklistsIndex(Request $request)
    {
        $query = AdminDocumentChecklist::with(['project', 'document', 'verifier'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('milestone')) {
            $query->where('milestone', $request->milestone);
        }

        $checklists = $query->paginate(20)->withQueryString();
        $projects = Project::select('id', 'name')->latest()->get();

        return view('admin_support.checklists.index', compact('checklists', 'projects'));
    }

    /**
     * Update Checklist Item
     */
    public function checklistsUpdate(Request $request, $id)
    {
        $chk = AdminDocumentChecklist::findOrFail($id);
        $chk->update([
            'is_submitted' => $request->has('is_submitted'),
            'is_verified'  => $request->has('is_verified'),
            'notes'        => $request->notes,
            'verified_by'  => $request->has('is_verified') ? Auth::id() : null,
            'verified_at'  => $request->has('is_verified') ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Status checklist gatekeeper berhasil diperbarui.');
    }

    /**
     * Logistics, Surat Jalan & Delivery Orders
     */
    public function logisticsIndex(Request $request)
    {
        $query = AdminLogisticsDispatch::with(['project', 'items', 'creator'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('dispatch_number', 'like', "%{$s}%")
                  ->orWhere('recipient_name', 'like', "%{$s}%")
                  ->orWhere('client_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dispatches = $query->paginate(15)->withQueryString();
        $projects = Project::select('id', 'name')->latest()->get();
        $clients = Client::select('id', 'name')->latest()->get();

        return view('admin_support.logistics.index', compact('dispatches', 'projects', 'clients'));
    }

    /**
     * Store New Logistics Dispatch (Surat Jalan)
     */
    public function logisticsStore(Request $request)
    {
        $request->validate([
            'dispatch_date'       => 'required|date',
            'destination_address' => 'required|string',
            'recipient_name'      => 'required|string',
        ]);

        $year = date('Y');
        $count = AdminLogisticsDispatch::whereYear('created_at', $year)->count() + 1;
        $dispatchNumber = sprintf('SJ-%s-%04d', $year, $count);

        $dispatch = AdminLogisticsDispatch::create([
            'dispatch_number'     => $dispatchNumber,
            'project_id'          => $request->project_id ?: null,
            'client_id'           => $request->client_id ?: null,
            'client_name'         => $request->client_name,
            'dispatch_date'       => $request->dispatch_date,
            'courier_type'        => $request->courier_type ?: 'Internal Driver',
            'courier_name'        => $request->courier_name,
            'tracking_ref'        => $request->tracking_ref,
            'origin_warehouse'    => $request->origin_warehouse ?: 'HQ Warehouse Jakarta',
            'destination_address' => $request->destination_address,
            'recipient_name'      => $request->recipient_name,
            'recipient_phone'     => $request->recipient_phone,
            'status'              => 'Ready to Dispatch',
            'delivery_notes'      => $request->delivery_notes,
            'created_by'          => Auth::id(),
        ]);

        // Add items if provided
        if ($request->filled('item_name')) {
            AdminDispatchItem::create([
                'dispatch_id'    => $dispatch->id,
                'item_name'      => $request->item_name,
                'category'       => $request->item_category ?: 'Hardware',
                'quantity'       => $request->quantity ?: 1,
                'unit'           => $request->unit ?: 'Unit',
                'serial_numbers' => $request->serial_numbers,
                'condition'      => $request->condition ?: 'New / Segel',
                'notes'          => $request->item_notes,
            ]);
        }

        return redirect()->back()->with('success', "Surat Jalan {$dispatchNumber} berhasil diterbitkan.");
    }

    /**
     * Update Logistics Dispatch Status
     */
    public function logisticsUpdateStatus(Request $request, $id)
    {
        $dispatch = AdminLogisticsDispatch::findOrFail($id);
        $dispatch->update([
            'status'         => $request->status,
            'delivery_notes' => $request->delivery_notes ?: $dispatch->delivery_notes,
        ]);

        return redirect()->back()->with('success', "Status Surat Jalan {$dispatch->dispatch_number} diperbarui ke: {$request->status}");
    }

    /**
     * Master Serial Number Registry (SN Tracking)
     */
    public function inventoryIndex(Request $request)
    {
        $query = AdminSerialNumber::with(['project', 'client', 'dispatch'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('serial_number', 'like', "%{$s}%")
                  ->orWhere('product_name', 'like', "%{$s}%")
                  ->orWhere('brand', 'like', "%{$s}%")
                  ->orWhere('model', 'like', "%{$s}%")
                  ->orWhere('client_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $serialNumbers = $query->paginate(20)->withQueryString();
        $projects = Project::select('id', 'name')->latest()->get();
        $clients = Client::select('id', 'name')->latest()->get();

        return view('admin_support.inventory.index', compact('serialNumbers', 'projects', 'clients'));
    }

    /**
     * Store Serial Number
     */
    public function inventoryStoreSN(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|unique:admin_serial_numbers,serial_number',
            'product_name'  => 'required|string',
            'category'      => 'required|string',
        ]);

        AdminSerialNumber::create([
            'serial_number'    => strtoupper(trim($request->serial_number)),
            'product_name'     => $request->product_name,
            'brand'            => $request->brand,
            'model'            => $request->model,
            'category'         => $request->category,
            'current_status'   => $request->current_status ?: 'In Warehouse',
            'current_location' => $request->current_location ?: 'HQ Warehouse Jakarta',
            'project_id'       => $request->project_id ?: null,
            'client_id'        => $request->client_id ?: null,
            'client_name'      => $request->client_name,
            'warranty_expiry'  => $request->warranty_expiry,
            'notes'            => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Serial Number perangkat berhasil dicatat.');
    }

    /**
     * Operational Assets & Testing Equipment (OTDR, Splicer, Toolkit)
     */
    public function assetsIndex(Request $request)
    {
        $query = AdminEquipmentAsset::with('borrower')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('asset_code', 'like', "%{$s}%")
                  ->orWhere('asset_name', 'like', "%{$s}%")
                  ->orWhere('brand_model', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $assets = $query->paginate(15)->withQueryString();
        $engineers = User::all();

        return view('admin_support.assets.index', compact('assets', 'engineers'));
    }

    /**
     * Store Operational Equipment Asset
     */
    public function assetsStore(Request $request)
    {
        $request->validate([
            'asset_name' => 'required|string',
            'category'   => 'required|string',
        ]);

        $prefix = match($request->category) {
            'Testing Tool'   => 'OTDR',
            'Fusion Splicer' => 'SPL',
            'Power Meter'    => 'OPM',
            'Toolkit'        => 'TLS',
            default          => 'AST',
        };

        $count = AdminEquipmentAsset::count() + 1;
        $assetCode = sprintf('AST-%s-%03d', $prefix, $count);

        AdminEquipmentAsset::create([
            'asset_code'       => $request->asset_code ?: $assetCode,
            'asset_name'       => $request->asset_name,
            'category'         => $request->category,
            'brand_model'      => $request->brand_model,
            'serial_number'    => $request->serial_number,
            'condition'        => $request->condition ?: 'Good',
            'status'           => 'Available in HQ',
            'storage_location' => $request->storage_location ?: 'HQ Workshop / Rak Alat',
            'notes'            => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Aset alat kerja berhasil didaftarkan.');
    }

    /**
     * Borrow or Return Asset
     */
    public function assetsBorrowReturn(Request $request, $id)
    {
        $asset = AdminEquipmentAsset::findOrFail($id);

        if ($request->action == 'borrow') {
            $asset->update([
                'status'               => 'Borrowed by Engineer',
                'current_borrower_id'  => $request->borrower_id,
                'borrowed_at'          => now(),
                'expected_return_date' => $request->expected_return_date,
                'notes'                => $request->notes,
            ]);
            return redirect()->back()->with('success', "Aset {$asset->asset_code} berhasil dipinjamkan.");
        } else {
            $asset->update([
                'status'               => 'Available in HQ',
                'current_borrower_id'  => null,
                'borrowed_at'          => null,
                'expected_return_date' => null,
                'condition'            => $request->condition ?: $asset->condition,
            ]);
            return redirect()->back()->with('success', "Aset {$asset->asset_code} telah dikembalikan ke HQ.");
        }
    }

    /**
     * Project Handover Records & Repository Archive
     */
    public function handoversIndex(Request $request)
    {
        $query = AdminProjectHandover::with(['project', 'auditor'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('handover_number', 'like', "%{$s}%")
                  ->orWhereHas('project', function($q) use ($s) {
                      $q->where('name', 'like', "%{$s}%");
                  });
        }

        $handovers = $query->paginate(15)->withQueryString();
        $projects = Project::select('id', 'name')->latest()->get();

        return view('admin_support.handovers.index', compact('handovers', 'projects'));
    }

    /**
     * Store Project Handover
     */
    public function handoversStore(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'handover_date' => 'required|date',
        ]);

        $year = date('Y');
        $count = AdminProjectHandover::whereYear('created_at', $year)->count() + 1;
        $handoverNumber = sprintf('HND-%s-%04d', $year, $count);

        AdminProjectHandover::create([
            'handover_number'    => $handoverNumber,
            'project_id'         => $request->project_id,
            'target_division'    => $request->target_division ?: 'Client',
            'handover_date'      => $request->handover_date,
            'status'             => 'Audit Review',
            'completeness_score' => $request->completeness_score ?: 90,
            'auditor_id'         => Auth::id(),
            'audited_at'         => now(),
            'audit_notes'        => $request->audit_notes,
            'archive_box_code'   => $request->archive_box_code,
        ]);

        return redirect()->back()->with('success', "Berkas Serah Terima {$handoverNumber} berhasil dicatat.");
    }
}
