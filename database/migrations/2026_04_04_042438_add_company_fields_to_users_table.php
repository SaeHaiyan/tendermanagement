<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('company_name')->nullable();
        $table->string('grade')->nullable(); // e.g., G1, G7
        $table->text('services')->nullable(); // Service provided
        $table->integer('year_established')->nullable();
    });
}


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
