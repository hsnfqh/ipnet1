<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\SalesActivity;
use App\Models\User;
use Carbon\Carbon;

class SalesCrmActivitySeeder extends Seeder
{
    public function run(): void
    {
        $salesUser = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Sales', 'Account Manager', 'CRO']);
        })->first() ?? User::first();

        $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])->get();

        $stagesList = [
            'Qualification',
            'Qualified Opportunity',
            'Proposal Request',
            'Quotation',
            'Negotiation',
            'Approval',
            'Contract / PO / SPK',
            'Closed Won'
        ];

        $stageProbMap = [
            'Qualification'         => 10,
            'Qualified Opportunity' => 25,
            'Proposal Request'      => 50,
            'Quotation'             => 70,
            'Negotiation'           => 85,
            'Approval'              => 95,
            'Contract / PO / SPK'   => 98,
            'Closed Won'            => 100,
            'Closed Lost'           => 0,
        ];

        foreach ($projects as $index => $p) {
            // Distribute stages logically if not set
            $stage = $p->sales_stage;
            if (!$stage || $stage === 'Qualification') {
                if ($p->status === 'Completed' || $p->status === 'Closed Won') {
                    $stage = 'Closed Won';
                } elseif ($p->status === 'On Progress' || $p->status === 'In Progress') {
                    $stage = $index % 2 === 0 ? 'Contract / PO / SPK' : 'Approval';
                } elseif ($p->status === 'Pending') {
                    $stage = 'Negotiation';
                } else {
                    $stageIndex = $index % 5;
                    $stage = $stagesList[$stageIndex];
                }
            }

            $prob = $stageProbMap[$stage] ?? 25;
            $closingDate = $p->expected_closing_date ?? Carbon::now()->addDays(rand(10, 60));
            $quoNum = $p->quotation_number ?? ('QUO-IPNET/2026/09/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT));

            $p->update([
                'sales_stage'           => $stage,
                'win_probability'       => $prob,
                'expected_closing_date' => $closingDate,
                'quotation_number'      => $quoNum,
                'quotation_amount'      => $p->contract_value,
                'billing_terms'         => $p->billing_terms ?? 'DP 30% setelah PO, 50% delivery hardware, 20% setelah UAT & BAST selesai.',
                'commercial_terms'      => $p->commercial_terms ?? 'Franco Jakarta, Pembayaran TOP 30 Hari setelah invoice resmi.',
                'sla_commitment'        => $p->sla_commitment ?? 'Garansi Resmi Prinsipal 1 Tahun, Support SLA 8x5 Response Time 4 Jam.',
            ]);

            // Create 1-2 realistic CRM Activities for projects
            if (SalesActivity::where('project_id', $p->id)->count() === 0) {
                SalesActivity::create([
                    'project_id'       => $p->id,
                    'sales_id'         => $salesUser->id,
                    'activity_type'    => $index % 3 === 0 ? 'Meeting' : ($index % 3 === 1 ? 'Demo / Presentation' : 'Quotation Submission'),
                    'subject'          => "Klarifikasi Kebutuhan & Pembahasan Scope {$p->name}",
                    'activity_date'    => Carbon::now()->subDays(rand(1, 14)),
                    'notes'            => "Telah dilaksanakan pembahasan detail bersama user teknis dan procurement klien {$p->client}. User menyetujui estimasi BoQ dan SLA standar.",
                    'next_action'      => 'Follow-up jadwal presentasi proposal final & penerbitan SPK',
                    'next_action_date' => Carbon::now()->addDays(rand(3, 10)),
                    'status'           => 'Completed',
                ]);

                if ($stage === 'Negotiation' || $stage === 'Contract / PO / SPK' || $stage === 'Closed Won') {
                    SalesActivity::create([
                        'project_id'       => $p->id,
                        'sales_id'         => $salesUser->id,
                        'activity_type'    => 'Negotiation',
                        'subject'          => "Negosiasi Harga & Finalisasi Termin Pembayaran",
                        'activity_date'    => Carbon::now()->subDays(rand(1, 5)),
                        'notes'            => "Klarifikasi diskon volume dan kesepakatan termin pembayaran TOP 30 hari. User meminta draft kontrak dikirim.",
                        'next_action'      => 'Kirim berkas Commercial Handover ke Tim Delivery PMO',
                        'next_action_date' => Carbon::now()->addDays(2),
                        'status'           => 'Completed',
                    ]);
                }
            }
        }
    }
}
