<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Project;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bersihkan customer_pic_business & customer_pic_finance yang tercampur nama PIC orang
        $projects = Project::all();
        foreach ($projects as $p) {
            $hd = is_array($p->handover_data) ? $p->handover_data : [];
            $dirty = false;

            // Jika business contact bukan nomor telpon / berisi huruf, bersihkan atau ambil dari customer_pic_phone
            if (!empty($p->customer_pic_business) && preg_match('/[a-zA-Z]/', $p->customer_pic_business)) {
                $realPhone = $hd['customer_pic_phone'] ?? null;
                if ($realPhone && !preg_match('/[a-zA-Z]/', $realPhone)) {
                    $p->customer_pic_business = $realPhone;
                } else {
                    $p->customer_pic_business = null;
                }
                $dirty = true;
            }

            // Jika finance contact bukan email yang valid, bersihkan atau ambil dari customer_pic_email
            if (!empty($p->customer_pic_finance) && (!str_contains($p->customer_pic_finance, '@') || str_contains($p->customer_pic_finance, ' '))) {
                $realEmail = $hd['customer_pic_email'] ?? null;
                if ($realEmail && str_contains($realEmail, '@') && !str_contains($realEmail, ' ')) {
                    $p->customer_pic_finance = $realEmail;
                } else {
                    $p->customer_pic_finance = null;
                }
                $dirty = true;
            }

            if ($dirty) {
                $p->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
