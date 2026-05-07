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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selected_subcon_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('title');
            $table->string('tender_ref_number')->unique();
            $table->text('description');

            $table->string('required_grade')->nullable(); // e.g., G1, G7
            $table->text('required_services')->nullable();
            $table->integer('years_experience_required')->default(0);

            $table->decimal('estimated_budget', 15)->nullable();
            $table->string('site_location')->nullable();
            $table->dateTime('site_visit_date')->nullable();
            $table->date('deadline');

            $table->string('priority_level')->default('medium'); // low, medium, high
            $table->string('status')->default('open'); // open, closed, awarded
            $table->string('work_status')->default('not_started'); // in_progress, completed
            $table->integer('progress_percent')->default(0);
            $table->json('report_path')->nullable(); // Store multiple file paths

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
