<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\Division;
use App\Models\ManagedServicePmCycle;
use App\Models\ManagedServiceTicket;
use App\Models\ProjectDocument;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EndToEndFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $salesUser;
    protected User $msUser;
    protected User $leadUser;
    protected User $pmoUser;
    protected User $engineer1;
    protected User $engineer2;
    protected Division $techDivision;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // 1. Buat Roles
        $roles = [
            'Admin',
            'Super Admin',
            'Sales',
            'Managed Service',
            'Lead Maintenance',
            'Lead Engineer',
            'PMO',
            'Project Manager',
            'Field Support',
            'Network Engineer',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Buat Divisi
        $this->techDivision = Division::firstOrCreate(
            ['name' => 'Divisi Field Engineering'],
            ['description' => 'Divisi penanganan operasional teknis lapangan']
        );

        // 3. Buat User Aktor untuk Seluruh Role
        $this->salesUser = User::factory()->create([
            'name'     => 'Hendry Wibowo',
            'email'    => 'hendry.sales@ipnetsolusindo.com',
            'position' => 'Account Manager',
        ]);
        $this->salesUser->assignRole('Sales');

        $this->msUser = User::factory()->create([
            'name'     => 'Doris',
            'email'    => 'doris.ms@ipnetsolusindo.com',
            'position' => 'Managed Service Coordinator',
        ]);
        $this->msUser->assignRole('Managed Service');

        $this->leadUser = User::factory()->create([
            'name'        => 'Nugraha Pratama',
            'email'       => 'nugraha.lead@ipnetsolusindo.com',
            'position'    => 'Team Leader Network Engineering',
            'division_id' => $this->techDivision->id,
        ]);
        $this->leadUser->assignRole('Lead Engineer');

        $this->pmoUser = User::factory()->create([
            'name'     => 'Rizki',
            'email'    => 'rizki.pmo@ipnetsolusindo.com',
            'position' => 'Project Manager',
        ]);
        $this->pmoUser->assignRole('PMO');
        $this->pmoUser->assignRole('Project Manager');

        $this->engineer1 = User::factory()->create([
            'name'        => 'Mario',
            'email'       => 'mario.eng@ipnetsolusindo.com',
            'position'    => 'Field Support Engineer',
            'division_id' => $this->techDivision->id,
        ]);
        $this->engineer1->assignRole('Field Support');

        $this->engineer2 = User::factory()->create([
            'name'        => 'Raihan Ghiffary',
            'email'       => 'raihan.eng@ipnetsolusindo.com',
            'position'    => 'Field Network Engineer',
            'division_id' => $this->techDivision->id,
        ]);
        $this->engineer2->assignRole('Network Engineer');
    }

    /**
     * Test 1: Flow Commercial Handover oleh Sales ke Managed Service
     */
    public function test_sales_commercial_handover_flow(): void
    {
        $project = Project::create([
            'name'           => 'Pemeliharaan Jaringan Core & Security PT Bank Central Asia Tbk',
            'client'         => 'PT Bank Central Asia Tbk',
            'location'       => 'Menara BCA Lt. 18, Jakarta',
            'start_date'     => now()->toDateString(),
            'deadline'       => now()->addYear()->toDateString(),
            'contract_value' => 360000000,
            'status'         => 'In Progress',
            'sales_stage'    => 'Closed Won',
            'created_by'     => $this->salesUser->id,
        ]);

        // 1. Akses halaman menu handover oleh Sales
        $response = $this->actingAs($this->salesUser)->get(route('sales.handover.index'));
        $response->assertStatus(200);

        // 2. Submit Handover Komersial resmi
        $postData = [
            'handover_target'       => 'managed_service',
            'po_spk_number'         => 'SPK/BCA/NET/2026/088',
            'po_spk_date'           => now()->toDateString(),
            'contract_value'        => 360000000,
            'billing_terms'         => 'Termin Quarterly / per 3 bulan',
            'sla_tier'              => 'Gold',
            'sla_coverage_hours'    => '24x7',
            'maintenance_frequency' => 'Quarterly',
            'service_start_date'    => now()->toDateString(),
            'service_end_date'      => now()->addYear()->toDateString(),
            'po_spk_file'           => UploadedFile::fake()->create('spk_resmi.pdf', 500, 'application/pdf'),
        ];

        $submitResponse = $this->actingAs($this->salesUser)
            ->post(route('sales.handover.submit', $project->id), $postData);

        $submitResponse->assertRedirect();
        
        $project->refresh();
        $this->assertEquals('managed_service', $project->handover_target);
        $this->assertEquals('Submitted', $project->ms_handover_status);

        // 3. Akses Berita Acara Serah Terima (BAST Internal)
        $bastResponse = $this->actingAs($this->salesUser)->get(route('sales.handover.document', $project->id));
        $bastResponse->assertStatus(200);
        $bastResponse->assertSee('BERITA ACARA SERAH TERIMA PROYEK');
    }

    /**
     * Test 2: Flow Penerimaan Handover oleh Tim Managed Service & Kelengkapan 6 Berkas
     */
    public function test_managed_service_acceptance_and_documents_completeness(): void
    {
        $project = Project::create([
            'name'                   => 'Managed Service SLA BCA Thamrin 2026',
            'client'                 => 'PT Bank Central Asia Tbk',
            'location'               => 'Menara BCA Lt. 18, Jakarta',
            'start_date'             => now()->toDateString(),
            'deadline'               => now()->addYear()->toDateString(),
            'contract_value'         => 360000000,
            'status'                 => 'In Progress',
            'sales_stage'            => 'Closed Won',
            'handover_target'        => 'managed_service',
            'handover_status'        => 'Submitted',
            'po_spk_file'            => 'project_documents/sample_sc_contract.pdf',
            'contract_file'          => 'project_documents/sample_sc_contract.pdf',
            'boq_file'               => 'project_documents/sample_technical_boq.pdf',
            'tor_file'               => 'project_documents/sample_sla_managed_service.pdf',
            'bast_file'              => 'project_documents/sample_installation_report.pdf',
            'proposal_file'          => 'project_documents/sample_technical_boq.pdf',
            'po_spk_number'          => 'SPK/BCA/NET/2026/088',
            'po_number'              => 'PO-BCA-2026-9921',
            'tor_number'             => 'TOR-BCA-SLA-2026',
            'created_by'             => $this->salesUser->id,
            'commercial_handover_by' => $this->salesUser->id,
            'commercial_handover_at' => now(),
        ]);

        // 1. Dashboard & Projects MS
        $dashRes = $this->actingAs($this->msUser)->get(route('ms.dashboard'));
        $dashRes->assertStatus(200);

        $projListRes = $this->actingAs($this->msUser)->get(route('ms.projects.index'));
        $projListRes->assertStatus(200);

        // 2. Terima Proyek (Accept Handover Checklist)
        $acceptData = [
            'so_code'                  => 'SO-2026-MS-BCA99',
            'handover_status_selected' => 'approved',
            'handover_confirmation'    => 'Berkas lengkap dan verified.',
            'checklist_ba'             => '1',
            'checklist_spk'            => '1',
            'checklist_contract'       => '1',
            'checklist_boq'            => '1',
            'checklist_tor'            => '1',
            'checklist_technical'      => '1',
            'sla_tier'                 => 'Gold',
            'maintenance_frequency'    => 'Quarterly',
            'pm_frequency_type'        => 'per_3_bulan',
            'pm_total_cycles'          => 4,
            'cm_quota'                 => 12,
        ];

        $acceptRes = $this->actingAs($this->msUser)
            ->post(route('ms.handovers.accept', $project->id), $acceptData);

        $acceptRes->assertRedirect();

        $project->refresh();
        $this->assertEquals('Accepted', $project->ms_handover_status);
        $this->assertEquals('Operate', $project->stage);
        $this->assertEquals('Active', $project->status);

        // 3. Buka Detail Proyek MS dan pastikan 6 dokumen TERSEDIA (lengkap)
        $detailRes = $this->actingAs($this->msUser)->get(route('ms.projects.show', $project->id));
        $detailRes->assertStatus(200);
        $detailRes->assertSee('Tersedia');
        $detailRes->assertDontSee('Menunggu dari Sales');
    }

    /**
     * Test 3: Flow Siklus PM & Tiket CM Tersinkronisasi Otomatis ke Tabel Tasks
     */
    public function test_pm_cycle_and_cm_ticket_task_sync(): void
    {
        $project = Project::create([
            'name'               => 'Maintenance SLA Datacenter IPNet',
            'client'             => 'PT Mitra Solusindo',
            'location'           => 'Jakarta Pusat',
            'start_date'         => now()->toDateString(),
            'deadline'           => now()->addYear()->toDateString(),
            'status'             => 'Active',
            'stage'              => 'Operate',
            'handover_target'    => 'managed_service',
            'ms_handover_status' => 'Accepted',
            'pm_total_cycles'    => 4,
            'created_by'         => $this->msUser->id,
        ]);

        \App\Http\Controllers\ManagedServiceController::initPmCycles($project);

        $firstCycle = ManagedServicePmCycle::where('project_id', $project->id)->first();
        $this->assertNotNull($firstCycle);

        // 1. Tim Maintenance menentukan tanggal kunjungan PM
        $scheduledDate = now()->addDays(3)->toDateString();
        $pmUpdateRes = $this->actingAs($this->msUser)->post(route('ms.monitoring.pm.update', $firstCycle->id), [
            'scheduled_date'       => $scheduledDate,
            'activity_status'      => 'Scheduled',
            'assigned_engineer_id' => $this->engineer1->id,
            'report_status'        => 'Pending',
            'billing_ba_status'    => 'Pending',
            'activity_notes'       => 'Pemeriksaan berkala Q1 core switch dan cabling.',
        ]);

        $pmUpdateRes->assertRedirect();

        // Verifikasi Schedule dan Task tercipta otomatis
        $schedule = Schedule::where('project_id', $project->id)->first();
        $this->assertNotNull($schedule);
        $this->assertEquals($scheduledDate, $schedule->date->toDateString());

        $task = Task::where('project_id', $project->id)
            ->where('title', 'like', "%{$firstCycle->cycle_label}%")
            ->first();
        $this->assertNotNull($task);
        $this->assertEquals($this->engineer1->id, $task->engineer_id);

        // 2. Catat Tiket CM Baru
        $cmRes = $this->actingAs($this->msUser)->post(route('ms.tickets.store'), [
            'client_name'   => $project->client,
            'project_id'    => $project->id,
            'title'         => 'Port SFP Flapping di Rack C2',
            'description'   => 'Kabel patchcord SFP+ mengalami link down berkala.',
            'priority'      => 'High',
            'status'        => 'In Progress',
            'assigned_to'   => $this->engineer2->id,
            'incident_date' => now()->toDateString(),
        ]);

        $cmRes->assertRedirect();

        $cmTicket = ManagedServiceTicket::where('project_id', $project->id)->first();
        $this->assertNotNull($cmTicket);

        $cmTask = Task::where('title', 'like', "%{$cmTicket->ticket_number}%")->first();
        $this->assertNotNull($cmTask);
        $this->assertEquals($this->engineer2->id, $cmTask->engineer_id);
    }

    /**
     * Test 4: Flow Lead Engineer Melihat Proyek Aktif & Menugaskan Personil Lapangan
     */
    public function test_lead_engineer_dashboard_and_assignment_flow(): void
    {
        $project = Project::create([
            'name'               => 'SLA Network Security BCA Thamrin',
            'client'             => 'PT Bank Central Asia Tbk',
            'location'           => 'Jakarta Pusat',
            'start_date'         => now()->toDateString(),
            'deadline'           => now()->addYear()->toDateString(),
            'status'             => 'Active',
            'stage'              => 'Operate',
            'handover_target'    => 'managed_service',
            'ms_handover_status' => 'Accepted',
            'division_id'        => $this->techDivision->id,
            'created_by'         => $this->salesUser->id,
        ]);

        // 1. Lead Engineer membuka Dashboard Lead
        $leadDash = $this->actingAs($this->leadUser)->get(route('dashboard.lead'));
        $leadDash->assertStatus(200);
        $leadDash->assertSee($project->name);

        // 2. Lead Engineer mendelegasikan teknisi lapangan (Mario & Raihan)
        $assignRes = $this->actingAs($this->leadUser)->post(route('pmo.projects.assign_team', $project->id), [
            'engineer_ids' => [$this->engineer1->id, $this->engineer2->id],
            'notes'        => 'Mario ditugaskan sebagai PIC utama on-site, Raihan backup routing.',
            'roles'        => [
                $this->engineer1->id => 'PIC Lapangan Utama',
                $this->engineer2->id => 'Teknisi Lapangan',
            ],
        ]);

        $assignRes->assertRedirect();

        $project->refresh();
        $assigned = $project->handover_data['assigned_engineers'] ?? [];
        $this->assertCount(2, $assigned);
        $this->assertEquals('Mario', $assigned[0]['name']);
        $this->assertEquals('Raihan Ghiffary', $assigned[1]['name']);
    }

    /**
     * Test 5: Flow Pembatasan Hak Update Progres (Hanya PM & Maintenance)
     */
    public function test_progress_update_permissions_enforcement(): void
    {
        $project = Project::create([
            'name'               => 'Implementasi Core Router Denpasar',
            'client'             => 'PT Angkasa Pura Indonesia',
            'location'           => 'Bandara Ngurah Rai, Bali',
            'start_date'         => now()->toDateString(),
            'deadline'           => now()->addMonth()->toDateString(),
            'status'             => 'In Progress',
            'stage'              => 'Deliver',
            'handover_target'    => 'pmo',
            'pm_id'              => $this->pmoUser->id,
            'created_by'         => $this->pmoUser->id,
        ]);

        $task = Task::create([
            'title'       => 'Staging & Konfigurasi VLAN Switch',
            'project_id'  => $project->id,
            'engineer_id' => $this->engineer1->id,
            'priority'    => 'High',
            'status'      => 'In Progress',
            'deadline'    => now()->addDays(5)->toDateString(),
            'progress'    => 30,
            'created_by'  => $this->pmoUser->id,
        ]);

        // 1. Teknisi Lapangan (Mario) mencoba mengupdate progress skala ke 100% dan status Completed
        $engUpdateRes = $this->actingAs($this->engineer1)->put(route('tasks.update', $task->id), [
            'progress'    => 100,
            'status'      => 'Completed',
            'description' => 'Konfigurasi VLAN 10, 20, 30 selesai diverifikasi.',
        ]);

        $engUpdateRes->assertRedirect();

        $task->refresh();
        // Server-side guard: Progres TIDAK BERUBAH ke 100% (tetap 30%)
        // Status Completed diubah ke Waiting Review (menunggu verifikasi PM)
        $this->assertEquals(30, $task->progress);
        $this->assertEquals('Waiting Review', $task->status);
        $this->assertEquals('Konfigurasi VLAN 10, 20, 30 selesai diverifikasi.', $task->description);

        // 2. Project Manager (Rizki) memperbarui skala progres ke 100% dan mengesahkan status Completed
        $pmUpdateRes = $this->actingAs($this->pmoUser)->put(route('tasks.update', $task->id), [
            'progress'    => 100,
            'status'      => 'Completed',
            'description' => 'Hasil verifikasi PM: Konfigurasi valid dan selesai.',
        ]);

        $pmUpdateRes->assertRedirect();

        $task->refresh();
        $this->assertEquals(100, $task->progress);
        $this->assertEquals('Completed', $task->status);
    }

    /**
     * Test 6: Flow Dashboard PMO & Summary Progress Indicator (Hijau / Kuning / Merah)
     */
    public function test_pmo_dashboard_and_summary_progress_indicator(): void
    {
        $project = Project::create([
            'name'           => 'Deployment Server Bandara Denpasar',
            'client'         => 'PT Angkasa Pura Indonesia',
            'location'       => 'Bandara Ngurah Rai, Bali',
            'contract_value' => 500000000,
            'status'         => 'In Progress',
            'stage'          => 'Deliver',
            'handover_target'=> 'pmo',
            'pm_id'          => $this->pmoUser->id,
            'start_date'     => now()->subWeeks(2)->toDateString(),
            'deadline'       => now()->addWeeks(6)->toDateString(),
            'progress'       => 45,
            'created_by'     => $this->pmoUser->id,
            'handover_data'  => [
                'assigned_engineers' => [
                    [
                        'id'       => $this->engineer1->id,
                        'name'     => $this->engineer1->name,
                        'position' => $this->engineer1->position,
                        'role'     => 'Staging Lead',
                    ]
                ]
            ],
        ]);

        // Buat task selesai dan task berjalan tepat waktu
        Task::create([
            'title'       => 'Pemasangan Rack & Server',
            'project_id'  => $project->id,
            'engineer_id' => $this->engineer1->id,
            'priority'    => 'High',
            'status'      => 'Completed',
            'deadline'    => now()->subDays(2)->toDateString(),
            'progress'    => 100,
            'created_by'  => $this->pmoUser->id,
        ]);

        Task::create([
            'title'       => 'Pengkabelan Fiber Optic Antar Lantai',
            'project_id'  => $project->id,
            'engineer_id' => $this->engineer1->id,
            'priority'    => 'Medium',
            'status'      => 'In Progress',
            'deadline'    => now()->addDays(5)->toDateString(),
            'progress'    => 50,
            'created_by'  => $this->pmoUser->id,
        ]);

        $pmoDashRes = $this->actingAs($this->pmoUser)->get(route('pmo.projects.show', $project->id));
        $pmoDashRes->assertStatus(200);
        $pmoDashRes->assertSee('Summary Progress Proyek');
        $pmoDashRes->assertSee('Kesehatan Task Mingguan');
        $pmoDashRes->assertSee('DISTRIBUSI TIM');
    }

    public function test_pm_can_create_update_and_delete_wbs_tasks(): void
    {
        $project = Project::create([
            'name'           => 'Implementasi Network Test Project',
            'client'         => 'PT Test Client',
            'location'       => 'Bandara IGNR Denpasar, Bali',
            'sales_name'     => $this->salesUser->name,
            'created_by'     => $this->salesUser->id,
            'pm_id'          => $this->pmoUser->id,
            'division_id'    => $this->techDivision->id,
            'stage'          => 'Deliver',
            'status'         => 'In Progress',
            'process_status' => 'In Progress',
            'sales_stage'    => 'Closed Won',
            'start_date'     => now()->toDateString(),
            'deadline'       => now()->addDays(56)->toDateString(),
            'progress'       => 0,
        ]);

        // 1. PM can store task
        $storeRes = $this->actingAs($this->pmoUser)->post(route('pmo.projects.tasks.store', $project->id), [
            'title'         => 'Stagging Switch Baru',
            'duration_days' => 15,
            'start_date'    => now()->toDateString(),
            'deadline'      => now()->addDays(14)->toDateString(),
            'bobot'         => 15.46,
            'progress'      => 50,
            'status'        => 'In Progress',
            'priority'      => 'High',
            'engineer_id'   => $this->engineer1->id,
            'description'   => 'Staging rincian switch di posko',
        ]);
        $storeRes->assertSessionHas('success');

        $task = Task::where('project_id', $project->id)->where('title', 'Stagging Switch Baru')->first();
        $this->assertNotNull($task);
        $this->assertEquals(50, $task->progress);
        $this->assertEquals('In Progress', $task->status);

        // 2. PM can update task
        $updateRes = $this->actingAs($this->pmoUser)->post(route('pmo.tasks.update', $task->id), [
            'title'         => 'Stagging Switch Selesai Diperbarui',
            'duration_days' => 15,
            'start_date'    => now()->toDateString(),
            'deadline'      => now()->addDays(14)->toDateString(),
            'bobot'         => 15.46,
            'progress'      => 100,
            'status'        => 'Completed',
            'priority'      => 'High',
            'engineer_id'   => $this->engineer2->id,
            'description'   => 'Selesai 100%',
        ]);
        $updateRes->assertSessionHas('success');

        $task->refresh();
        $this->assertEquals('Stagging Switch Selesai Diperbarui', $task->title);
        $this->assertEquals(100, $task->progress);
        $this->assertEquals('Completed', $task->status);
        $this->assertEquals($this->engineer2->id, $task->engineer_id);

        // 3. PM can delete task
        $deleteRes = $this->actingAs($this->pmoUser)->post(route('pmo.tasks.destroy_post', $task->id));
        $deleteRes->assertSessionHas('success');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
