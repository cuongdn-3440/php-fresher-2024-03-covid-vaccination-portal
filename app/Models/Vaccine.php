<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vaccine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function vaccineLots()
    {
        return $this->hasMany(VaccineLot::class);
    }

    public function getIsAllowAttribute($value)
    {
        if ($value) {
            return 'Allowed';
        }

        return 'Not allow';
    }

    public function scopeIsAllow($query)
    {
        return $query->where('is_allow', true);
    }
}
