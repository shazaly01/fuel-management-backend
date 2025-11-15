<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- إضافة استيراد
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'license_number',
        'phone_number',
        'status',
        // --- بداية الإضافة ---
        'address',
        'work_nature_id',
        'document_image_path',
        // --- نهاية الإضافة ---

    ];


     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['document_image_url']; // <-- 2. إضافة الحقل الجديد هنا

    // --- بداية الإضافة: Accessor لرابط الصورة ---

    /**
     * Get the full URL for the driver's document image.
     *
     * @return string|null
     */
    public function getDocumentImageUrlAttribute(): ?string
    {
        // 3. التحقق مما إذا كان المسار موجودًا في قاعدة البيانات
        if ($this->document_image_path) {
            // 4. بناء الرابط الكامل باستخدام Storage facade
            // الدالة url() ستقوم تلقائيًا بإضافة النطاق والمنفذ الصحيح من ملف .env (APP_URL)
            return Storage::disk('public')->url($this->document_image_path);
        }

        // 5. إرجاع null إذا لم تكن هناك صورة
        return null;
    }
    /**
     * Get the truck associated with the driver.
     */
    public function truck(): HasOne
    {
        return $this->hasOne(Truck::class);
    }

    /**
     * Get the fuel orders for the driver.
     */
    public function fuelOrders(): HasMany
    {
        return $this->hasMany(FuelOrder::class);
    }

    /**
     * Get the work nature for the driver.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workNature(): BelongsTo // <-- إضافة العلاقة الجديدة
    {
        return $this->belongsTo(WorkNature::class);
    }
}
