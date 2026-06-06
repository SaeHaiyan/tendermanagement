<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubconReview extends Model
{
    protected $fillable = [
        'tender_id',
        'subcon_id',
        'admin_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function subcon()
    {
        return $this->belongsTo(User::class, 'subcon_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
