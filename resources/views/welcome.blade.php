<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RedFlags</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/svg+xml" href="./img/logo.svg">
</head>

<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white min-h-screen flex flex-col">

    <header class="p-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <img src="./img/logo.svg" alt="Red Flags Logo" class="w-12 h-12">
            <h1 class="text-3xl font-bold tracking-wide">Red Flags</h1>
        </div>
        <nav class="space-x-6 text-lg">
            <a href="#" class="hover:text-cyan-400 transition">Home</a>
            <a href="./about" class="hover:text-cyan-400 transition">About</a>
        </nav>
    </header>

    <main class="flex-1 container mx-auto px-6 pt-4" x-data="logsComponent()">
        <div class="flex space-x-4 border-b border-white/10 pb-2 mb-6">
            <button @click="tab='logs'" :class="tab==='logs' ? 'border-cyan-400 text-cyan-400' : 'border-transparent'"
                class="px-4 pb-2 border-b-2 font-semibold transition">Logs</button>
            <button @click="tab='analytics'"
                :class="tab==='analytics' ? 'border-cyan-400 text-cyan-400' : 'border-transparent'"
                class="px-4 pb-2 border-b-2 font-semibold transition">Analytics</button>
        </div>

        <!-- LOGS TAB -->
        <section x-show="tab==='logs'" class="space-y-6">
            <div class="flex items-center space-x-4">
                <input type="text" x-model="search" placeholder="Search logs..."
                    class="px-4 py-2 bg-white/10 rounded-lg w-80 focus:outline-none focus:ring-2 focus:ring-cyan-400" />

                <select x-model="limit"
                    class="px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    <template x-for="n in 10" :key="n">
                        <option :value="n*10" x-text="n*10" class="text-black bg-white"></option>
                    </template>
                </select>

                <!-- Severity -->
                <select x-model="severity"
                    class="px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    <option value="">All Severities</option>
                    <option value="CRITICAL">CRITICAL</option>
                    <option value="HIGH">HIGH</option>
                    <option value="MEDIUM">MEDIUM</option>
                    <option value="LOW">LOW</option>
                    <option value="INFO">INFO</option>
                </select>

                <!-- Log Type -->
                <select x-model="log_type"
                    class="px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    <option value="">All Types</option>
                    <option value="system">System</option>
                    <option value="web">Web</option>
                    <option value="application">Application</option>
                </select>

                <!-- Hours -->
                <input type="number" x-model="hours" min="1" placeholder="Last N hours"
                    class="px-3 py-2 bg-white text-black rounded-lg shadow-md w-24 focus:outline-none focus:ring-2 focus:ring-cyan-400" />

                <button @click="fetchLogs()"
                    class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-semibold transition">
                    Apply
                </button>
            </div>

            <!-- Logs Panel -->
            <div
                class="bg-white/5 backdrop-blur-lg rounded-xl shadow-xl p-6 h-[500px] overflow-y-auto border border-white/10">
                <template x-for="log in filteredLogs()" :key="log.id">
                    <div class="py-2 border-b border-white/10 flex flex-col md:flex-row md:items-center md:space-x-3"
                        :class="getLogStyle(log).bg">
                        <i :class="getLogStyle(log).icon" class="text-xl"></i>
                        <div class="flex-1">
                            <p class="text-white/90 font-medium" x-text="log.raw_log_message"></p>
                            <p class="text-white/50 text-sm mt-1 md:mt-0"
                                x-text="`Source: ${log.source_host} | Severity: ${log.severity}`"></p>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <!-- ANALYTICS TAB -->
        <section x-show="tab==='analytics'" class="animate-fade-in space-y-6" x-data="analyticsComponent()">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/5 p-6 rounded-xl border border-white/10 shadow">
                    <h3 class="text-lg font-semibold mb-3">Total Incidents</h3>
                    <p class="text-4xl font-bold text-cyan-400" x-text="stats.total_incidents || 0"></p>
                </div>
                <div class="bg-white/5 p-6 rounded-xl border border-white/10 shadow">
                    <h3 class="text-lg font-semibold mb-3">Incidents Last 24h</h3>
                    <p class="text-4xl font-bold text-red-400" x-text="stats.last_24h || 0"></p>
                </div>
                <div class="bg-white/5 p-6 rounded-xl border border-white/10 shadow">
                    <h3 class="text-lg font-semibold mb-3">Top Source Host</h3>
                    <p class="text-2xl font-bold text-yellow-300" x-text="stats.top_source_hosts?.[0]?.host || '-'"></p>
                    <p class="text-sm text-white/50" x-text="`Count: ${stats.top_source_hosts?.[0]?.count || 0}`"></p>
                </div>
            </div>

            <!-- By Severity -->
            <div class="bg-white/5 p-6 rounded-xl border border-white/10 shadow">
                <h3 class="text-lg font-semibold mb-4">Incidents by Severity</h3>
                <ul class="space-y-2">
                    <template x-for="(count, severity) in stats.by_severity" :key="severity">
                        <li class="flex justify-between">
                            <span x-text="severity"></span>
                            <span x-text="count"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- By Log Type -->
            <div class="bg-white/5 p-6 rounded-xl border border-white/10 shadow">
                <h3 class="text-lg font-semibold mb-4">Incidents by Log Type</h3>
                <ul class="space-y-2">
                    <template x-for="(count, logType) in stats.by_log_type" :key="logType">
                        <li class="flex justify-between">
                            <span x-text="logType"></span>
                            <span x-text="count"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer
        class="bg-white/5 dark:bg-gray-800 mt-10 py-6 border-t border-white/10 flex justify-between items-center px-6 rounded-t-xl">
        <p class="text-white/70">© <span id="year"></span> Red Flags Dashboard</p>
        <div class="flex items-center space-x-4">
            <img src="./img/logo-secureu-white.svg" alt="Logo 1" class="w-24 h-24">
            <img src="./img/ihu-logo-white.svg" alt="Logo 2" class="w-24 h-24">
        </div>
    </footer>

    <script>
        window.apiBaseUrl = "{{ config('api.base_url') }}";
        document.getElementById('year').textContent = new Date().getFullYear();
        document.addEventListener('alpine:init', () => {
            Alpine.data('logsComponent', () => ({
                tab: 'logs',
                logs: [],
                search: '',
                severity: '',
                log_type: '',
                hours: '',
                limit: 20,

                async fetchLogs() {
                    try {
                        let url = `${window.apiBaseUrl}/incidents?limit=${this.limit}&offset=0&x_api_key=-`;
                        if (this.severity) url += `&severity=${this.severity}`;
                        if (this.log_type) url += `&log_type=${this.log_type}`;
                        if (this.hours) url += `&hours=${this.hours}`;

                        const res = await fetch(url);
                        const data = await res.json();
                        this.logs = data.incidents || [];
                    } catch (e) {
                        console.error("Failed to fetch logs:", e);
                    }
                },

                filteredLogs() {
                    return this.logs.filter(l =>
                        (!this.search || l.raw_log_message.toLowerCase().includes(this.search.toLowerCase()))
                    );
                },

                getLogStyle(log) {
                    if (log.raw_log_message && log.raw_log_message.toLowerCase().includes('anomaly')) {
                        return { icon: 'ph ph-skull text-red-600', bg: 'bg-red-900/20' };
                    }

                    switch ((log.severity || '').toLowerCase()) {
                        case 'critical':
                        case 'high':
                            return { icon: 'ph ph-warning-circle text-red-400', bg: 'bg-red-800/20' };
                        case 'medium':
                            return { icon: 'ph ph-exclamation text-yellow-400', bg: 'bg-yellow-800/20' };
                        case 'low':
                        case 'info':
                            return { icon: 'ph ph-info text-cyan-400', bg: 'bg-cyan-800/20' };
                        default:
                            return { icon: 'ph ph-terminal text-green-400', bg: 'bg-green-800/20' };
                    }
                }
            }));
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('analyticsComponent', () => ({
                stats: {},
                async fetchStats() {
                    try {
                        const res = await fetch(`${window.apiBaseUrl}/statistics?x_api_key=-`);
                        const data = await res.json();
                        this.stats = data;
                    } catch (e) {
                        console.error("Failed to fetch statistics:", e);
                    }
                },
                init() {
                    this.fetchStats();
                }
            }));
        });
    </script>

</body>

</html>