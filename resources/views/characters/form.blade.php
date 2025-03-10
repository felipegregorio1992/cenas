<x-app-layout>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ isset($character) ? 'Editar Personagem' : 'Novo Personagem' }}
                </h2>
            </div>

            <form action="{{ isset($character) ? route('characters.update', $character) : route('characters.store') }}" 
                  method="POST" 
                  class="space-y-6">
                @csrf
                @if(isset($character))
                    @method('PUT')
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $character->name ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           required>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                              required>{{ old('description', $character->description ?? '') }}</textarea>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Função/Papel</label>
                    <input type="text" 
                           name="role" 
                           id="role" 
                           value="{{ old('role', $character->role ?? '') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           required>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('characters.index') }}" 
                       class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ isset($character) ? 'Atualizar' : 'Criar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 