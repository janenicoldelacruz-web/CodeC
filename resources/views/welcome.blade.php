<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIATRACK Portal - Southern Isabela Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#d1d5db] min-h-screen flex items-center justify-center p-4">

    <div class="relative bg-white w-full max-w-sm rounded-[36px] p-8 pt-14 shadow-2xl border border-gray-100 flex flex-col items-center">
        
        <!-- Official SIA Seal Logo -->
        <div class="absolute -top-14 w-28 h-28 rounded-full bg-white p-1 shadow-xl border-2 border-red-500 flex items-center justify-center">
            <img src="{{ asset('images/sia-logo.png') }}" alt="SIA Official Seal" class="w-full h-full object-contain rounded-full">
        </div>

        <h1 class="text-2xl font-black text-gray-900 tracking-tight text-center mt-2">SIATRACK Portal</h1>
        <p class="text-xs text-gray-500 font-semibold mt-0.5 mb-6 text-center">Southern Isabela Academy</p>

        <!-- Admin & Faculty Access Section -->
        <div class="w-full">
            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="flex-shrink mx-3 text-[11px] font-bold text-gray-500">Admin & Faculty Access</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <div class="space-y-3 mt-3">
                <a href="{{ route('login.portal', ['role' => 'teacher']) }}" 
                   class="w-full py-3.5 px-5 rounded-2xl bg-gradient-to-r from-[#38bdf8] via-[#60a5fa] to-[#93c5fd] hover:opacity-90 text-white font-black text-sm flex items-center justify-center gap-2.5 shadow-md transition transform active:scale-95">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
                    <span>Faculty Login</span>
                </a>

                <a href="{{ route('login.portal', ['role' => 'admin']) }}" 
                   class="w-full py-3.5 px-5 rounded-2xl bg-[#1e293b] hover:bg-black text-white font-black text-sm flex items-center justify-center gap-2.5 shadow-md transition transform active:scale-95">
                    <i class="fa-solid fa-user-gear text-base"></i>
                    <span>Admin Access</span>
                </a>
            </div>
        </div>

        <!-- Student Access Section -->
        <div class="w-full mt-6">
            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="flex-shrink mx-3 text-[11px] font-bold text-gray-500">Student Access</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <div class="mt-3">
                <a href="{{ route('login.portal', ['role' => 'student']) }}" 
                   class="w-full py-3.5 px-5 rounded-2xl bg-gradient-to-r from-[#22c55e] to-[#16a34a] hover:opacity-90 text-white font-black text-sm flex items-center justify-center gap-2.5 shadow-md transition transform active:scale-95">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>Evaluate Faculty</span>
                </a>
            </div>
        </div>

    </div>

</body>
</html>