<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $character->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Projeto: {{ $project->name }}</p>
            </div>
            <a href="{{ route('characters.index', ['project' => $project->id]) }}" class="text-gray-600 hover:text-gray-900">
                Voltar para Lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Informações Básicas do Personagem -->
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <h3 class="text-2xl font-bold">{{ $character->name }}</h3>
                        <span class="px-3 py-1 text-sm rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                            {{ $character->role }}
                        </span>
                        @if($character->type)
                            <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">
                                {{ $character->type }}
                            </span>
                        @endif
                    </div>
                    @if($character->description)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Descrição</h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $character->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Características do Personagem -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @if($character->goals)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Objetivos</h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $character->goals }}</p>
                        </div>
                    @endif

                    @if($character->fears)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Medos</h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $character->fears }}</p>
                        </div>
                    @endif

                    @if($character->personality)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Personalidade</h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $character->personality }}</p>
                        </div>
                    @endif

                    @if($character->history)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">História</h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $character->history }}</p>
                        </div>
                    @endif
                </div>

                @if($character->notes)
                    <div class="mb-8">
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Notas Adicionais</h4>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <p class="text-gray-700 whitespace-pre-line">{{ $character->notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Timeline dos Atos -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Linha do Tempo</h4>
                    <div class="space-y-4">
                        @forelse($character->scenes->sortBy('order') as $scene)
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-white dark:bg-gray-700">
                                <div class="flex justify-between items-start mb-2">
                                    <h5 class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">{{ $scene->title }}</h5>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Duração: {{ $scene->duration }} minutos</span>
                                </div>
                                @if($scene->description)
                                    <p class="text-gray-700 dark:text-gray-300 mb-3 whitespace-pre-line">{{ $scene->description }}</p>
                                @endif
                                @if($scene->pivot->dialogue)
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded p-3 mt-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Diálogo:</p>
                                        <p class="text-gray-600 italic mt-1 whitespace-pre-line">"{{ $scene->pivot->dialogue }}"</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 italic">Este personagem ainda não participa de nenhuma cena.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex gap-4">
                    <a href="{{ route('characters.edit', ['character' => $character, 'project' => $project->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Editar Personagem
                    </a>
                    <form action="{{ route('characters.destroy', ['character' => $character, 'project' => $project->id]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Tem certeza que deseja excluir este personagem?')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Excluir Personagem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 