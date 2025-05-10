<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;
    
    protected $table = 'criterias';
    protected $fillable = ['name', 'code', 'description', 'weight', 'is_cost'];
    
    public function comparisonMatrix1()
    {
        return $this->hasMany(ComparisonMatrix::class, 'criteria1_id');
    }
    
    public function comparisonMatrix2()
    {
        return $this->hasMany(ComparisonMatrix::class, 'criteria2_id');
    }
    
    public function alternativeScores()
    {
        return $this->hasMany(AlternativeScore::class);
    }
}