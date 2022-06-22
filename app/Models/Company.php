<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inisial', 'nama_perusahaan', 'level', 'ccow_id', 'mitra_id', 'hiden'
    ];

    public function ccow()
    {
        return $this->belongsTo(Company::class,'ccow_id','id');
    }

    public function mitra()
    {
        return $this->belongsTo(Company::class,'mitra_id', 'id');
    }

    public function submitra()
    {
        return $this->belongsTo(Company::class,'mitra_id');
    }
}
