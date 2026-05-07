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
        Schema::table('tenders', function (Blueprint $table) {
            $table->string('tender_ref_number')->unique()->after('id');
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->string('site_location')->nullable();
            $table->dateTime('site_visit_date')->nullable();
            $table->integer('years_experience_required')->default(0);
            $table->string('priority_level')->default('normal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            //
        });
    }
};
