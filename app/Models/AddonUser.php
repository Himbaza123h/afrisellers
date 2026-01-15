<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AddonUser extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'addon_id',
        'user_id',
        'type',
        'paid_at',
        'paid_days',
        'ended_at',
        'product_id',
        'supplier_id',
        'loadboad_id',
        'car_id',
        'showroom_id',
        'tradeshow_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'paid_at' => 'datetime',
        'ended_at' => 'datetime',
        'paid_days' => 'integer',
    ];

    /**
     * Available addon types.
     */
    const TYPE_PRODUCT = 'product';
    const TYPE_SUPPLIER = 'supplier';
    const TYPE_LOADBOAD = 'loadboad';
    const TYPE_CAR = 'car';
    const TYPE_SHOWROOM = 'showroom';
    const TYPE_TRADESHOW = 'tradeshow';

    /**
     * Get all available types.
     */
    public static function getTypes()
    {
        return [
            self::TYPE_PRODUCT,
            self::TYPE_SUPPLIER,
            self::TYPE_LOADBOAD,
            self::TYPE_CAR,
            self::TYPE_SHOWROOM,
            self::TYPE_TRADESHOW,
        ];
    }

    /**
     * Get the addon that owns the addon user.
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }

    /**
     * Get the user that owns the addon user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product if type is product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the supplier if type is supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(BusinessProfile::class, 'supplier_id');
    }

    /**
     * Get the loadboad if type is loadboad.
     */
    public function loadboad()
    {
        return $this->belongsTo(Load::class, 'loadboad_id');
    }

    /**
     * Get the car if type is car.
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get the showroom if type is showroom.
     */
    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }

    /**
     * Get the tradeshow if type is tradeshow.
     */
    public function tradeshow()
    {
        return $this->belongsTo(Tradeshow::class);
    }

    /**
     * Get the related entity based on type.
     */
    public function getRelatedEntityAttribute()
    {
        switch ($this->type) {
            case self::TYPE_PRODUCT:
                return $this->product;
            case self::TYPE_SUPPLIER:
                return $this->supplier;
            case self::TYPE_LOADBOAD:
                return $this->loadboad;
            case self::TYPE_CAR:
                return $this->car;
            case self::TYPE_SHOWROOM:
                return $this->showroom;
            case self::TYPE_TRADESHOW:
                return $this->tradeshow;
            default:
                return null;
        }
    }

    /**
     * Check if the addon is active.
     */
    public function isActive()
    {
        if (!$this->paid_at) {
            return false;
        }

        if (!$this->ended_at) {
            return true;
        }

        return $this->ended_at->isFuture();
    }

    /**
     * Check if the addon has expired.
     */
    public function isExpired()
    {
        return $this->ended_at && $this->ended_at->isPast();
    }

    /**
     * Get days remaining until expiration.
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->ended_at) {
            return null;
        }

        $now = Carbon::now();

        if ($this->ended_at->isPast()) {
            return 0;
        }

        return $now->diffInDays($this->ended_at);
    }

    /**
     * Calculate and set the ended_at date based on paid_days.
     */
    public function calculateEndDate()
    {
        if ($this->paid_at && $this->paid_days > 0) {
            $this->ended_at = Carbon::parse($this->paid_at)->addDays($this->paid_days);
            $this->save();
        }
    }

    /**
     * Scope a query to only include active addons.
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('paid_at')
            ->where(function ($query) {
                $query->whereNull('ended_at')
                    ->orWhere('ended_at', '>', now());
            });
    }

    /**
     * Scope a query to only include expired addons.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('ended_at')
            ->where('ended_at', '<=', now());
    }

    /**
     * Scope a query to only include addons of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include addons for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Renew the addon for additional days.
     */
    public function renew($days)
    {
        $currentEndDate = $this->ended_at ?? now();

        $this->paid_days += $days;
        $this->ended_at = Carbon::parse($currentEndDate)->addDays($days);
        $this->save();

        return $this;
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute()
    {
        if ($this->isActive()) {
            return 'green';
        } elseif ($this->isExpired()) {
            return 'red';
        } else {
            return 'gray';
        }
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute()
    {
        if ($this->isActive()) {
            return 'Active';
        } elseif ($this->isExpired()) {
            return 'Expired';
        } else {
            return 'Pending';
        }
    }
}
