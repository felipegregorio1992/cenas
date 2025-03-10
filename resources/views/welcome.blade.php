<!DOCTYPE html>
<html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linha do Tempo - Sua Plataforma de Escrita Criativa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
            <style>
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
            </style>
    </head>
<body class="font-poppins">
    <!-- Navegação -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-playfair font-bold text-gray-800">Linha do Tempo</h1>
                </div>
                <div class="flex items-center space-x-4">
            @if (Route::has('login'))
                    @auth
                            <a href="{{ url('/dashboard') }}" class="text-indigo-600 hover:text-indigo-800">Dashboard</a>
                    @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Entrar</a>
                        @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">Registrar</a>
                        @endif
                    @endauth
                    @endif
                </div>
            </div>
        </div>
                </nav>

    <!-- Hero Section -->
    <div class="hero-gradient">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h2 class="text-4xl md:text-6xl font-playfair font-bold text-white mb-6">
                    Transforme suas histórias em obras-primas
                </h2>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                    Uma plataforma completa para escritores organizarem suas histórias, personagens e cenas de forma intuitiva e profissional.
                </p>
                <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-gray-100 transition duration-300 inline-block">
                    Comece sua história agora
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-playfair font-bold text-gray-900 mb-4">
                    Recursos para impulsionar sua criatividade
                </h3>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Tudo que você precisa para transformar suas ideias em histórias incríveis.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-xl shadow-sm feature-card transition duration-300">
                    <div class="text-indigo-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">Organização de Cenas</h4>
                    <p class="text-gray-600">Organize suas cenas de forma intuitiva, com uma linha do tempo visual e fácil de gerenciar.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-xl shadow-sm feature-card transition duration-300">
                    <div class="text-indigo-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">Gerenciamento de Personagens</h4>
                    <p class="text-gray-600">Crie perfis detalhados para seus personagens e acompanhe suas jornadas ao longo da história.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-xl shadow-sm feature-card transition duration-300">
                    <div class="text-indigo-600 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">Importação de Roteiros</h4>
                    <p class="text-gray-600">Importe seus roteiros do Excel e organize-os automaticamente em nossa plataforma.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-700 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-3xl font-playfair font-bold text-white mb-4">
                Pronto para começar sua jornada?
            </h3>
            <p class="text-white/90 mb-8 max-w-2xl mx-auto">
                Junte-se a outros escritores e comece a dar vida às suas histórias hoje mesmo.
            </p>
            <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-gray-100 transition duration-300 inline-block">
                Criar conta gratuita
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-playfair text-xl font-bold mb-4">Linha do Tempo</h4>
                    <p class="text-gray-400">Sua plataforma para criação de histórias incríveis.</p>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Recursos</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Organização de Cenas</a></li>
                        <li><a href="#" class="hover:text-white">Personagens</a></li>
                        <li><a href="#" class="hover:text-white">Importação de Roteiros</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Suporte</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Documentação</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                        <li><a href="#" class="hover:text-white">Contato</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Legal</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                        <li><a href="#" class="hover:text-white">Privacidade</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Linha do Tempo. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
    </body>
</html>
