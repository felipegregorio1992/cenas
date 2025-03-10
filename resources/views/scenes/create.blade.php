<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Criar Cena') }}
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
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('scenes.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div>
                            <x-input-label for="title" value="Título" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" 
                                         :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Descrição" />
                            <textarea id="description" name="description" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      rows="3" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="duration" value="Duração (minutos)" />
                            <x-text-input id="duration" name="duration" type="number" class="mt-1 block w-full" 
                                         :value="old('duration')" required min="1" />
                            <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="order" value="Ordem" />
                            <x-text-input id="order" name="order" type="number" class="mt-1 block w-full" 
                                         :value="old('order')" required min="1" />
                            <x-input-error :messages="$errors->get('order')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="characters" value="Personagens" />
                            <div class="space-y-4">
                                @foreach($characters as $character)
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-start gap-4">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" 
                                                       name="characters[]" 
                                                       value="{{ $character->id }}"
                                                       id="character_{{ $character->id }}"
                                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            </div>
                                            
                                            <div class="flex-grow">
                                                <label for="character_{{ $character->id }}" class="font-medium text-gray-700">
                                                    {{ $character->name }}
                                                    <span class="text-sm text-gray-500">({{ $character->role }})</span>
                                                </label>
                                                
                                                <div class="mt-2">
                                                    <label for="dialogue_{{ $character->id }}" class="sr-only">Diálogo</label>
                                                    <textarea name="dialogues[{{ $character->id }}]" 
                                                              id="dialogue_{{ $character->id }}"
                                                              rows="2"
                                                              placeholder="Diálogo do personagem nesta cena..."
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old("dialogues.$character->id") }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('characters')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Criar') }}</x-primary-button>
                            <a href="{{ route('scenes.index', ['project' => $project->id]) }}" class="text-gray-600 hover:text-gray-900">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 