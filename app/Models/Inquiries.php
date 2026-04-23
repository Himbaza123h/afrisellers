<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;

class Inquiries extends Model
{
    use LogsActivity;
    protected $fillable = ['user_id', 'product_id', 'message', 'name', 'email', 'phone', 'phone_code', 'country_id', 'city', 'address', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
