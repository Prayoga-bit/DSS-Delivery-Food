<?php

namespace App\Services;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\Criteria;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TOPSISService
{
    /**
     * Get the decision matrix (alternatives x criteria)
     * 
     * @return array
     */
    public function getDecisionMatrix()
    {
        $alternatives = Alternative::orderBy('id')->get();
        $criterias = Criteria::orderBy('id')->get();
        $matrix = [];
        
        foreach ($alternatives as $i => $alternative) {
            foreach ($criterias as $j => $criteria) {
                $score = AlternativeScore::where('alternative_id', $alternative->id)
                    ->where('criteria_id', $criteria->id)
                    ->first();
                
                $matrix[$i][$j] = $score ? $score->score : 0;
            }
        }
        
        return [
            'matrix' => $matrix,
            'alternatives' => $alternatives,
            'criterias' => $criterias
        ];
    }
    
    /**
     * Normalize the decision matrix
     * 
     * @param array $matrix
     * @return array
     */
    public function normalizeMatrix($matrix)
    {
        $normalized = [];
        $rows = count($matrix);
        $cols = count($matrix[0]);
        
        // Calculate the square root of sum of squares for each column
        $denominators = [];
        for ($j = 0; $j < $cols; $j++) {
            $sumOfSquares = 0;
            for ($i = 0; $i < $rows; $i++) {
                $sumOfSquares += pow($matrix[$i][$j], 2);
            }
            $denominators[$j] = sqrt($sumOfSquares);
        }
        
        // Normalize each element
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                $normalized[$i][$j] = ($denominators[$j] == 0) ? 0 : $matrix[$i][$j] / $denominators[$j];
            }
        }
        
        return $normalized;
    }
    
    /**
     * Calculate weighted normalized matrix
     * 
     * @param array $normalizedMatrix
     * @param array $criterias
     * @return array
     */
    public function calculateWeightedMatrix($normalizedMatrix, $criterias)
    {
        $weighted = [];
        $rows = count($normalizedMatrix);
        $cols = count($normalizedMatrix[0]);
        
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                $weighted[$i][$j] = $normalizedMatrix[$i][$j] * $criterias[$j]->weight;
            }
        }
        
        return $weighted;
    }
    
    /**
     * Find ideal and negative-ideal solutions
     * 
     * @param array $weightedMatrix
     * @param array $criterias
     * @return array
     */
    public function findIdealSolutions($weightedMatrix, $criterias)
    {
        $rows = count($weightedMatrix);
        $cols = count($weightedMatrix[0]);
        
        $idealPositive = [];
        $idealNegative = [];
        
        for ($j = 0; $j < $cols; $j++) {
            $values = array_column($weightedMatrix, $j);
            
            // For benefit criteria (is_cost = false), max is ideal positive, min is ideal negative
            // For cost criteria (is_cost = true), min is ideal positive, max is ideal negative
            if ($criterias[$j]->is_cost) {
                $idealPositive[$j] = min($values);
                $idealNegative[$j] = max($values);
            } else {
                $idealPositive[$j] = max($values);
                $idealNegative[$j] = min($values);
            }
        }
        
        return [
            'positive' => $idealPositive,
            'negative' => $idealNegative
        ];
    }
    
    /**
     * Calculate separation measures (distance to ideal solutions)
     * 
     * @param array $weightedMatrix
     * @param array $idealSolutions
     * @return array
     */
    public function calculateSeparationMeasures($weightedMatrix, $idealSolutions)
    {
        $rows = count($weightedMatrix);
        $cols = count($weightedMatrix[0]);
        
        $positiveDistances = [];
        $negativeDistances = [];
        
        for ($i = 0; $i < $rows; $i++) {
            $positiveSum = 0;
            $negativeSum = 0;
            
            for ($j = 0; $j < $cols; $j++) {
                // Distance to positive ideal
                $positiveSum += pow($weightedMatrix[$i][$j] - $idealSolutions['positive'][$j], 2);
                
                // Distance to negative ideal
                $negativeSum += pow($weightedMatrix[$i][$j] - $idealSolutions['negative'][$j], 2);
            }
            
            $positiveDistances[$i] = sqrt($positiveSum);
            $negativeDistances[$i] = sqrt($negativeSum);
        }
        
        return [
            'positive' => $positiveDistances,
            'negative' => $negativeDistances
        ];
    }
    
    /**
     * Calculate preference values and rankings
     * 
     * @param array $separationMeasures
     * @param array $alternatives
     * @return array
     */
    public function calculatePreferenceValues($separationMeasures, $alternatives)
    {
        $preferenceValues = [];
        
        for ($i = 0; $i < count($alternatives); $i++) {
            $positiveDistance = $separationMeasures['positive'][$i];
            $negativeDistance = $separationMeasures['negative'][$i];
            
            // Calculate preference value: Vi = Di- / (Di+ + Di-)
            $denominator = $positiveDistance + $negativeDistance;
            $preferenceValues[$i] = ($denominator == 0) ? 0 : $negativeDistance / $denominator;
        }
        
        // Sort preference values for ranking
        $ranks = $preferenceValues;
        arsort($ranks); // Sort in descending order
        
        $ranking = [];
        $rank = 1;
        foreach (array_keys($ranks) as $index) {
            $ranking[$index] = $rank++;
        }
        
        return [
            'preference_values' => $preferenceValues,
            'ranking' => $ranking
        ];
    }
    
    /**
     * Perform the complete TOPSIS calculation
     * 
     * @return array
     */
    public function calculate()
    {
        // Step 1: Get decision matrix
        $data = $this->getDecisionMatrix();
        $matrix = $data['matrix'];
        $alternatives = $data['alternatives'];
        $criterias = $data['criterias'];
        
        // Steps 2-6: Your existing calculation code
        $normalizedMatrix = $this->normalizeMatrix($matrix);
        $weightedMatrix = $this->calculateWeightedMatrix($normalizedMatrix, $criterias);
        $idealSolutions = $this->findIdealSolutions($weightedMatrix, $criterias);
        $separationMeasures = $this->calculateSeparationMeasures($weightedMatrix, $idealSolutions);
        $preferenceData = $this->calculatePreferenceValues($separationMeasures, $alternatives);
        
        // Step 7: Save results to database with proper transaction handling
        $now = Carbon::now();
        
        try {
            DB::beginTransaction();
            
            // Use delete instead of truncate to avoid potential issues
            Result::query()->delete();
            
            // Save new results
            foreach ($alternatives as $i => $alternative) {
                Result::create([
                    'alternative_id' => $alternative->id,
                    'positive_distance' => $separationMeasures['positive'][$i],
                    'negative_distance' => $separationMeasures['negative'][$i],
                    'preference_value' => $preferenceData['preference_values'][$i],
                    'rank' => $preferenceData['ranking'][$i],
                    'calculated_at' => $now
                ]);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TOPSIS calculation error: ' . $e->getMessage());
            throw $e;
        }
        
        // Return calculation results
        return [
            'alternatives' => $alternatives,
            'criterias' => $criterias,
            'matrix' => $matrix,
            'normalized_matrix' => $normalizedMatrix,
            'weighted_matrix' => $weightedMatrix,
            'ideal_solutions' => $idealSolutions,
            'separation_measures' => $separationMeasures,
            'preference_values' => $preferenceData['preference_values'],
            'ranking' => $preferenceData['ranking']
        ];
    }
    // public function calculate()
    // {
    //     // Step 1: Get decision matrix
    //     $data = $this->getDecisionMatrix();
    //     $matrix = $data['matrix'];
    //     $alternatives = $data['alternatives'];
    //     $criterias = $data['criterias'];
        
    //     // Step 2: Normalize the decision matrix
    //     $normalizedMatrix = $this->normalizeMatrix($matrix);
        
    //     // Step 3: Calculate weighted normalized matrix
    //     $weightedMatrix = $this->calculateWeightedMatrix($normalizedMatrix, $criterias);
        
    //     // Step 4: Find ideal solutions
    //     $idealSolutions = $this->findIdealSolutions($weightedMatrix, $criterias);
        
    //     // Step 5: Calculate separation measures
    //     $separationMeasures = $this->calculateSeparationMeasures($weightedMatrix, $idealSolutions);
        
    //     // Step 6: Calculate preference values
    //     $preferenceData = $this->calculatePreferenceValues($separationMeasures, $alternatives);
        
    //     // Save results to database
    //     $now = Carbon::now();
        
    //     DB::transaction(function () use ($alternatives, $separationMeasures, $preferenceData, $now) {
    //         // Clear previous results
    //         Result::truncate();
            
    //         // Save new results
    //         foreach ($alternatives as $i => $alternative) {
    //             Result::create([
    //                 'alternative_id' => $alternative->id,
    //                 'positive_distance' => $separationMeasures['positive'][$i],
    //                 'negative_distance' => $separationMeasures['negative'][$i],
    //                 'preference_value' => $preferenceData['preference_values'][$i],
    //                 'rank' => $preferenceData['ranking'][$i],
    //                 'calculated_at' => $now
    //             ]);
    //         }
    //     });
        
    //     return [
    //         'alternatives' => $alternatives,
    //         'criterias' => $criterias,
    //         'matrix' => $matrix,
    //         'normalized_matrix' => $normalizedMatrix,
    //         'weighted_matrix' => $weightedMatrix,
    //         'ideal_solutions' => $idealSolutions,
    //         'separation_measures' => $separationMeasures,
    //         'preference_values' => $preferenceData['preference_values'],
    //         'ranking' => $preferenceData['ranking']
    //     ];
    // }
}