<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tenders', function (Blueprint $table) {
            // 1. Change work_status to a flexible string (fixes the 'truncated' error)
            $table->string('work_status')->default('pending')->change();

            // 2. Change report_path to TEXT (allows long lists of multiple PDF paths)
            $table->text('report_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
