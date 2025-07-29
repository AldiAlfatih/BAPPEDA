<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeTahun extends Model
{
    use HasFactory;

    protected $table = 'periode_tahun';

    protected $fillable = [
        'status',
        'tahun',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    public function periode()
    {
        return $this->hasMany(Periode::class, 'tahun_id');
    }

    /**
     * Get tahun aktif saat ini
     */
    public static function getTahunAktif()
    {
        $tahunAktif = self::where('status', 1)->first();

        // Jika tidak ada tahun aktif, ambil tahun terbaru
        if (!$tahunAktif) {
            $tahunAktif = self::orderByDesc('tahun')->first();

            // Jika ada tahun terbaru, set sebagai aktif
            if ($tahunAktif) {
                self::query()->update(['status' => 0]); // Reset semua
                $tahunAktif->update(['status' => 1]); // Set yang terbaru sebagai aktif
            }
        }

        return $tahunAktif;
    }

    /**
     * Set tahun sebagai aktif dan reset tahun lainnya
     */
    public function setAsActive()
    {
        // Reset semua tahun menjadi tidak aktif
        self::query()->update(['status' => 0]);

        // Set tahun ini sebagai aktif
        $this->update(['status' => 1]);

        return $this;
    }

    /**
     * Scope untuk tahun aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Check apakah tahun ini aktif
     */
    public function isActive()
    {
        return $this->status == 1;
    }
}
