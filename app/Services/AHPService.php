<?php

namespace App\Services;

use App\Models\Criteria;
use App\Models\ComparisonMatrix;
use Illuminate\Support\Facades\DB;

class AHPService
{
    /**
     * Get the pairwise comparison matrix as a 2D array
     * 
     * @return array
     */
    public function getComparisonMatrix()
    {
        $criterias = Criteria::orderBy('id')->get();
        $n = $criterias->count();
        
        // Initialize matrix with 1's on diagonal
        $matrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i == $j) {
                    $matrix[$i][$j] = 1;
                } else {
                    $matrix[$i][$j] = 0;
                }
            }
        }
        
        // Fill matrix with stored comparison values
        $comparisons = ComparisonMatrix::all();
        
        foreach ($comparisons as $comparison) {
            $i = $criterias->search(function($item) use ($comparison) {
                return $item->id == $comparison->criteria1_id;
            });
            
            $j = $criterias->search(function($item) use ($comparison) {
                return $item->id == $comparison->criteria2_id;
            });
            
            if ($i !== false && $j !== false) {
                $matrix[$i][$j] = $comparison->value;
                // Reciprocal value
                $matrix[$j][$i] = 1 / $comparison->value;
            }
        }
        
        return [
            'matrix' => $matrix,
            'criterias' => $criterias
        ];
    }
    
    /**
     * Calculate criteria weights using AHP
     * 
     * @return array
     */
    public function calculateWeights()
    {
        // Get comparison matrix
        $data = $this->getComparisonMatrix();
        $matrix = $data['matrix'];
        $criterias = $data['criterias'];
        $n = count($matrix);
        
        // Calculate column sums
        $colSums = [];
        for ($j = 0; $j < $n; $j++) {
            $sum = 0;
            for ($i = 0; $i < $n; $i++) {
                $sum += $matrix[$i][$j];
            }
            $colSums[$j] = $sum;
        }
        
        // Normalize matrix
        $normalizedMatrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalizedMatrix[$i][$j] = $matrix[$i][$j] / $colSums[$j];
            }
        }
        
        // Calculate row averages (criteria weights)
        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $normalizedMatrix[$i][$j];
            }
            $weights[$i] = $sum / $n;
        }
        
        // Calculate Consistency Ratio
        $consistencyData = $this->calculateConsistencyRatio($matrix, $weights, $n);
        
        // Save weights to database
        for ($i = 0; $i < $n; $i++) {
            $criteria = $criterias[$i];
            $criteria->weight = $weights[$i];
            $criteria->save();
        }
        
        return [
            'weights' => $weights,
            'consistency_ratio' => $consistencyData['consistency_ratio'],
            'is_consistent' => $consistencyData['consistency_ratio'] < 0.1,
            'criterias' => $criterias
        ];
    }
    
    /**
     * Calculate Consistency Ratio
     * 
     * @param array $matrix
     * @param array $weights
     * @param int $n
     * @return array
     */
    private function calculateConsistencyRatio($matrix, $weights, $n)
    {
        // Calculate weighted sum vector
        $weightedSum = [];
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $matrix[$i][$j] * $weights[$j];
            }
            $weightedSum[$i] = $sum;
        }
        
        // Calculate consistency vector
        $consistencyVector = [];
        for ($i = 0; $i < $n; $i++) {
            $consistencyVector[$i] = $weightedSum[$i] / $weights[$i];
        }
        
        // Calculate lambda max (Principal Eigen Value)
        $lambdaMax = array_sum($consistencyVector) / $n;
        
        // Calculate Consistency Index
        $CI = ($lambdaMax - $n) / ($n - 1);
        
        // Random Index values for n=1 to n=10
        $RI = [0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
        
        // Calculate Consistency Ratio
        $CR = ($n <= 2) ? 0 : $CI / $RI[$n - 1];
        
        return [
            'lambda_max' => $lambdaMax,
            'consistency_index' => $CI,
            'consistency_ratio' => $CR
        ];
    }
    
    /**
     * Save comparison value between two criteria
     * 
     * @param int $criteria1Id
     * @param int $criteria2Id
     * @param float $value
     * @return void
     */
    public function saveComparison($criteria1Id, $criteria2Id, $value)
    {
        ComparisonMatrix::updateOrCreate(
            ['criteria1_id' => $criteria1Id, 'criteria2_id' => $criteria2Id],
            ['value' => $value]
        );
        
        // Set reciprocal value automatically
        if ($criteria1Id != $criteria2Id) {
            ComparisonMatrix::updateOrCreate(
                ['criteria1_id' => $criteria2Id, 'criteria2_id' => $criteria1Id],
                ['value' => 1 / $value]
            );
        }
    }
    
    /**
     * Get comparison value between two criteria
     * 
     * @param int $criteria1Id
     * @param int $criteria2Id
     * @return float
     */
    public function getComparisonValue($criteria1Id, $criteria2Id)
    {
        $comparison = ComparisonMatrix::where('criteria1_id', $criteria1Id)
            ->where('criteria2_id', $criteria2Id)
            ->first();
            
        return $comparison ? $comparison->value : ($criteria1Id == $criteria2Id ? 1 : null);
    }
}