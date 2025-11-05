<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // In database/migrations/xxxx_xx_xx_xxxxxx_create_companies_table.php

public function up(): void
{
    Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // اسم الشركة يجب أن يكون فريداً
        $table->timestamps();
        $table->softDeletes(); // لإضافة الحذف الناعم
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
