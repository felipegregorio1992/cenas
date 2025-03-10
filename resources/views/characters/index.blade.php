<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Personagens') }}
                </h2>
                @if(isset($project))
                    <p class="text-sm text-gray-600 mt-1">Projeto: {{ $project->name }}</p>
                @endif
            </div>
            <a href="{{ route('characters.create', ['project' => $project->id]) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Personagem
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($characters->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum personagem encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando um novo personagem.</p>
                            <div class="mt-6">
                                <a href="{{ route('characters.create', ['project' => $project->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Criar Personagem
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($characters as $character)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('characters.show', ['character' => $character, 'project' => $project->id]) }}" class="hover:text-indigo-600">
                                                {{ $character->name }}
                                            </a>
                                        </h3>
                                        <div class="flex gap-2 mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $character->role }}
                                            </span>
                                            @if($character->type)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $character->type }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('characters.edit', ['character' => $character, 'project' => $project->id]) }}" 
                                           class="text-gray-400 hover:text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('characters.destroy', ['character' => $character, 'project' => $project->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Tem certeza que deseja excluir este personagem?')"
                                                    class="text-gray-400 hover:text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                @if($character->description)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                                        {{ $character->description }}
                                    </p>
                                @endif

                                <div class="mt-4 space-y-3">
                                    @if($character->goals)
                                        <div>
                                            <h4 class="text-xs font-medium text-gray-500 uppercase">Objetivos</h4>
                                            <p class="text-sm text-gray-700 line-clamp-2">{{ $character->goals }}</p>
                                        </div>
                                    @endif

                                    @if($character->personality)
                                        <div>
                                            <h4 class="text-xs font-medium text-gray-500 uppercase">Personalidade</h4>
                                            <p class="text-sm text-gray-700 line-clamp-2">{{ $character->personality }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">
                                            {{ $character->scenes->count() }} cenas
                                        </span>
                                        <a href="{{ route('characters.show', ['character' => $character, 'project' => $project->id]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Ver detalhes →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleAct(contentId) {
            const content = document.getElementById(contentId);
            const iconId = 'icon-' + contentId;
            const icon = document.getElementById(iconId);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
</x-app-layout> 