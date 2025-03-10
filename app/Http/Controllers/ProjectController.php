<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $projects = Auth::user()->projects()->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $project = auth()->user()->projects()->create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Roteiro criado com sucesso!');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        $scenes = $project->scenes()
            ->with('characters')
            ->orderBy('order')
            ->get()
            ->groupBy(function ($scene) {
                if (preg_match('/Ato (\d+)/', $scene->title, $matches)) {
                    return 'Ato ' . $matches[1];
                }
                return 'Outros';
            });

        return view('projects.show', compact('project', 'scenes'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $project->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Roteiro atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Roteiro excluído com sucesso!');
    }
} 