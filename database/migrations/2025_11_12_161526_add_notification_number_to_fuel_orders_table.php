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
        Schema::table('fuel_orders', function (Blueprint $table) {
            // إضافة العمود الجديد بعد عمود 'notes'
            // يمكن أن يكون نصًا ويقبل القيم الفارغة (nullable)
            $table->string('notification_number')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_orders', function (Blueprint $table) {
            // تعريف كيفية التراجع عن التغيير (حذف العمود)
            $table->dropColumn('notification_number');
        });
    }
};
