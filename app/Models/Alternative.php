<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternative extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'code', 'description', 'logo'];
    
    public function scores()
    {
        return $this->hasMany(AlternativeScore::class);
    }
    
    public function result()
    {
        return $this->hasOne(Result::class);
    }
    
    public function getScoreAttribute()
    {
        return $this->result ? $this->result->preference_value : 0;
    }
    
    public function getRankAttribute()
    {
        return $this->result ? $this->result->rank : 0;
    }
}