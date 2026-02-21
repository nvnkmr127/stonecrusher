<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->latest()->paginate(15);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        if (!$request->has('status')) {
            $request->merge(['status' => 'active']);
        }
        if (!$request->has('progress')) {
            $request->merge(['progress' => 0]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required_unless:is_internal,1|nullable|exists:clients,id',
            'is_internal' => 'boolean',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'estimated_quantity' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,active,completed,cancelled',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    public function edit(Project $project)
    {
        $clients = Client::where('is_active', true)->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function show(Project $project)
    {
        $project->load('client');
        $gatePasses = $project->gatePasses()
            ->with(['vehicle', 'metalType'])
            ->latest('date')
            ->paginate(15);

        $totalTrips = $project->gatePasses()->count();
        $totalCft = $project->gatePasses()->sum('net_weight');

        return view('projects.show', compact('project', 'gatePasses', 'totalTrips', 'totalCft'));
    }

    public function update(Request $request, Project $project)
    {
        if (!$request->has('status')) {
            $request->merge(['status' => $project->status ?? 'active']);
        }
        if (!$request->has('progress')) {
            $request->merge(['progress' => $project->progress ?? 0]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required_unless:is_internal,1|nullable|exists:clients,id',
            'is_internal' => 'boolean',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'estimated_quantity' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,active,completed,cancelled',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
