<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance & Absence Reporting - SIATRACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .slanted-pill { transform: skewX(-20deg); }
    </style>
</head>

<body class="bg-[#fcfbfb] text-gray-800 antialiased min-h-screen flex">

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="w-64 bg-white border-r border-red-100 flex flex-col justify-between shrink-0 h-screen sticky top-0">
        <div>
            <div class="p-6 pb-5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tight text-[#b91c1c]">SIATRACK</span>
                    <i class="fa-solid fa-wifi rotate-45 text-[#b91c1c] text-lg"></i>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">
                    Southern Isabela Academy
                </p>
            </div>

            <nav class="p-4 space-y-2 text-sm font-semibold">
                <a href="{{ route('teacher.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-users text-sm"></i>
                    <span>Class Attendance View</span>
                </a>

                <!-- Active Item -->
                <a href="{{ route('teacher.absence.reporting') }}" 
                   class="flex items-center gap-3 px-4 py-3 bg-[#cf2e2e] text-white rounded-2xl shadow-sm transition">
                    <i class="fa-solid fa-file-invoice text-sm"></i>
                    <span>Absence Reporting</span>
                </a>

                <a href="{{ route('teacher.evaluation.report') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-chart-simple text-sm text-gray-500"></i>
                    <span>Evaluation Report View</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50/60">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs border border-red-200">
                        {{ strtoupper(substr(auth()->user()->first_name ?? 'F', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-gray-800 truncate">
                            {{ auth()->user()->first_name ?? 'Faculty' }} {{ auth()->user()->last_name ?? 'Member' }}
                        </p>
                        <p class="text-[10px] text-red-600 font-bold uppercase tracking-wider">Faculty</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="text-gray-400 hover:text-red-600 p-1.5 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-red-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">
                Attendance Reporting
            </h1>

            <div class="flex items-center gap-6">
                <form method="GET" action="{{ route('teacher.absence.reporting') }}" class="relative w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-red-600">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Search Bar" 
                           class="w-full pl-9 pr-4 py-1.5 text-xs bg-white border border-red-500 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 placeholder-gray-400">
                </form>

                <div class="flex items-center gap-4">
                    <span class="text-sm font-bold text-gray-900 hover:text-red-600 cursor-pointer">
                        Faculty Profile
                    </span>
                    <button class="relative text-gray-800 hover:text-red-600 transition text-lg">
                        <i class="fa-regular fa-bell"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Body Area -->
        <div class="p-8 space-y-8 max-w-7xl w-full">

            <!-- SECTION 1: Absence Overview & Summary + Calendar -->
            <section class="space-y-4">
                <h2 class="text-base font-extrabold text-gray-900">
                    1. Absence Overview & Summary
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    <!-- Counter Card 1: Total Students -->
                    <div class="lg:col-span-4 bg-white border-2 border-red-300 rounded-3xl p-6 relative flex flex-col justify-between shadow-xs">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                        <div class="text-center py-2">
                            <p class="text-sm font-bold text-gray-800">Total Students</p>
                            <p class="text-4xl font-black text-gray-900 mt-2">{{ $totalStudents }}</p>
                        </div>
                        <div class="flex justify-end gap-1.5 mt-2">
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                        </div>
                    </div>

                    <!-- Counter Card 2: Students Marked Absent -->
                    <div class="lg:col-span-4 bg-white border-2 border-red-300 rounded-3xl p-6 relative flex flex-col justify-between shadow-xs">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                        <div class="text-center py-2">
                            <p class="text-sm font-bold text-gray-800">Students marked Absent</p>
                            <p class="text-4xl font-black text-red-600 mt-2">{{ $absentCount }}</p>
                        </div>
                        <div class="flex justify-end gap-1.5 mt-2">
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                        </div>
                    </div>

                    <!-- Mini Calendar Widget -->
                    <div class="lg:col-span-4 bg-white border-2 border-red-300 rounded-3xl p-5 shadow-xs">
                        <div class="flex items-center justify-between mb-3 text-xs font-bold text-gray-800">
                            <button class="p-1 hover:text-red-600">&lt;</button>
                            <div class="flex gap-2">
                                <span class="bg-gray-100 px-2 py-0.5 rounded border border-gray-300">{{ date('M') }}</span>
                                <span class="bg-gray-100 px-2 py-0.5 rounded border border-gray-300">{{ date('Y') }}</span>
                            </div>
                            <button class="p-1 hover:text-red-600">&gt;</button>
                        </div>

                        <div class="grid grid-cols-7 text-[10px] text-center font-bold text-gray-500 gap-y-1.5">
                            <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            <span class="text-gray-400">30</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span>
                            <span>7</span><span>8</span>
                            <span class="bg-gray-900 text-white rounded-full w-5 h-5 flex items-center justify-center mx-auto">{{ date('j') }}</span>
                            <span>10</span><span>11</span><span>12</span><span>13</span>
                            <span>14</span><span>15</span><span>16</span><span>17</span><span>18</span><span>19</span><span>20</span>
                            <span>21</span><span>22</span><span>23</span><span>24</span><span>25</span><span>26</span><span>27</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: Core Table & Student List -->
            <section class="space-y-4">
                <h2 class="text-base font-extrabold text-gray-900">
                    2. Student Absence Reporting (Core Table)
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Left: Present Today / Scanned Core Table (8 cols) -->
                    <div class="lg:col-span-8 bg-white border-2 border-red-300 rounded-3xl p-6 shadow-xs relative">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-black text-gray-900">Present Today</span>
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 bg-gray-100 px-3 py-1 rounded-full border border-gray-300">
                                <span>Waiting for Scan</span>
                                <i class="fa-solid fa-qrcode text-red-600 text-sm"></i>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-xl">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-800 text-white text-[11px] font-bold uppercase">
                                        <th class="py-2.5 px-4 text-center w-24">Student Photo</th>
                                        <th class="py-2.5 px-4 border-l border-gray-700">Name & ID Number</th>
                                        <th class="py-2.5 px-4 border-l border-gray-700 text-center w-32">Time-In Stamp</th>
                                        <th class="py-2.5 px-4 border-l border-gray-700 text-center w-32">OUT</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-300 text-xs font-semibold text-gray-900 bg-[#e5e7eb]">
                                    @foreach($presentStudents->take(5) as $student)
                                        <tr class="hover:bg-gray-300 transition">
                                            <td class="py-2 px-4 text-center">
                                                <div class="w-9 h-9 mx-auto rounded-full border-2 border-gray-900 flex items-center justify-center text-gray-800 bg-white">
                                                    <i class="fa-regular fa-user text-sm"></i>
                                                </div>
                                            </td>
                                            <td class="py-2 px-4 border-l border-gray-400">
                                                <p class="font-bold text-gray-900">{{ $student->name }}</p>
                                                <p class="text-gray-600 text-[11px]">ID: {{ $student->id_number }}</p>
                                            </td>
                                            <td class="py-2 px-4 border-l border-gray-400 text-center font-bold text-gray-800">
                                                {{ $student->time_in }}
                                            </td>
                                            <td class="py-2 px-4 border-l border-gray-400 text-center font-bold text-gray-800">
                                                {{ $student->time_out }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end gap-1.5 mt-4">
                            <span class="w-5 h-3 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-5 h-3 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-5 h-3 bg-[#881337] rounded-sm slanted-pill"></span>
                        </div>
                    </div>

                    <!-- Right: Student List Reference (4 cols) -->
                    <div class="lg:col-span-4 bg-white border-2 border-red-300 rounded-3xl p-5 shadow-xs relative">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>

                        <div class="text-center mb-3">
                            <h3 class="text-sm font-black text-gray-900">Student List</h3>
                        </div>

                        <div class="space-y-2 max-h-[380px] overflow-y-auto pr-1">
                            @foreach($allStudents as $st)
                                <div class="flex items-center gap-3 p-2 bg-gray-50 border border-gray-200 rounded-xl hover:bg-red-50 transition">
                                    <div class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center bg-white shrink-0">
                                        <i class="fa-regular fa-user text-xs text-gray-700"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $st->first_name }} {{ $st->last_name }}</p>
                                        <p class="text-[10px] text-gray-500 font-medium">ID: {{ $st->id_number ?? '00'.$st->id }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-1 mt-4">
                            <span class="w-4 h-2.5 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2.5 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2.5 bg-[#881337] rounded-sm slanted-pill"></span>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </main>

</body>
</html>