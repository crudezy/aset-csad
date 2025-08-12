<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    public function pegawais() 
    { 
        return $this->hasMany(Pegawai::class); 
    }

    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
