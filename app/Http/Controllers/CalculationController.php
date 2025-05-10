<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\Result;
use App\Services\AHPService;
use App\Services\TOPSISService;
use Illuminate\Http\Request;

class CalculationController extends Controller
{
    protected $ahpService;
    protected $topsisService;
    
    public function __construct(AHPService $ahpService, TOPSISService $topsisService)
    {
        $this->ahpService = $ahpService;
        $this->topsisService = $topsisService;
    }
    
    /**
     * Show calculation page and results
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check if we have all required data
        $criteriaCount = Criteria::count();
        $alternativeCount = Alternative::count();
        
        if ($criteriaCount == 0 || $alternativeCount == 0) {
            return redirect()->route('dashboard')
                ->with('warning', 'Please add criteria and alternatives first.');
        }
        
        // Get AHP weights
        $ahpData = $this->ahpService->calculateWeights();
        $criterias = $ahpData['criterias'];
        
        // Calculate if requested
        $calculated = false;
        $topsisData = null;
        $results = Result::with('alternative')->orderBy('rank')->get();
        
        if ($results->count() > 0) {
            $calculated = true;
            $topsisData = $this->topsisService->getDecisionMatrix();
        }
        
        return view('calculation.index', compact(
            'criterias', 
            'calculated', 
            'results', 
            'topsisData'
        ));
    }
    
    /**
     * Perform AHP and TOPSIS calculation
     *
     * @return \Illuminate\Http\Response
     */
    public function calculate()
    {
        // Check if there are enough scores
        $totalScores = \App\Models\AlternativeScore::count();
        $expectedScores = Alternative::count() * Criteria::count();
        
        if ($totalScores < $expectedScores) {
            return redirect()->route('alternatives.scores')
                ->with('warning', 'Please complete all alternative scores before calculating.');
        }
        
        // Calculate AHP weights first
        $ahpResult = $this->ahpService->calculateWeights();
        
        if (!$ahpResult['is_consistent']) {
            return redirect()->route('comparison.index')
                ->with('warning', 'The comparison matrix is not consistent (CR = ' . 
                    round($ahpResult['consistency_ratio'], 3) . 
                    ' > 0.1). Please revise your comparisons.');
        }
        
        // Run TOPSIS calculation
        $topsisResult = $this->topsisService->calculate();
        
        return redirect()->route('calculation.index')
            ->with('success', 'Calculation completed successfully.');
    }
}