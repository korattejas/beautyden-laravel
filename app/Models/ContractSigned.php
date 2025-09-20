<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractSigned extends Model
{
    protected $table = 'contracts_signed';
    
    protected $fillable = [
        'provider_id',
        'provider_name',
        'provider_mobile',
        'provider_address',
        'contract_type',
        'signed_pdf',
        'signature_image',
        'ip_address',
        'signed_at',
        'status',
    ];
}
