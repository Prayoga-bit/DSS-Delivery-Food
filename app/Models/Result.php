<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'alternative_id', 
        'positive_distance', 
        'negative_distance', 
        'preference_value', 
        'rank',
        'calculated_at'
    ];
    
    protected $casts = [
        'calculated_at' => 'datetime'
    ];
    
    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }
}