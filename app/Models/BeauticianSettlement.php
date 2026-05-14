<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeauticianSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'company_to_beautician',
        'beautician_to_company',
    ];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }
}
