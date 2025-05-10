<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\Criteria;
use Illuminate\Http\Request;

class AlternativeController extends Controller
{
    /**
     * Display a listing of alternatives.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alternatives = Alternative::orderBy('code')->get();
        
        return view('alternatives.index', compact('alternatives'));
    }

    /**
     * Show the form for creating a new alternative.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('alternatives.create');
    }

    /**
     * Store a newly created alternative in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:alternatives',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024', // Optional logo upload
        ]);
        
        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }
        
        Alternative::create($validated);
        
        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative created successfully.');
    }

    /**
     * Show the form for editing the specified alternative.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function edit(Alternative $alternative)
    {
        return view('alternatives.edit', compact('alternative'));
    }

    /**
     * Update the specified alternative in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alternative $alternative)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:alternatives,code,' . $alternative->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024', // Optional logo upload
        ]);
        
        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }
        
        $alternative->update($validated);
        
        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative updated successfully.');
    }

    /**
     * Remove the specified alternative from storage.
     *
     * @param  \App\Models\Alternative  $alternative
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alternative $alternative)
    {
        // Delete related scores
        AlternativeScore::where('alternative_id', $alternative->id)->delete();
        
        // Delete the alternative
        $alternative->delete();
        
        return redirect()->route('alternatives.index')
            ->with('success', 'Alternative deleted successfully.');
    }
    
    /**
     * Show form for editing scores for all alternatives.
     *
     * @return \Illuminate\Http\Response
     */
    public function editScores()
    {
        $alternatives = Alternative::orderBy('code')->get();
        $criterias = Criteria::orderBy('code')->get();
        $scores = [];
        
        foreach ($alternatives as $alternative) {
            foreach ($criterias as $criteria) {
                $score = AlternativeScore::where('alternative_id', $alternative->id)
                    ->where('criteria_id', $criteria->id)
                    ->first();
                
                $scores[$alternative->id][$criteria->id] = $score ? $score->score : null;
            }
        }
        
        return view('alternatives.scores', compact('alternatives', 'criterias', 'scores'));
    }
    
    /**
     * Update scores for all alternatives.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateScores(Request $request)
    {
        $scores = $request->input('scores', []);
        
        foreach ($scores as $alternativeId => $criteriaScores) {
            foreach ($criteriaScores as $criteriaId => $score) {
                if ($score !== null && $score !== '') {
                    AlternativeScore::updateOrCreate(
                        [
                            'alternative_id' => $alternativeId,
                            'criteria_id' => $criteriaId
                        ],
                        ['score' => $score]
                    );
                }
            }
        }
        
        return redirect()->route('alternatives.scores')
            ->with('success', 'Scores updated successfully.');
    }
}