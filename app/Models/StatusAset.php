<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusAset extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function asets() { return $this->hasMany(Aset::class, 'status_id'); }
}
