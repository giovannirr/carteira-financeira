<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carteira Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Cabeçalho -->
    <header class="bg-[#5185A6] text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Carteira Digital</h1>
            <div class="space-x-4">
                <a href="/login" class="hover:underline">Entrar</a>
                <a href="/register" class="hover:underline">Registrar</a>
            </div>
        </div>
    </header>

    <!-- Conteúdo principal -->
    <main class="bg-gray-100 flex-grow flex flex-col items-center justify-center text-center px-4">
        <!-- SVG de gráfico de barras -->
        <div class="w-40 h-40 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#5185A6" class="w-full h-full">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13v5M11 9v9M15 5v13M19 11v7" />
            </svg>
        </div>

        <h2 class="text-3xl font-semibold text-gray-800 mb-2">Organize suas finanças com facilidade</h2>
        <p class="text-gray-600 text-lg mb-6 max-w-xl">Gerencie entradas, saídas e transferências com uma carteira digital eficiente e simples de usar.</p>
    </main>

    <!-- Rodapé -->
    <footer class="bg-[#5185A6] text-white text-center p-4">
        <p class="text-sm">&copy; {{ date('Y') }} Carteira Digital. Todos os direitos reservados.</p>
    </footer>

</body>
</html>