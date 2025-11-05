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
    Schema::create('fuel_orders', function (Blueprint $table) {
        $table->id();

        // الربط مع الجداول الأخرى
        $table->foreignId('driver_id')->constrained('drivers');
        $table->foreignId('station_id')->constrained('stations');
        $table->foreignId('product_id')->constrained('products');
        $table->foreignId('order_status_id')->constrained('order_statuses');

        // معلومات إضافية عن الطلبية
        $table->decimal('quantity', 10, 2)->nullable(); // الكمية، مثال: 1000.50 لتر
        $table->date('order_date'); // تاريخ إنشاء الطلبية
        $table->timestamp('delivery_date')->nullable(); // تاريخ التسليم الفعلي
        $table->text('notes')->nullable(); // أي ملاحظات إضافية

        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_orders');
    }
};
