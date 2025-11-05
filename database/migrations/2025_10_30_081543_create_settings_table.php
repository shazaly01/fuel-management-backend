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
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique(); // المفتاح، مثل: 'max_pending_orders_per_driver'
        $table->string('value'); // القيمة، مثل: '4'
        $table->string('type')->default('text'); // نوع الحقل (text, number, boolean) للمساعدة في عرضه بالواجهة لاحقاً
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
