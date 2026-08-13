<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Management - SIATRACK</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="bg-gray-50">


    {{-- SIDEBAR --}}

    @include('layouts.sidebar')


    {{-- MAIN CONTENT --}}

    <main class="ml-64 min-h-screen">


        {{-- HEADER --}}

        <header class="bg-white border-b border-gray-200">

            <div class="px-8 py-6">

                <div class="flex items-center justify-between">

                    <div>

                        <h1
                            class="text-2xl
                                   font-bold
                                   text-gray-900"
                        >
                            User Management
                        </h1>

                        <p
                            class="mt-1
                                   text-sm
                                   text-gray-500"
                        >
                            Manage users and their account information.
                        </p>

                    </div>


                    {{-- ADD USER --}}

                    <a
                        href="{{ route('admin.users.create') }}"
                        class="inline-flex
                               items-center
                               gap-2
                               px-5
                               py-3
                               bg-red-600
                               hover:bg-red-700
                               text-white
                               rounded-xl
                               text-sm
                               font-semibold
                               transition"
                    >

                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />

                        </svg>

                        Add New User

                    </a>

                </div>

            </div>

        </header>


        {{-- PAGE CONTENT --}}

        <div class="p-8">


            {{-- SUCCESS MESSAGE --}}

            @if(session('success'))

                <div
                    class="mb-6
                           flex
                           items-center
                           gap-3
                           rounded-xl
                           border border-green-200
                           bg-green-50
                           px-5
                           py-4
                           text-sm
                           text-green-700"
                >

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />

                    </svg>

                    {{ session('success') }}

                </div>

            @endif


            {{-- STATISTICS --}}

            <div
                class="grid
                       grid-cols-1
                       sm:grid-cols-2
                       lg:grid-cols-4
                       gap-5
                       mb-8"
            >


                {{-- TOTAL --}}

                <div
                    class="bg-white
                           border border-gray-200
                           rounded-2xl
                           p-5
                           shadow-sm"
                >

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Total Users
                    </p>

                    <p
                        class="mt-2
                               text-3xl
                               font-bold
                               text-gray-900"
                    >
                        {{ $totalUsers }}
                    </p>

                </div>


                {{-- STUDENTS --}}

                <div
                    class="bg-white
                           border border-gray-200
                           rounded-2xl
                           p-5
                           shadow-sm"
                >

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Students
                    </p>

                    <p
                        class="mt-2
                               text-3xl
                               font-bold
                               text-gray-900"
                    >
                        {{ $studentCount }}
                    </p>

                </div>


                {{-- TEACHERS --}}

                <div
                    class="bg-white
                           border border-gray-200
                           rounded-2xl
                           p-5
                           shadow-sm"
                >

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Teachers
                    </p>

                    <p
                        class="mt-2
                               text-3xl
                               font-bold
                               text-gray-900"
                    >
                        {{ $teacherCount }}
                    </p>

                </div>


                {{-- ACTIVE --}}

                <div
                    class="bg-white
                           border border-gray-200
                           rounded-2xl
                           p-5
                           shadow-sm"
                >

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Active Users
                    </p>

                    <p
                        class="mt-2
                               text-3xl
                               font-bold
                               text-green-600"
                    >
                        {{ $activeUsers }}
                    </p>

                </div>

            </div>


            {{-- SEARCH / FILTERS --}}

            <div
                class="bg-white
                       border border-gray-200
                       rounded-2xl
                       p-5
                       shadow-sm
                       mb-6"
            >

                <form
                    method="GET"
                    action="{{ route('admin.users.index') }}"
                    class="flex flex-col xl:flex-row gap-3"
                >


                    {{-- SEARCH --}}

                    <div class="relative flex-1">

                        <svg
                            class="absolute
                                   left-4
                                   top-1/2
                                   -translate-y-1/2
                                   w-5
                                   h-5
                                   text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0z"
                            />

                        </svg>


                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search ID, name, or email..."
                            class="w-full
                                   pl-11
                                   pr-4
                                   py-3
                                   rounded-xl
                                   border border-gray-300
                                   bg-white
                                   text-sm
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-red-500
                                   focus:border-red-500"
                        >

                    </div>


                    {{-- ROLE --}}

                    <div class="w-full xl:w-52">

                        <select
                            name="role"
                            onchange="this.form.submit()"
                            class="w-full
                                   px-4
                                   py-3
                                   rounded-xl
                                   border border-gray-300
                                   bg-white
                                   text-sm
                                   text-gray-700
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-red-500
                                   focus:border-red-500"
                        >

                            <option value="">
                                All Roles
                            </option>


                            @foreach($roles as $role)

                                <option
                                    value="{{ $role->id }}"
                                    {{ (string) request('role') === (string) $role->id ? 'selected' : '' }}
                                >

                                    {{ ucfirst($role->name) }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- STATUS --}}

                    <div class="w-full xl:w-52">

                        <select
                            name="status"
                            onchange="this.form.submit()"
                            class="w-full
                                   px-4
                                   py-3
                                   rounded-xl
                                   border border-gray-300
                                   bg-white
                                   text-sm
                                   text-gray-700
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-red-500
                                   focus:border-red-500"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="active"
                                {{ request('status') === 'active' ? 'selected' : '' }}
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                {{ request('status') === 'inactive' ? 'selected' : '' }}
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- SEARCH BUTTON --}}

                    <button
                        type="submit"
                        class="px-6
                               py-3
                               rounded-xl
                               bg-red-600
                               hover:bg-red-700
                               text-white
                               text-sm
                               font-semibold
                               transition"
                    >
                        Search
                    </button>


                    {{-- CLEAR --}}

                    @if(
                        request()->filled('search') ||
                        request()->filled('role') ||
                        request()->filled('status')
                    )

                        <a
                            href="{{ route('admin.users.index') }}"
                            class="px-6
                                   py-3
                                   rounded-xl
                                   bg-gray-100
                                   hover:bg-gray-200
                                   text-gray-700
                                   text-sm
                                   font-semibold
                                   transition
                                   text-center"
                        >
                            Clear
                        </a>

                    @endif

                </form>

            </div>


            {{-- USER TABLE --}}

            <div
                class="bg-white
                       border border-gray-200
                       rounded-2xl
                       shadow-sm
                       overflow-hidden"
            >

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-50 border-b border-gray-200">

                            <tr>

                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    ID Number
                                </th>


                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    Full Name
                                </th>


                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    Role / Designation
                                </th>


                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    Grade / Section
                                </th>


                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    NFC Tag ID
                                </th>


                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    Contact Number
                                </th>


                                <th
                                    class="px-6
                                           py-4
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    Status
                                </th>


                                <th
                                    class="px-6
                                           py-4
                                           text-right
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase
                                           tracking-wider"
                                >
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">


                            @forelse($users as $user)

                                <tr
                                    class="hover:bg-gray-50
                                           transition"
                                >


                                    {{-- USER ID --}}

                                    <td
                                        class="px-6
                                               py-5
                                               whitespace-nowrap"
                                    >

                                        <span
                                            class="font-semibold
                                                   text-gray-900"
                                        >
                                            {{ $user->id_number }}
                                        </span>

                                    </td>


                                    {{-- FULL NAME --}}

                                    <td
                                        class="px-6
                                               py-5
                                               whitespace-nowrap"
                                    >

                                        <div
                                            class="font-semibold
                                                   text-gray-900"
                                        >
                                            {{ $user->first_name }}
                                            {{ $user->last_name }}
                                        </div>

                                        <div
                                            class="text-xs
                                                   text-gray-400
                                                   mt-1"
                                        >
                                            {{ $user->email }}
                                        </div>

                                    </td>


                                    {{-- ROLE --}}

                                    <td
                                        class="px-6
                                               py-5
                                               whitespace-nowrap"
                                    >

                                        @if($user->role)

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       px-3
                                                       py-1
                                                       rounded-full
                                                       bg-red-50
                                                       text-red-700
                                                       text-xs
                                                       font-semibold"
                                            >
                                                {{ ucfirst($user->role->name) }}
                                            </span>

                                        @else

                                            <span
                                                class="text-gray-400
                                                       text-sm"
                                            >
                                                No role
                                            </span>

                                        @endif

                                    </td>


                                    {{-- GRADE / SECTION --}}

<td class="px-6 py-5 whitespace-nowrap">
    @if($user->sections->count())

        @foreach($user->sections as $section)

            <span class="text-sm text-gray-700">
                {{ $section->grade_level }} - {{ $section->section_name }}
            </span>

        @endforeach

    @else

        <span class="text-sm text-gray-400">
            —
        </span>

    @endif
</td>


                                    {{-- NFC TAG --}}

                                    <td
                                        class="px-6
                                               py-5
                                               whitespace-nowrap"
                                    >

                                        <span
                                            class="text-sm
                                                   text-gray-400"
                                        >
                                            —
                                        </span>

                                    </td>


                                    {{-- CONTACT --}}

                                    <td
                                        class="px-6
                                               py-5
                                               whitespace-nowrap"
                                    >

                                        @if($user->phone_number)

                                            <span
                                                class="text-sm
                                                       text-gray-700"
                                            >
                                                {{ $user->phone_number }}
                                            </span>

                                        @else

                                            <span
                                                class="text-sm
                                                       text-gray-400"
                                            >
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- STATUS --}}

                                    <td
                                        class="px-6
                                               py-5
                                               whitespace-nowrap"
                                    >

                                        @if($user->is_active)

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       px-3
                                                       py-1
                                                       rounded-full
                                                       bg-green-50
                                                       text-green-700
                                                       text-xs
                                                       font-semibold"
                                            >
                                                Active
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       px-3
                                                       py-1
                                                       rounded-full
                                                       bg-gray-100
                                                       text-gray-600
                                                       text-xs
                                                       font-semibold"
                                            >
                                                Inactive
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTIONS --}}

                                    <td
                                        class="px-6
                                               py-5
                                               whitespace-nowrap"
                                    >

                                        <div
                                            class="flex
                                                   items-center
                                                   justify-end
                                                   gap-2"
                                        >


                                            {{-- EDIT --}}

                                            <a
                                                href="{{ route('admin.users.edit', $user) }}"
                                                class="inline-flex
                                                       items-center
                                                       justify-center
                                                       w-9
                                                       h-9
                                                       rounded-lg
                                                       bg-gray-100
                                                       text-gray-600
                                                       hover:bg-red-50
                                                       hover:text-red-600
                                                       transition"
                                                title="Edit User"
                                            >

                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 8.5-8.5z"
                                                    />

                                                </svg>

                                            </a>


                                            {{-- DELETE --}}

                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex
                                                           items-center
                                                           justify-center
                                                           w-9
                                                           h-9
                                                           rounded-lg
                                                           bg-gray-100
                                                           text-gray-600
                                                           hover:bg-red-50
                                                           hover:text-red-600
                                                           transition"
                                                    title="Delete User"
                                                >

                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        />

                                                    </svg>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="8"
                                        class="px-6
                                               py-16
                                               text-center"
                                    >

                                        <div
                                            class="flex
                                                   flex-col
                                                   items-center"
                                        >

                                            <svg
                                                class="w-12 h-12 text-gray-300"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M20 21a8 8 0 00-16 0m16 0H4m16 0v-1a8 8 0 00-16 0v1m12-13a4 4 0 11-8 0 4 4 0 018 0z"
                                                />

                                            </svg>


                                            <h3
                                                class="mt-4
                                                       text-sm
                                                       font-semibold
                                                       text-gray-900"
                                            >
                                                No users found
                                            </h3>


                                            <p
                                                class="mt-1
                                                       text-sm
                                                       text-gray-500"
                                            >
                                                Try changing your search or filters.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- PAGINATION --}}

                @if($users->hasPages())

                    <div
                        class="px-6
                               py-4
                               border-t border-gray-200"
                    >

                        {{ $users->links() }}

                    </div>

                @endif

            </div>

        </div>

    </main>

</body>

</html>