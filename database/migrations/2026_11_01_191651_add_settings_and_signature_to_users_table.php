<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'signature_data')) {
                $table->longText('signature_data')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'signature_type')) {
                $table->string('signature_type')->default('signature_qr')->after('signature_data');
            }
            if (!Schema::hasColumn('users', 'signature_qr_token')) {
                $table->string('signature_qr_token', 64)->nullable()->unique()->after('signature_type');
            }
            if (!Schema::hasColumn('users', 'signature_updated_at')) {
                $table->timestamp('signature_updated_at')->nullable()->after('signature_qr_token');
            }
            if (!Schema::hasColumn('users', 'notification_web')) {
                $table->boolean('notification_web')->default(true)->after('signature_updated_at');
            }
            if (!Schema::hasColumn('users', 'notification_email')) {
                $table->boolean('notification_email')->default(false)->after('notification_web');
            }
            if (!Schema::hasColumn('users', 'theme_preference')) {
                $table->string('theme_preference')->default('system')->after('notification_email');
            }
            if (!Schema::hasColumn('users', 'timezone_preference')) {
                $table->string('timezone_preference')->default('Asia/Jakarta')->after('theme_preference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [
                'signature_data',
                'signature_type',
                'signature_qr_token',
                'signature_updated_at',
                'notification_web',
                'notification_email',
                'theme_preference',
                'timezone_preference',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
