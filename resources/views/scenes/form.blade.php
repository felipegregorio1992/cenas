<x-app-layout>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ isset($scene) ? 'Editar Cena' : 'Nova Cena' }}
                </h2>
            </div>

            <form action="{{ isset($scene) ? route('scenes.update', $scene) : route('scenes.store') }}" 
                  method="POST" 
                  class="space-y-6">
                @csrf
                @if(isset($scene))
                    @method('PUT')
                @endif

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $scene->title ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           required>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                              required>{{ old('description', $scene->description ?? '') }}</textarea>
                </div>

                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700">Duração (minutos)</label>
                    <input type="number" 
                           name="duration" 
                           id="duration" 
                           value="{{ old('duration', $scene->duration ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           required
                           min="1">
                </div>

                <div>
                    <label for="characters" class="block text-sm font-medium text-gray-700">Personagens</label>
                    <select name="characters[]" 
                            id="characters" 
                            multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}"
                                    {{ isset($scene) && $scene->characters->contains($character->id) ? 'selected' : '' }}>
                                {{ $character->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-sm text-gray-500">
                        Pressione Ctrl (Windows) ou Command (Mac) para selecionar múltiplos personagens
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('scenes.index') }}" 
                       class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ isset($scene) ? 'Atualizar' : 'Criar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 