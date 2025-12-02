<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - RedFlags</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="icon" type="image/svg+xml" href="./img/logo.svg">
</head>

<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white min-h-screen flex flex-col">

    <header class="p-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <img src="./img/logo.svg" alt="Red Flags Logo" class="w-24 h-24">
            <h1 class="text-3xl font-bold tracking-wide">Red Flags</h1>
        </div>
        <nav class="space-x-6 text-lg">
            <a href="./" class="hover:text-cyan-400 transition">Home</a>
            <a href="#" class="hover:text-cyan-400 transition text-cyan-400 border-b-2 border-cyan-400 pb-1">About</a>
        </nav>
    </header>

    <main class="flex-1 container mx-auto px-6 pt-10 max-w-3xl animate-fade-in">
        <div class="bg-white/5 backdrop-blur-lg rounded-xl shadow-xl p-10 border border-white/10 space-y-6">
            <h2 class="text-4xl font-extrabold text-cyan-400 tracking-wide">About This Project</h2>

            <p class="text-white/80 leading-relaxed text-lg">
                The Red Flags Dashboard is a modern UI built to communicate with an external Python-based
                analysis system via API calls. Its purpose is to help visualize logs, detect suspicious
                patterns, and present analytics in a clear and elegant interface.
            </p>

            <p class="text-white/70 leading-relaxed text-lg">
                Powered by Laravel on the frontend and a custom machine-learning backend, this platform
                provides a seamless and fast workflow for monitoring, reviewing, and understanding log data
                in real time.
            </p>

            <p class="text-white/60 italic">
                More features coming soon — including automated reports, detailed anomaly breakdowns,
                and real-time dashboards.
            </p>
        </div>
    </main>

    <!-- FOOTER -->
    <footer
        class="bg-white/5 dark:bg-gray-800 mt-10 py-6 border-t border-white/10 flex justify-between items-center px-6 rounded-t-xl">
        <p class="text-white/70">© 2025 Red Flags Dashboard</p>
        <div class="flex items-center space-x-4">
            <img src="./img/logo-secureu-white.svg" alt="Logo 1" class="w-24 h-24">
            <img src="./img/ihu-logo-white.svg" alt="Logo 2" class="w-24 h-24">
        </div>
    </footer>

</body>

</html>