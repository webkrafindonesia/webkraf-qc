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
        Schema::create('test_case_scenarios', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->integer('test_case_id_id')->unsigned()->nullable();
            $table->string('scenario_name');
            $table->longText('scenario_steps');
            $table->text('expected_result')->nullable();
            $table->text('actual_result')->nullable();
            $table->enum('status', ['Passed', 'Failed', 'Remark'])->nullable();
            $table->longText('remarks')->nullable();
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
        Schema::dropIfExists('test_case_scenarios');
    }
};
