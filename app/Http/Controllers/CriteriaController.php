<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    /**
     * Display a listing of the criteria.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $criterias = Criteria::orderBy('code')->get();
        
        return view('criteria.index', compact('criterias'));
    }

    /**
     * Show the form for creating a new criteria.
     * Note: Based on requirements, we're using fixed criteria, but keeping this for flexibility
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('criteria.create');
    }

    /**
     * Store a newly created criteria in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:criterias',
            'description' => 'nullable|string',
            'is_cost' => 'required|boolean',
        ]);
        
        Criteria::create($validated);
        
        return redirect()->route('criteria.index')
            ->with('success', 'Criteria created successfully.');
    }

    /**
     * Show the form for editing the specified criteria.
     *
     * @param  \App\Models\Criteria  $criteria
     * @return \Illuminate\Http\Response
     */
    public function edit(Criteria $criteria)
    {
        return view('criteria.edit', compact('criteria'));
    }

    /**
     * Update the specified criteria in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Criteria  $criteria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Criteria $criteria)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:criterias,code,' . $criteria->id,
            'description' => 'nullable|string',
            'is_cost' => 'required|boolean',
        ]);
        
        $criteria->update($validated);
        
        return redirect()->route('criteria.index')
            ->with('success', 'Criteria updated successfully.');
    }

    /**
     * Remove the specified criteria from storage.
     * Note: We're not implementing delete because criteria are fixed in this case
     *
     * @param  \App\Models\Criteria  $criteria
     * @return \Illuminate\Http\Response
     */
    public function destroy(Criteria $criteria)
    {
        // Not implemented due to fixed criteria requirement
        return redirect()->route('criteria.index')
            ->with('error', 'Cannot delete fixed criteria.');
    }
}