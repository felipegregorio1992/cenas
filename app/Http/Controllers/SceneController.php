<?php

namespace App\Http\Controllers;

use App\Models\Scene;
use App\Models\Character;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SceneController extends Controller
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

        // Buscar todas as cenas do projeto
        $scenes = Scene::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->with(['characters' => function($query) {
                $query->select('characters.*', 'character_scene.dialogue')
                    ->orderBy('name', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();

        // Organizar as cenas por ato
        $acts = [];
        foreach ($scenes as $scene) {
            $actNumber = 0; // Ato padrão para cenas sem número de ato
            
            // Tenta encontrar o número do ato no título
            if (preg_match('/Ato (\d+)/', $scene->title, $matches)) {
                $actNumber = (int) $matches[1];
            }
            
            // Se não encontrou um ato no título, coloca na seção "Outras Cenas"
            $actTitle = $actNumber > 0 ? "Ato {$actNumber}" : "Outras Cenas";
            
            if (!isset($acts[$actNumber])) {
                $acts[$actNumber] = [
                    'title' => $actTitle,
                    'scenes' => []
                ];
            }
            
            $acts[$actNumber]['scenes'][] = [
                'id' => $scene->id,
                'title' => $scene->title,
                'description' => $scene->description,
                'duration' => $scene->duration,
                'characters' => $scene->characters->map(function($character) {
                    return [
                        'name' => $character->name,
                        'dialogue' => $character->pivot->dialogue
                    ];
                })->toArray()
            ];
        }

        // Ordenar os atos por número
        ksort($acts);

        return view('scenes.index', compact('acts', 'project'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Verifica se um projeto foi especificado
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

        $this->authorize('create', Scene::class);
        $characters = Character::where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->orderBy('name', 'asc')
            ->get();
        return view('scenes.create', compact('characters', 'project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Scene::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'characters' => 'required|array|min:1',
            'characters.*' => 'exists:characters,id',
            'dialogues' => 'array',
            'dialogues.*' => 'nullable|string',
            'project_id' => 'required|exists:projects,id'
        ]);

        // Verifica se o usuário tem acesso ao projeto
        $project = Project::findOrFail($validated['project_id']);
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        $scene = Auth::user()->scenes()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'order' => $validated['order'],
            'project_id' => $validated['project_id']
        ]);

        // Prepara os dados dos personagens com seus diálogos
        $characters = collect($validated['characters'])->mapWithKeys(function($characterId) use ($request) {
            return [$characterId => ['dialogue' => $request->input("dialogues.$characterId")]];
        })->all();

        // Vincula os personagens e seus diálogos
        $scene->characters()->attach($characters);

        return redirect()->route('scenes.show', ['scene' => $scene, 'project' => $validated['project_id']])
            ->with('success', 'Cena criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Scene $scene, Request $request)
    {
        $this->authorize('view', $scene);

        // Verifica se um projeto foi especificado
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Verifica se a cena pertence ao projeto
        if ($scene->project_id != $projectId) {
            abort(404, 'Cena não encontrada neste projeto.');
        }

        // Carrega o projeto
        $project = Project::findOrFail($projectId);
        
        // Verifica se o usuário tem acesso ao projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        $scene->load(['characters' => function($query) {
            $query->orderBy('name', 'asc');
        }]);

        return view('scenes.show', compact('scene', 'project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scene $scene, Request $request)
    {
        $this->authorize('update', $scene);

        // Verifica se um projeto foi especificado
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Verifica se a cena pertence ao projeto
        if ($scene->project_id != $projectId) {
            abort(404, 'Cena não encontrada neste projeto.');
        }

        // Carrega o projeto
        $project = Project::findOrFail($projectId);
        
        // Verifica se o usuário tem acesso ao projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        $characters = Character::where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->orderBy('name', 'asc')
            ->get();
        return view('scenes.edit', compact('scene', 'characters', 'project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scene $scene)
    {
        $this->authorize('update', $scene);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'characters' => 'required|array|min:1',
            'characters.*' => 'exists:characters,id',
            'dialogues' => 'array',
            'dialogues.*' => 'nullable|string',
            'project_id' => 'required|exists:projects,id'
        ]);

        // Verifica se o usuário tem acesso ao projeto
        $project = Project::findOrFail($validated['project_id']);
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        // Verifica se a cena pertence ao projeto
        if ($scene->project_id != $validated['project_id']) {
            abort(404, 'Cena não encontrada neste projeto.');
        }

        $scene->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'order' => $validated['order']
        ]);

        // Prepara os dados dos personagens com seus diálogos
        $characters = collect($validated['characters'])->mapWithKeys(function($characterId) use ($request) {
            return [$characterId => ['dialogue' => $request->input("dialogues.$characterId")]];
        })->all();

        // Sincroniza os personagens e seus diálogos
        $scene->characters()->sync($characters);

        return redirect()->route('scenes.show', ['scene' => $scene, 'project' => $validated['project_id']])
            ->with('success', 'Cena atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scene $scene, Request $request)
    {
        $this->authorize('delete', $scene);

        // Verifica se um projeto foi especificado
        $projectId = $request->query('project');
        if (!$projectId) {
            return redirect()->route('projects.index')
                ->with('error', 'Por favor, selecione um projeto.');
        }

        // Verifica se a cena pertence ao projeto
        if ($scene->project_id != $projectId) {
            abort(404, 'Cena não encontrada neste projeto.');
        }

        $scene->characters()->detach();
        $scene->delete();

        return redirect()->route('scenes.index', ['project' => $projectId])
            ->with('success', 'Cena excluída com sucesso!');
    }

    public function reorder(Request $request)
    {
        $scenes = $request->validate([
            'scenes' => 'required|array',
            'scenes.*' => 'exists:scenes,id'
        ])['scenes'];

        foreach ($scenes as $order => $id) {
            $scene = Scene::findOrFail($id);
            $this->authorize('update', $scene);
            $scene->update(['order' => $order + 1]);
        }

        return response()->json(['message' => 'Ordem atualizada com sucesso!']);
    }

    public function addCharacter(Request $request, Scene $scene)
    {
        $this->authorize('update', $scene);
        
        $validated = $request->validate([
            'character_id' => 'required|exists:characters,id',
            'dialogue' => 'nullable|string'
        ]);

        $scene->characters()->attach($validated['character_id'], [
            'dialogue' => $validated['dialogue']
        ]);

        return back()->with('success', 'Personagem adicionado à cena!');
    }

    public function removeCharacter(Scene $scene, Character $character)
    {
        $this->authorize('update', $scene);
        $scene->characters()->detach($character->id);
        return back()->with('success', 'Personagem removido da cena!');
    }
}
