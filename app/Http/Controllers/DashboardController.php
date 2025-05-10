<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\Result;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $criteriaCount = Criteria::count();
        $alternativeCount = Alternative::count();
        $results = Result::with('alternative')->orderBy('rank')->take(3)->get();
        $hasResults = Result::count() > 0;
        
        return view('layouts.dashboard', compact(
            'criteriaCount',
            'alternativeCount',
            'results',
            'hasResults'
        ));
    }
}