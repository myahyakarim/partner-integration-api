<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->index();
            $table->string('partner_code');
            $table->string('idempotency_key')->unique();
            $table->json('items');
            $table->string('fulfill_at');
            $table->string('status');
            $table->string('partner_ticket_id')->nullable()->unique();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_orders');
    }
};
