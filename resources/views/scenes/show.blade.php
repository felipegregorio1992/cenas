<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $scene->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Projeto: {{ $project->name }}</p>
            </div>
            <a href="{{ route('scenes.index', ['project' => $project->id]) }}" 
               class="text-gray-600 hover:text-gray-900">
                Voltar para Lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Informações da Cena -->
                    <div class="mb-8">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $scene->title }}</h3>
                                <p class="text-gray-500">Duração: {{ $scene->duration }} minutos</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('scenes.edit', ['scene' => $scene, 'project' => $project->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Editar
                                </a>
                                <form action="{{ route('scenes.destroy', ['scene' => $scene, 'project' => $project->id]) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Tem certeza que deseja excluir esta cena?')"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-700 whitespace-pre-line">{{ $scene->description }}</p>
                    </div>

                    <!-- Personagens na Cena -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Personagens na Cena</h3>
                        <div class="bg-gray-50 rounded-lg p-6">
                            @if ($scene->characters->isNotEmpty())
                                <div class="space-y-6">
                                    @foreach ($scene->characters->sortBy('name') as $character)
                                        <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h4 class="text-indigo-600 font-medium">
                                                        <a href="{{ route('characters.show', ['character' => $character, 'project' => $project->id]) }}" 
                                                           class="hover:text-indigo-800">
                                                            {{ $character->name }}
                                                        </a>
                                                    </h4>
                                                    <span class="text-sm text-gray-500">{{ $character->role }}</span>
                                                </div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $character->type }}
                                                </span>
                                            </div>
                                            @if($character->pivot->dialogue)
                                                <div class="mt-2 bg-white rounded p-4">
                                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $character->pivot->dialogue }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 italic">Nenhum personagem nesta cena.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 