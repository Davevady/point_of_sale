<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tax_type')->default('percent')->after('tax_amount');
            $table->decimal('tax_value', 15, 2)->default(0)->after('tax_type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tax_type', 'tax_value']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable(false)->change();
        });
    }
};
