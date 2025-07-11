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
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->string('system_name')->nullable();
            $table->string('system_version')->nullable();
            $table->string('company')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['SIT', 'UAT'])->default('SIT');
            $table->enum('status', ['Draft', 'In Progress', 'Completed', 'Archived'])->default('Draft');
            $table->string('tester_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('platform_version')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};
