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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('subscription_status')->default('inactive')->after('status');
            $table->string('subscription_type')->default('free')->after('subscription_status');
            $table->timestamp('subscription_expires_at')->nullable()->default(null)->after('subscription_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['subscription_status', 'subscription_type', 'subscription_expires_at']);
        });
    }
};
