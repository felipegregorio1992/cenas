<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Criar Personagem') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Projeto: {{ $project->name }}</p>
            </div>
            <a href="{{ route('characters.index', ['project' => $project->id]) }}" 
               class="text-gray-600 hover:text-gray-900">
                Voltar para Lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('characters.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <!-- Informações Básicas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" value="Nome" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                             :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="role" value="Papel" />
                                <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Protagonista" {{ old('role') === 'Protagonista' ? 'selected' : '' }}>Protagonista</option>
                                    <option value="Antagonista" {{ old('role') === 'Antagonista' ? 'selected' : '' }}>Antagonista</option>
                                    <option value="Mentor" {{ old('role') === 'Mentor' ? 'selected' : '' }}>Mentor</option>
                                    <option value="Aliado" {{ old('role') === 'Aliado' ? 'selected' : '' }}>Aliado</option>
                                    <option value="Personagem" {{ old('role', 'Personagem') === 'Personagem' ? 'selected' : '' }}>Personagem</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="type" value="Tipo" />
                                <x-text-input id="type" name="type" type="text" class="mt-1 block w-full" 
                                             :value="old('type')" placeholder="Ex: Humano, Elfo, Androide..." />
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div>
                            <x-input-label for="description" value="Descrição" />
                            <textarea id="description" name="description" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      rows="3" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Objetivos e Medos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="goals" value="Objetivos" />
                                <textarea id="goals" name="goals" 
                                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          rows="3" placeholder="O que o personagem deseja alcançar?">{{ old('goals') }}</textarea>
                                <x-input-error :messages="$errors->get('goals')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="fears" value="Medos" />
                                <textarea id="fears" name="fears" 
                                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          rows="3" placeholder="Do que o personagem tem medo?">{{ old('fears') }}</textarea>
                                <x-input-error :messages="$errors->get('fears')" class="mt-2" />
                            </div>
                        </div>

                        <!-- História e Personalidade -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="history" value="História" />
                                <textarea id="history" name="history" 
                                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          rows="4" placeholder="Passado e background do personagem">{{ old('history') }}</textarea>
                                <x-input-error :messages="$errors->get('history')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="personality" value="Personalidade" />
                                <textarea id="personality" name="personality" 
                                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          rows="4" placeholder="Traços de personalidade, comportamento">{{ old('personality') }}</textarea>
                                <x-input-error :messages="$errors->get('personality')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Notas Adicionais -->
                        <div>
                            <x-input-label for="notes" value="Notas Adicionais" />
                            <textarea id="notes" name="notes" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      rows="3" placeholder="Outras informações relevantes">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Criar') }}</x-primary-button>
                            <a href="{{ route('characters.index', ['project' => $project->id]) }}" class="text-gray-600 hover:text-gray-900">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 