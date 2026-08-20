<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations safely without modifying or dropping existing tables/data.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'provider')) {
                $table->string('provider', 50)->default('sandbox')->after('status')->index();
            }

            if (! Schema::hasColumn('payments', 'provider_payment_id')) {
                $table->string('provider_payment_id', 100)->nullable()->after('provider_reference')->index();
            }

            if (! Schema::hasColumn('payments', 'checkout_url')) {
                $table->text('checkout_url')->nullable()->after('provider_payment_id');
            }

            if (! Schema::hasColumn('payments', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->nullable()->after('checkout_url')->index();
            }

            if (! Schema::hasColumn('payments', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('expired_at');
            }

            if (! Schema::hasColumn('payments', 'refund_reason')) {
                $table->string('refund_reason', 255)->nullable()->after('refunded_at');
            }

            if (! Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable()->after('refund_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = [
                'user_id',
                'provider',
                'provider_payment_id',
                'checkout_url',
                'idempotency_key',
                'expired_at',
                'refunded_at',
                'refund_reason',
                'metadata',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
