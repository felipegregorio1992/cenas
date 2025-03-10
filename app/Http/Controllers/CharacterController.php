<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Scene;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CharacterController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Verifica se um projeto foi especificado
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Carrega o projeto
        $project = Project::findOrFail($projectId);
        
        // Verifica se o usuário tem acesso ao projeto
        if ($project->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        // Busca os personagens do projeto específico
        $characters = Character::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->with(['scenes' => function($query) {
                $query->orderBy('order', 'asc');
            }])
            ->get();

        foreach ($characters as $character) {
            // Buscar todas as cenas do personagem do projeto atual
            $scenes = Scene::whereHas('characters', function($query) use ($character) {
                $query->where('characters.id', $character->id);
            })
            ->where('project_id', $projectId)
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy(function($scene) {
                if (preg_match('/Ato (\d+)/', $scene->title, $matches)) {
                    return (int) $matches[1];
                }
                return 0; // Para cenas sem número de ato
            });

            // Organizar o conteúdo por ato
            $actContents = [];
            
            // Iterar sobre os atos (1 a 30)
            for ($act = 1; $act <= 30; $act++) {
                if (isset($scenes[$act])) {
                    $actContent = '';
                    foreach ($scenes[$act] as $scene) {
                        // Buscar o diálogo específico deste personagem para esta cena
                        $dialogue = DB::table('character_scene')
                            ->where('character_id', $character->id)
                            ->where('scene_id', $scene->id)
                            ->value('dialogue');

                        if (!empty($dialogue)) {
                            if (!empty($actContent)) {
                                $actContent .= "\n\n";
                            }
                            $actContent .= $dialogue;
                        } elseif (!empty($scene->description)) {
                            if (!empty($actContent)) {
                                $actContent .= "\n\n";
                            }
                            $actContent .= $scene->description;
                        }
                    }
                    $actContents[$act] = $actContent;
                }
            }

            $character->act_contents = $actContents;
        }

        return view('characters.index', compact('characters', 'project'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Carrega o projeto
        $project = Project::findOrFail($projectId);
        
        // Verifica se o usuário tem acesso ao projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        return view('characters.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'role' => 'required|max:255',
            'type' => 'nullable|max:255',
            'goals' => 'nullable',
            'fears' => 'nullable',
            'history' => 'nullable',
            'personality' => 'nullable',
            'notes' => 'nullable',
            'project_id' => 'required|exists:projects,id'
        ]);

        // Verifica se o usuário tem acesso ao projeto
        $project = Project::findOrFail($validated['project_id']);
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        $character = Character::create([
            'user_id' => Auth::id(),
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'role' => $validated['role'],
            'type' => $validated['type'],
            'goals' => $validated['goals'],
            'fears' => $validated['fears'],
            'history' => $validated['history'],
            'personality' => $validated['personality'],
            'notes' => $validated['notes']
        ]);

        return redirect()->route('characters.show', ['character' => $character, 'project' => $validated['project_id']])
            ->with('success', 'Personagem criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Character $character, Request $request)
    {
        $this->authorize('view', $character);
        
        // Verifica se um projeto foi especificado
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Verifica se o personagem pertence ao projeto
        if ($character->project_id != $projectId) {
            abort(404, 'Personagem não encontrado neste projeto.');
        }

        // Carrega o projeto
        $project = Project::findOrFail($projectId);
        
        // Verifica se o usuário tem acesso ao projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }
        
        // Carrega as cenas ordenadas
        $character->load(['scenes' => function($query) {
            $query->orderBy('order', 'asc');
        }]);
        
        return view('characters.show', compact('character', 'project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Character $character, Request $request)
    {
        $this->authorize('update', $character);

        // Verifica se um projeto foi especificado
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Verifica se o personagem pertence ao projeto
        if ($character->project_id != $projectId) {
            abort(404, 'Personagem não encontrado neste projeto.');
        }

        // Carrega o projeto
        $project = Project::findOrFail($projectId);
        
        // Verifica se o usuário tem acesso ao projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        return view('characters.edit', compact('character', 'project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Character $character)
    {
        $this->authorize('update', $character);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'role' => 'required|max:255',
            'type' => 'nullable|max:255',
            'goals' => 'nullable',
            'fears' => 'nullable',
            'history' => 'nullable',
            'personality' => 'nullable',
            'notes' => 'nullable',
            'project_id' => 'required|exists:projects,id'
        ]);

        // Verifica se o usuário tem acesso ao projeto
        $project = Project::findOrFail($validated['project_id']);
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        // Verifica se o personagem pertence ao projeto
        if ($character->project_id != $validated['project_id']) {
            abort(404, 'Personagem não encontrado neste projeto.');
        }

        $character->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'role' => $validated['role'],
            'type' => $validated['type'],
            'goals' => $validated['goals'],
            'fears' => $validated['fears'],
            'history' => $validated['history'],
            'personality' => $validated['personality'],
            'notes' => $validated['notes']
        ]);

        return redirect()->route('characters.show', ['character' => $character, 'project' => $validated['project_id']])
            ->with('success', 'Personagem atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Character $character, Request $request)
    {
        $this->authorize('delete', $character);

        // Verifica se um projeto foi especificado
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Verifica se o personagem pertence ao projeto
        if ($character->project_id != $projectId) {
            abort(404, 'Personagem não encontrado neste projeto.');
        }

        // Remove o personagem e suas relações
        $character->scenes()->detach();
        $character->delete();

        return redirect()->route('characters.index', ['project' => $projectId])
            ->with('success', 'Personagem excluído com sucesso!');
    }
}
