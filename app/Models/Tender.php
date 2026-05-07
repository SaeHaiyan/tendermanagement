<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'selected_subcon_id',
        'work_status',
        'progress_percent',
        'report_path',
        'title',
        'tender_ref_number',
        'required_grade',
        'required_services',
        'deadline',
        'description',
        'estimated_budget',
        'priority_level',
        'years_experience_required',
        'site_location',
        'site_visit_date',
        'status',
    ];

    public function selectedSubcon()
    {
        return $this->belongsTo(User::class, 'selected_subcon_id');
    }

    protected $casts = [
        'report_path' => 'array',
        'deadline' => 'date',
        'site_visit_date' => 'datetime',
    ];
}
