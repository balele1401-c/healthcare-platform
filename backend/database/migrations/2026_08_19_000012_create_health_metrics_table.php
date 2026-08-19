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
        Schema::create('health_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('metric_type', 30)->index();
            $table->decimal('value', 8, 2);
            $table->decimal('secondary_value', 8, 2)->nullable();
            $table->string('unit', 20);
            $table->timestamp('measured_at')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'metric_type', 'measured_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_metrics');
    }
};
