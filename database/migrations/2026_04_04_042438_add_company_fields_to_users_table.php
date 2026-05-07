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
        $table->text('company_address')->nullable();
        $table->string('pic_name')->nullable(); // Person In Charge
        $table->string('pic_phone')->nullable();
        $table->string('office_phone')->nullable();
        $table->string('company_email')->nullable();
        $table->string('cidb_reg_number')->nullable();
        $table->string('ssm_number')->nullable();
        $table->string('company_level')->nullable();
        $table->year('year_established')->nullable();
        $table->json('cidb_grades')->nullable(); // To store multiple (G1, G2, etc.)
        $table->text('services_provided')->nullable();
    });
}


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
