<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            // This links the tender to a user (the subcon)
            $table->foreignId('selected_subcon_id')->nullable()->constrained('users')->onDelete('set null');

            // We also need a way to track the work status
            $table->enum('work_status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->integer('progress_percent')->default(0);
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
