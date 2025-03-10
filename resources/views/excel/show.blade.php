<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Linha do Tempo') }} - {{ $excelData->file_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4 flex justify-between items-center">
                        <a href="{{ route('excel.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Voltar') }}
                        </a>
                        <div class="text-sm text-gray-500">
                            Total de Personagens: {{ count($timelineMatrix) }} | 
                            Total de Atos: {{ $maxActs }}
                        </div>
                    </div>

                    @if(empty($timelineMatrix))
                        <p class="text-gray-500">Nenhum dado encontrado no arquivo.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="sticky left-0 z-10 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                            Personagem
                                        </th>
                                        @for($i = 1; $i <= $maxActs; $i++)
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                                Ato {{ $i }}
                                            </th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($timelineMatrix as $characterId => $character)
                                        <tr class="hover:bg-gray-50">
                                            <td class="sticky left-0 z-10 bg-white px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">
                                                {{ $character['name'] }}
                                            </td>
                                            @for($i = 1; $i <= $maxActs; $i++)
                                                <td class="px-6 py-4 text-sm text-gray-500 border-r">
                                                    @if(isset($character['acts'][$i]) && !empty($character['acts'][$i]))
                                                        <div class="whitespace-pre-line max-h-48 overflow-y-auto">
                                                            {{ $character['acts'][$i] }}
                                                        </div>
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .overflow-x-auto {
            overflow-x: auto;
            max-width: 100vw;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #e5e7eb;
            min-width: 200px;
            max-width: 400px;
            vertical-align: top;
        }
        .sticky {
            position: sticky;
            background-color: white;
            z-index: 1;
        }
        .whitespace-pre-line {
            white-space: pre-line;
        }
        .max-h-48 {
            max-height: 12rem;
        }
        .overflow-y-auto {
            overflow-y: auto;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover td {
            background-color: #f3f4f6;
        }
        td.sticky:hover {
            background-color: #f3f4f6 !important;
        }
    </style>
</x-app-layout> 