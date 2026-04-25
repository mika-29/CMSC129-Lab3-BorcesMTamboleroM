<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Inventory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'minimum_stock',
        'expiration_date',
        'category'
    ];

    // LOW STOCK
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->minimum_stock;
    }

    // EXPIRY STATUS
    public function getExpiryStatusAttribute()
    {
        if (!$this->expiration_date) return 'safe';

        $today = Carbon::today();
        $expiry = Carbon::parse($this->expiration_date);

        if ($expiry->isPast()) return 'expired';
        if ($expiry->isBefore(now()->addMonths(5))) return 'warning';

        return 'safe';
    }
}
