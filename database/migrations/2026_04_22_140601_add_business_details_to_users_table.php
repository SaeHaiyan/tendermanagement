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
            $table->string('pic_name')->nullable(); // Person In Charge
            $table->string('phone_office')->nullable();
            $table->string('phone_pic')->nullable();
            $table->string('company_email')->nullable();
            $table->string('cidb_reg_number')->nullable();
            $table->string('ssm_number')->nullable();
            $table->string('company_level')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
