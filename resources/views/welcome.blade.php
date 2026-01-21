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
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .new-log {
            animation: slideIn 0.5s ease-out;
            background: linear-gradient(90deg, rgba(34, 211, 238, 0.2) 0%, transparent 100%);
        }

        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .log-row {
            transition: all 0.2s ease;
        }

        .log-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .log-expanded {
            background: rgba(34, 211, 238, 0.1);
        }
    </style>
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

    <main class="flex-1 container mx-auto px-6 pt-4" x-data="mainComponent()">
        <div class="flex space-x-4 border-b border-white/10 pb-2 mb-6">
            <button @click="tab='raw-logs'"
                :class="tab==='raw-logs' ? 'border-cyan-400 text-cyan-400' : 'border-transparent'"
                class="px-4 pb-2 border-b-2 font-semibold transition">Raw Logs</button>
            <button @click="tab='logs'" :class="tab==='logs' ? 'border-cyan-400 text-cyan-400' : 'border-transparent'"
                class="px-4 pb-2 border-b-2 font-semibold transition">Incident Logs</button>
            <button @click="tab='analytics'"
                :class="tab==='analytics' ? 'border-cyan-400 text-cyan-400' : 'border-transparent'"
                class="px-4 pb-2 border-b-2 font-semibold transition">Analytics</button>
        </div>

        <!-- INCIDENT LOGS TAB -->
        <section x-show="tab==='logs'" class="space-y-6" x-data="logsComponent()">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 flex-wrap gap-2">
                    <input type="text" x-model="search" placeholder="Search logs..."
                        class="px-4 py-2 bg-white/10 rounded-lg w-80 focus:outline-none focus:ring-2 focus:ring-cyan-400" />

                    <select x-model="limit"
                        class="px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        <template x-for="n in 10" :key="n">
                            <option :value="n*10" x-text="n*10" class="text-black bg-white"></option>
                        </template>
                    </select>

                    <select x-model="severity"
                        class="px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        <option value="">All Severities</option>
                        <option value="CRITICAL" style="color: #dc2626; font-weight: bold;">CRITICAL</option>
                        <option value="HIGH" style="color: #ea580c; font-weight: bold;">HIGH</option>
                        <option value="MEDIUM" style="color: #ca8a04; font-weight: bold;">MEDIUM</option>
                        <option value="LOW" style="color: #2563eb; font-weight: bold;">LOW</option>
                        <option value="INFO" style="color: #0891b2; font-weight: bold;">INFO</option>
                    </select>

                    <select x-model="log_type"
                        class="px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        <option value="">All Types</option>
                        <option value="system">System</option>
                        <option value="web">Web</option>
                        <option value="application">Application</option>
                    </select>

                    <input type="number" x-model="hours" min="1" placeholder="Last N hours"
                        class="px-3 py-2 bg-white text-black rounded-lg shadow-md w-24 focus:outline-none focus:ring-2 focus:ring-cyan-400" />

                    <button @click="applyFilters()"
                        class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg font-semibold transition">
                        Apply
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2">
                        <div :class="autoRefresh ? 'bg-green-500' : 'bg-gray-500'"
                            class="w-2 h-2 rounded-full pulse-dot"></div>
                        <span class="text-sm text-white/70" x-text="autoRefresh ? 'Live' : 'Paused'"></span>
                    </div>
                    <button @click="toggleAutoRefresh()"
                        :class="autoRefresh ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600'"
                        class="px-3 py-1 text-white text-sm rounded-lg font-semibold transition">
                        <span x-text="autoRefresh ? 'Pause' : 'Resume'"></span>
                    </button>
                    <span class="text-xs text-white/50" x-text="`Updated ${lastUpdate}`"></span>
                </div>
            </div>

            <div x-show="newLogsCount > 0"
                class="bg-cyan-500/20 border border-cyan-400 rounded-lg p-3 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="ph ph-bell-ringing text-cyan-400 text-xl"></i>
                    <span x-text="`${newLogsCount} new log${newLogsCount > 1 ? 's' : ''} received`"></span>
                </div>
                <button @click="scrollToTop()"
                    class="px-3 py-1 bg-cyan-500 hover:bg-cyan-600 rounded text-sm transition">
                    View
                </button>
            </div>

            <div x-ref="logsContainer"
                class="bg-white/5 backdrop-blur-lg rounded-xl shadow-xl p-6 h-[500px] overflow-y-auto border border-white/10">
                <template x-for="log in filteredLogs()" :key="log.id">
                    <div class="py-2 border-b border-white/10 flex flex-col md:flex-row md:items-center md:space-x-3 cursor-pointer hover:bg-white/5 transition"
                        :class="[getLogStyle(log).bg, isNewLog(log) ? 'new-log' : '', selectedLog?.id === log.id ? 'ring-2 ring-cyan-400' : '']"
                        @click="selectLog(log)">
                        <i :class="getLogStyle(log).icon" class="text-xl"></i>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <p class="text-white/90 font-medium" x-text="log.raw_log_message"></p>
                                <span x-show="isNewLog(log)"
                                    class="px-2 py-0.5 bg-cyan-500 text-white text-xs rounded-full">NEW</span>
                            </div>
                            <p class="text-white/50 text-sm mt-1 md:mt-0"
                                x-text="`Source: ${log.source_host} | Severity: ${log.severity} | ${formatTime(log.event_timestamp)}`">
                            </p>
                        </div>
                        <i class="ph ph-caret-right text-cyan-400 text-lg"></i>
                    </div>
                </template>

                <div x-show="filteredLogs().length === 0" class="text-center text-white/50 py-8">
                    <i class="ph ph-magnifying-glass text-4xl mb-2"></i>
                    <p>No logs found</p>
                </div>
            </div>

            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed right-0 top-0 h-full w-full md:w-2/3 lg:w-1/2 bg-slate-900 shadow-2xl z-50 overflow-y-auto slide-in-right"
                @click.away="closeSidebar()">

                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <h2 class="text-2xl font-bold text-cyan-400">Incident Details</h2>
                        <button @click="closeSidebar()" class="text-white/70 hover:text-white transition">
                            <i class="ph ph-x text-2xl"></i>
                        </button>
                    </div>

                    <template x-if="selectedLog">
                        <div class="space-y-6">
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <h3 class="text-sm font-semibold text-white/70 mb-3 uppercase tracking-wide">Basic
                                    Information</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-white/50">Incident ID:</span>
                                        <span class="text-white font-mono" x-text="selectedLog.id"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-white/50">Created At:</span>
                                        <span class="text-white" x-text="formatTime(selectedLog.created_at)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-white/50">Event Timestamp:</span>
                                        <span class="text-white"
                                            x-text="formatTime(selectedLog.event_timestamp)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <h3 class="text-sm font-semibold text-white/70 mb-3 uppercase tracking-wide">Log
                                    Information</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-white/50">Log Type:</span>
                                        <span
                                            class="px-3 py-1 bg-blue-500/20 text-blue-300 text-sm rounded border border-blue-400"
                                            x-text="selectedLog.log_type"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-white/50">Source Host:</span>
                                        <span
                                            class="px-3 py-1 bg-purple-500/20 text-purple-300 text-sm rounded border border-purple-400"
                                            x-text="selectedLog.source_host"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-white/50">Severity:</span>
                                        <span class="px-3 py-1 text-sm rounded border"
                                            :class="getSeverityColor(selectedLog.severity)"
                                            x-text="selectedLog.severity"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <h3 class="text-sm font-semibold text-white/70 mb-3 uppercase tracking-wide">Raw Log
                                    Message</h3>
                                <p class="text-white bg-black/30 p-3 rounded font-mono text-sm break-words"
                                    x-text="selectedLog.raw_log_message"></p>
                            </div>

                            <template x-if="selectedLog.analysis_result">
                                <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                    <h3 class="text-sm font-semibold text-white/70 mb-3 uppercase tracking-wide">
                                        Analysis Result</h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-start">
                                            <span class="text-white/50">Event Type:</span>
                                            <span class="px-3 py-1 text-sm rounded border"
                                                :class="getEventTypeColor(selectedLog.analysis_result.event_type)"
                                                x-text="selectedLog.analysis_result.event_type || 'N/A'"></span>
                                        </div>
                                        <div>
                                            <span class="text-white/50 block mb-2">Description:</span>
                                            <p class="text-white bg-black/30 p-3 rounded text-sm"
                                                x-text="selectedLog.analysis_result.description || 'No description available'">
                                            </p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-white/10">
                                            <div>
                                                <span class="text-white/50 text-xs block mb-1">Hostname:</span>
                                                <span class="text-white text-sm"
                                                    x-text="selectedLog.analysis_result.hostname || 'N/A'"></span>
                                            </div>
                                            <div>
                                                <span class="text-white/50 text-xs block mb-1">Service:</span>
                                                <span class="text-white text-sm"
                                                    x-text="selectedLog.analysis_result.service || 'N/A'"></span>
                                            </div>
                                            <div>
                                                <span class="text-white/50 text-xs block mb-1">User:</span>
                                                <span class="text-white text-sm"
                                                    x-text="selectedLog.analysis_result.user || 'N/A'"></span>
                                            </div>
                                            <div>
                                                <span class="text-white/50 text-xs block mb-1">Source IP:</span>
                                                <span class="text-white text-sm"
                                                    x-text="selectedLog.analysis_result.source_ip || 'N/A'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <h3 class="text-sm font-semibold text-white/70 mb-3 uppercase tracking-wide">Full JSON
                                    Data</h3>
                                <pre
                                    class="bg-black/30 p-3 rounded text-xs text-green-300 font-mono overflow-x-auto max-h-64 overflow-y-auto"><code x-text="JSON.stringify(selectedLog, null, 2)"></code></pre>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="closeSidebar()" class="fixed inset-0 bg-black/50 z-40"></div>
        </section>

        <!-- RAW LOGS TAB -->
        <section x-show="tab==='raw-logs'" class="space-y-6" x-data="rawLogsComponent()">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white/5 backdrop-blur-lg rounded-xl shadow-xl p-6 border border-white/10 space-y-4">
                        <h3 class="text-xl font-bold text-cyan-400 mb-4">Filters</h3>

                        <div>
                            <label class="block text-sm font-medium text-white/70 mb-2">Number of recent logs</label>
                            <select x-model="n"
                                class="w-full px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white/70 mb-2">Log Type</label>
                            <select x-model="logType"
                                class="w-full px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                <option value="">All Types</option>
                                <option value="system">System Logs</option>
                                <option value="web">Web Logs</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white/70 mb-2">Source Host</label>
                            <select x-model="sourceHost"
                                class="w-full px-3 py-2 bg-white text-black rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                <option value="">All Hosts</option>
                                <template x-for="host in availableHosts" :key="host">
                                    <option :value="host" x-text="host"></option>
                                </template>
                            </select>
                        </div>

                        <button @click="fetchRawLogs()" :disabled="loading"
                            class="w-full px-4 py-2 bg-cyan-500 hover:bg-cyan-600 disabled:bg-gray-500 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition">
                            <i :class="loading ? 'ph ph-spinner animate-spin' : 'ph ph-funnel'" class="mr-2"></i>
                            <span x-text="loading ? 'Loading...' : 'Apply Filters'"></span>
                        </button>

                        <button @click="exportToJSON()" :disabled="rawLogs.length === 0"
                            class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 disabled:bg-gray-500 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition">
                            <i class="ph ph-download-simple mr-2"></i>Export JSON
                        </button>

                        <div class="pt-4 border-t border-white/10">
                            <p class="text-sm text-white/50">Logs loaded: <span class="text-cyan-400 font-bold"
                                    x-text="rawLogs.length"></span></p>
                            <p class="text-sm text-white/50 mt-1">Last updated: <span class="text-white/70"
                                    x-text="lastUpdated"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Main content area -->
                <div class="lg:col-span-3">
                    <div class="bg-white/5 backdrop-blur-lg rounded-xl shadow-xl p-6 border border-white/10">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">Raw Log Messages</h3>
                            <div class="flex items-center space-x-2">
                                <button @click="viewMode = 'table'"
                                    :class="viewMode === 'table' ? 'bg-cyan-500' : 'bg-white/10'"
                                    class="px-3 py-1 rounded-lg transition">
                                    <i class="ph ph-table"></i>
                                </button>
                                <button @click="viewMode = 'json'"
                                    :class="viewMode === 'json' ? 'bg-cyan-500' : 'bg-white/10'"
                                    class="px-3 py-1 rounded-lg transition">
                                    <i class="ph ph-code"></i>
                                </button>
                            </div>
                        </div>

                        <div x-show="viewMode === 'table'" class="h-[600px] overflow-y-auto">
                            <div x-show="error" class="bg-red-500/20 border border-red-400 rounded-lg p-4 mb-4">
                                <div class="flex items-center space-x-2">
                                    <i class="ph ph-warning-circle text-red-400 text-xl"></i>
                                    <span class="text-red-300" x-text="error"></span>
                                </div>
                            </div>

                            <div x-show="loading" class="text-center text-white/50 py-12">
                                <i class="ph ph-spinner text-5xl mb-3 animate-spin"></i>
                                <p class="text-lg">Loading logs...</p>
                            </div>

                            <div x-show="!loading">
                                <template x-for="(log, index) in rawLogs" :key="index">
                                    <div class="log-row border-b border-white/10 py-3 cursor-pointer"
                                        :class="expandedLog === index ? 'log-expanded' : ''"
                                        @click="toggleExpand(index)">
                                        <div class="flex items-start space-x-3">
                                            <i :class="expandedLog === index ? 'ph-caret-down' : 'ph-caret-right'"
                                                class="ph text-cyan-400 text-lg mt-1"></i>
                                            <div class="flex-1">
                                                <p class="text-white font-medium" x-text="log.message"></p>

                                                <div x-show="expandedLog === index" x-transition
                                                    class="mt-3 space-y-2 pl-4 border-l-2 border-cyan-400">
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded">
                                                            <i class="ph ph-tag mr-1"></i><span
                                                                x-text="log.log_type"></span>
                                                        </span>
                                                        <span
                                                            class="px-2 py-1 bg-purple-500/20 text-purple-300 text-xs rounded">
                                                            <i class="ph ph-hard-drives mr-1"></i><span
                                                                x-text="log.source_host"></span>
                                                        </span>
                                                    </div>
                                                    <p class="text-white/50 text-sm">
                                                        <i class="ph ph-file-text mr-1"></i>
                                                        <span x-text="log.file_path"></span>
                                                    </p>
                                                    <p class="text-white/50 text-sm">
                                                        <i class="ph ph-clock mr-1"></i>
                                                        <span x-text="formatTime(log.timestamp)"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="rawLogs.length === 0 && !loading && !error"
                                    class="text-center text-white/50 py-12">
                                    <i class="ph ph-database text-5xl mb-3"></i>
                                    <p class="text-lg">No logs loaded</p>
                                    <p class="text-sm mt-2">Apply filters to fetch raw logs</p>
                                </div>
                            </div>
                        </div>

                        <div x-show="viewMode === 'json'" class="h-[600px] overflow-y-auto">
                            <pre
                                class="bg-black/30 p-4 rounded-lg text-sm text-green-300 font-mono"><code x-text="JSON.stringify(getJSONExport(), null, 2)"></code></pre>
                        </div>
                    </div>
                </div>
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
            <img src="./img/ihu-logo-white.svg" alt="Logo 2" class="w-24 h-24">
        </div>
    </footer>

    <script>
        window.apiBaseUrl = "{{ config('api.base_url') }}";
        document.getElementById('year').textContent = new Date().getFullYear();

        document.addEventListener('alpine:init', () => {
            Alpine.data('mainComponent', () => ({
                tab: 'logs',
                init() {
                    this.$watch('tab', (value) => {
                        window.currentTab = value;
                    });
                    window.currentTab = this.tab;
                }
            }));

            Alpine.data('logsComponent', () => ({
                logs: [],
                search: '',
                severity: '',
                log_type: '',
                hours: '',
                limit: 20,
                autoRefresh: true,
                refreshInterval: null,
                lastUpdate: 'never',
                newLogsCount: 0,
                newLogIds: new Set(),
                selectedLog: null,
                sidebarOpen: false,
                selectedLog: null,
                sidebarOpen: false,

                init() {
                    this.fetchLogs();
                    this.startAutoRefresh();

                    this.$watch('$el', () => {
                        return () => {
                            if (this.refreshInterval) {
                                clearInterval(this.refreshInterval);
                            }
                        };
                    });
                },

                async fetchLogs() {
                    try {
                        let url = `${window.apiBaseUrl}/incidents?limit=${this.limit}&offset=0&x_api_key=-`;
                        if (this.severity) url += `&severity=${this.severity}`;
                        if (this.log_type) url += `&log_type=${this.log_type}`;
                        if (this.hours) url += `&hours=${this.hours}`;

                        const res = await fetch(url);
                        const data = await res.json();
                        const newLogs = data.incidents || [];

                        if (this.logs.length > 0) {
                            const currentIds = new Set(this.logs.map(l => l.id));
                            this.newLogIds = new Set();
                            this.newLogsCount = 0;

                            newLogs.forEach(log => {
                                if (!currentIds.has(log.id)) {
                                    this.newLogIds.add(log.id);
                                    this.newLogsCount++;
                                }
                            });
                        }

                        this.logs = newLogs;
                        this.lastUpdate = new Date().toLocaleTimeString();

                        setTimeout(() => {
                            this.newLogIds.clear();
                            this.newLogsCount = 0;
                        }, 10000);
                    } catch (e) {
                        console.error("Failed to fetch logs:", e);
                    }
                },

                applyFilters() {
                    this.newLogIds.clear();
                    this.newLogsCount = 0;
                    this.fetchLogs();
                },

                startAutoRefresh() {
                    if (this.refreshInterval) {
                        clearInterval(this.refreshInterval);
                    }
                    this.refreshInterval = setInterval(() => {
                        if (this.autoRefresh && window.currentTab === 'logs') {
                            this.fetchLogs();
                        }
                    }, 5000);
                },

                toggleAutoRefresh() {
                    this.autoRefresh = !this.autoRefresh;
                    if (this.autoRefresh) {
                        this.fetchLogs();
                    }
                },

                filteredLogs() {
                    return this.logs.filter(l =>
                        (!this.search || l.raw_log_message.toLowerCase().includes(this.search.toLowerCase()))
                    );
                },

                isNewLog(log) {
                    return this.newLogIds.has(log.id);
                },

                scrollToTop() {
                    this.$refs.logsContainer.scrollTop = 0;
                    this.newLogsCount = 0;
                },

                selectLog(log) {
                    this.selectedLog = log;
                    this.sidebarOpen = true;
                },

                closeSidebar() {
                    this.sidebarOpen = false;
                    setTimeout(() => {
                        this.selectedLog = null;
                    }, 300);
                },

                getSeverityColor(severity) {
                    switch ((severity || '').toLowerCase()) {
                        case 'critical':
                            return 'bg-red-500/20 text-red-300 border-red-400';
                        case 'high':
                            return 'bg-orange-500/20 text-orange-300 border-orange-400';
                        case 'medium':
                            return 'bg-yellow-500/20 text-yellow-300 border-yellow-400';
                        case 'low':
                            return 'bg-blue-500/20 text-blue-300 border-blue-400';
                        case 'info':
                            return 'bg-cyan-500/20 text-cyan-300 border-cyan-400';
                        default:
                            return 'bg-gray-500/20 text-gray-300 border-gray-400';
                    }
                },

                getEventTypeColor(eventType) {
                    switch ((eventType || '').toLowerCase()) {
                        case 'normal':
                            return 'bg-green-500/20 text-green-300 border-green-400';
                        case 'system_error':
                        case 'error':
                            return 'bg-red-500/20 text-red-300 border-red-400';
                        case 'warning':
                            return 'bg-yellow-500/20 text-yellow-300 border-yellow-400';
                        case 'anomaly':
                            return 'bg-purple-500/20 text-purple-300 border-purple-400';
                        default:
                            return 'bg-blue-500/20 text-blue-300 border-blue-400';
                    }
                },

                selectLog(log) {
                    this.selectedLog = log;
                    this.sidebarOpen = true;
                },

                closeSidebar() {
                    this.sidebarOpen = false;
                    setTimeout(() => {
                        this.selectedLog = null;
                    }, 300);
                },

                getSeverityColor(severity) {
                    switch ((severity || '').toLowerCase()) {
                        case 'critical':
                            return 'bg-red-500/20 text-red-300 border-red-400';
                        case 'high':
                            return 'bg-orange-500/20 text-orange-300 border-orange-400';
                        case 'medium':
                            return 'bg-yellow-500/20 text-yellow-300 border-yellow-400';
                        case 'low':
                            return 'bg-blue-500/20 text-blue-300 border-blue-400';
                        case 'info':
                            return 'bg-cyan-500/20 text-cyan-300 border-cyan-400';
                        default:
                            return 'bg-gray-500/20 text-gray-300 border-gray-400';
                    }
                },

                getEventTypeColor(eventType) {
                    switch ((eventType || '').toLowerCase()) {
                        case 'normal':
                            return 'bg-green-500/20 text-green-300 border-green-400';
                        case 'system_error':
                        case 'error':
                            return 'bg-red-500/20 text-red-300 border-red-400';
                        case 'warning':
                            return 'bg-yellow-500/20 text-yellow-300 border-yellow-400';
                        case 'anomaly':
                            return 'bg-purple-500/20 text-purple-300 border-purple-400';
                        default:
                            return 'bg-blue-500/20 text-blue-300 border-blue-400';
                    }
                },

                formatTime(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    return date.toLocaleString();
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

            Alpine.data('rawLogsComponent', () => ({
                rawLogs: [],
                n: 100,
                logType: '',
                sourceHost: '',
                availableHosts: [],
                expandedLog: null,
                viewMode: 'table',
                lastUpdated: 'never',
                loading: false,
                error: null,

                init() {
                    this.fetchHostsStats();
                },

                async fetchHostsStats() {
                    try {
                        const res = await fetch(`${window.apiBaseUrl}/raw-logs/stats?x_api_key=-`);
                        const data = await res.json();
                        this.availableHosts = data.hosts || [];
                    } catch (e) {
                        console.error("Failed to fetch hosts stats:", e);
                    }
                },

                async fetchRawLogs() {
                    try {
                        let url = `${window.apiBaseUrl}/raw-logs/recent?n=${this.n}&x_api_key=-`;
                        if (this.logType) url += `&log_type=${this.logType}`;
                        if (this.sourceHost) url += `&source_host=${this.sourceHost}`;

                        const res = await fetch(url);
                        const data = await res.json();

                        this.rawLogs = (data.logs || []).map(item => ({
                            message: item.message || '',
                            log_type: item.fields?.log_type || 'N/A',
                            source_host: item.fields?.source_host || 'N/A',
                            file_path: item.log?.file?.path || 'N/A',
                            timestamp: item['@timestamp'] || ''
                        }));

                        this.lastUpdated = new Date().toLocaleTimeString();
                        this.expandedLog = null;
                    } catch (e) {
                        console.error("Failed to fetch raw logs:", e);
                    }
                },

                toggleExpand(index) {
                    this.expandedLog = this.expandedLog === index ? null : index;
                },

                formatTime(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    return date.toLocaleString();
                },

                getJSONExport() {
                    return this.rawLogs.map(log => ({
                        "Message": log.message,
                        "Log type": log.log_type,
                        "Source Host": log.source_host,
                        "Collection time": log.timestamp
                    }));
                },

                exportToJSON() {
                    const exportData = this.getJSONExport();
                    const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `raw-logs-export-${new Date().toISOString()}.json`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }
            }));

            Alpine.data('analyticsComponent', () => ({
                stats: {},
                async fetchStats() {
                    try {
                        const res = await fetch(`${window.apiBaseUrl}/statistics?x_api_key=-`);
                        const data = await res.json();
                        this.stats = data || {};
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