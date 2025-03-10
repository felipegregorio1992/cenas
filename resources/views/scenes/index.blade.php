<x-app-layout>
    @isset($header)
        <x-slot name="header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Cenas') }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">{{ $project->name }}</p>
                    </div>
                    <a href="{{ route('scenes.create', ['project' => $project->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nova Cena
                    </a>
                </div>
            </div>
        </x-slot>
    @endisset

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 animate-fade-in-down">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(empty($acts))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma cena encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando uma nova cena para seu roteiro.</p>
                            <div class="mt-6">
                                <a href="{{ route('scenes.create', ['project' => $project->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Criar Cena
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($acts as $actNumber => $act)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm font-medium">
                                            {{ $actNumber }}
                                        </span>
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $act['title'] }}</h3>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                        {{ count($act['scenes']) }} cena(s)
                                    </span>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-200">
                                @foreach($act['scenes'] as $scene)
                                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex items-start justify-between">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="text-lg font-medium text-indigo-600">
                                                        <a href="{{ route('scenes.show', ['scene' => $scene['id'], 'project' => $project->id]) }}" 
                                                           class="hover:text-indigo-800 hover:underline">
                                                            {{ $scene['title'] }}
                                                        </a>
                                                    </h4>
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                                        {{ $scene['duration'] }} min
                                                    </span>
                                                </div>

                                                @if($scene['description'])
                                                    <p class="mt-2 text-sm text-gray-600 prose-content line-clamp-2">{{ $scene['description'] }}</p>
                                                @endif

                                                @if(!empty($scene['characters']))
                                                    <div class="mt-3">
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach($scene['characters'] as $character)
                                                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">
                                                                    {{ $character['name'] }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                        @if(collect($scene['characters'])->some(fn($char) => !empty($char['dialogue'])))
                                                            <div class="mt-3 pl-4 border-l-2 border-gray-200 space-y-3">
                                                                @foreach($scene['characters'] as $character)
                                                                    @if(!empty($character['dialogue']))
                                                                        <div class="relative">
                                                                            <div class="text-sm prose-content">
                                                                                <span class="font-medium text-gray-900">{{ $character['name'] }}</span>
                                                                                <p class="mt-0.5 text-gray-600 italic">
                                                                                    "{{ $character['dialogue'] }}"
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="ml-4 flex flex-shrink-0 gap-2">
                                                <a href="{{ route('scenes.edit', ['scene' => $scene['id'], 'project' => $project->id]) }}" 
                                                   class="rounded-md bg-white p-1.5 text-gray-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('scenes.destroy', ['scene' => $scene['id'], 'project' => $project->id]) }}" 
                                                      method="POST" 
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('Tem certeza que deseja excluir esta cena?')"
                                                            class="rounded-md bg-white p-1.5 text-gray-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<style>
    @keyframes fade-in-down {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-down {
        animation: fade-in-down 0.5s ease-out;
    }
</style> 