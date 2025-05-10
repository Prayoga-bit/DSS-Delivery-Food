<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Services\AHPService;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    protected $ahpService;
    
    public function __construct(AHPService $ahpService)
    {
        $this->ahpService = $ahpService;
    }
    
    /**
     * Show the comparison matrix form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $criterias = Criteria::orderBy('code')->get();
        $comparisonValues = [];
        
        // Get existing comparison values
        foreach ($criterias as $criteria1) {
            foreach ($criterias as $criteria2) {
                $value = $this->ahpService->getComparisonValue($criteria1->id, $criteria2->id);
                $comparisonValues[$criteria1->id][$criteria2->id] = $value;
            }
        }
        
        // Scale values for display
        $scaleValues = [
            1 => 'Sama penting',
            2 => '1/2 Sedikit lebih penting',
            3 => 'Sedikit lebih penting',
            4 => '1/4 Lebih penting',
            5 => 'Lebih penting',
            6 => '1/6 Sangat lebih penting',
            7 => 'Sangat lebih penting',
            8 => '1/8 Mutlak lebih penting',
            9 => 'Mutlak lebih penting'
        ];
        
        return view('comparison.index', compact('criterias', 'comparisonValues', 'scaleValues'));
    }
    
    /**
     * Save the comparison matrix values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $comparisons = $request->input('comparison', []);
        
        foreach ($comparisons as $criteria1Id => $values) {
            foreach ($values as $criteria2Id => $value) {
                if ($value !== null && $value !== '') {
                    $this->ahpService->saveComparison($criteria1Id, $criteria2Id, $value);
                }
            }
        }
        
        // Calculate weights after saving all comparison values
        $result = $this->ahpService->calculateWeights();
        
        $message = 'Comparison matrix saved successfully.';
        if (!$result['is_consistent']) {
            $message .= ' Warning: The comparison matrix is not consistent (CR = ' . 
                round($result['consistency_ratio'], 3) . 
                ' > 0.1). Consider revising your comparisons.';
            return redirect()->route('comparison.index')
                ->with('warning', $message);
        }
        
        return redirect()->route('comparison.index')
            ->with('success', $message);
    }
}