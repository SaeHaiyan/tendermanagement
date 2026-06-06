<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'company_name',
        'company_address',
        'pic_name',
        'pic_phone',
        'office_phone',
        'company_email',
        'cidb_reg_number',
        'ssm_number',
        'company_level',
        'year_established',
        'cidb_grades',
        'services_provided',
        'pending_documents',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'cidb_grades' => 'array'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cidb_grades' => 'array',
            'pending_documents' => 'array',
        ];
    }

    // Relationships
    public function reviews()
    {
        return $this->hasMany(SubconReview::class, 'subcon_id');
    }

    public function givenReviews()
    {
        return $this->hasMany(SubconReview::class, 'admin_id');
    }

    // Helper accessor for average rating
    public function getAverageRatingAttribute()
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 2) : 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
}
