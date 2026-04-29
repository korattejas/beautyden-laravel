<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffUnavailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'reason',
        'type',
        'status',
    ];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }

    public function beautician()
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }

    /**
     * Get human readable type
     */
    public function getTypeTextAttribute()
    {
        $types = [
            1 => 'Leave',
            2 => 'Personal',
            3 => 'Sick',
            4 => 'Holiday'
        ];
        return $types[$this->type] ?? 'N/A';
    }
}
