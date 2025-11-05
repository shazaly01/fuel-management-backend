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
    Schema::create('trucks', function (Blueprint $table) {
        $table->id();
        $table->string('truck_number')->unique(); // رقم الشاحنة، يجب أن يكون فريداً
        $table->string('truck_type')->nullable(); // نوع الشاحنة (e.g., ايفيكو)
        $table->string('color')->nullable();
        $table->string('trailer_number')->nullable()->unique(); // رقم المقطورة، اختياري وفريد إذا وجد

        // يمكن ربط الشاحنة مباشرة بسائق، وقد يكون السائق غير معين
        $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');

        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
