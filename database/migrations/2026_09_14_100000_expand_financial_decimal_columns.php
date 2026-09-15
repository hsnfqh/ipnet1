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
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'contract_value')) {
                $table->decimal('contract_value', 20, 2)->nullable()->change();
            }
            if (Schema::hasColumn('projects', 'quotation_amount')) {
                $table->decimal('quotation_amount', 20, 2)->nullable()->change();
            }
        });

        if (Schema::hasTable('market_intelligences')) {
            Schema::table('market_intelligences', function (Blueprint $table) {
                if (Schema::hasColumn('market_intelligences', 'potential_value')) {
                    $table->decimal('potential_value', 20, 2)->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'contract_value')) {
                $table->decimal('contract_value', 15, 2)->nullable()->change();
            }
            if (Schema::hasColumn('projects', 'quotation_amount')) {
                $table->decimal('quotation_amount', 15, 2)->nullable()->change();
            }
        });

        if (Schema::hasTable('market_intelligences')) {
            Schema::table('market_intelligences', function (Blueprint $table) {
                if (Schema::hasColumn('market_intelligences', 'potential_value')) {
                    $table->decimal('potential_value', 15, 2)->nullable()->change();
                }
            });
        }
    }
};
