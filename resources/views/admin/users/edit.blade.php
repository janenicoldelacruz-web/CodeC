<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit User - SIATRACK</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen">

    @include('layouts.sidebar')

    <main class="ml-64 min-h-screen">

        <!-- Header -->
        <div class="bg-white border-b border-red-100 px-8 py-6">

            <div class="flex items-center justify-between">

                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Edit User
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Update the user's information in the SIATRACK system.
                    </p>
                </div>

                <a
                    href="{{ route('admin.users.index') }}"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium"
                >
                    Back to Users
                </a>

            </div>

        </div>


        <!-- Form -->
        <div class="p-8">

            @if ($errors->any())

                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">

                    <p class="font-semibold mb-2">
                        Please fix the following:
                    </p>

                    <ul class="list-disc list-inside text-sm space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('admin.users.update', $user) }}"
                class="bg-white rounded-2xl border border-gray-200 shadow-sm"
            >

                @csrf
                @method('PUT')


                <!-- Personal Information -->
                <div class="p-6 border-b border-gray-100">

                    <h2 class="text-lg font-bold text-gray-800">
                        Personal Information
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Update the user's basic information.
                    </p>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">


                        <!-- ID Number -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                ID Number
                            </label>

                            <input
                                type="text"
                                name="id_number"
                                value="{{ old('id_number', $user->id_number) }}"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter ID number"
                            >

                        </div>


                        <!-- First Name -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                First Name
                            </label>

                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name', $user->first_name) }}"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter first name"
                            >

                        </div>


                        <!-- Last Name -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Last Name
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name', $user->last_name) }}"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter last name"
                            >

                        </div>


                        <!-- Email -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter email address"
                            >

                        </div>


                        <!-- Role -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Role
                            </label>

                            <select
                                name="role_id"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                            >

                                <option value="">
                                    Select role
                                </option>

                                @foreach ($roles as $role)

                                    <option
                                        value="{{ $role->id }}"
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}
                                    >
                                        {{ ucfirst($role->name) }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <!-- NFC Tag ID -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                NFC Tag ID
                            </label>

                            <input
                                type="text"
                                name="nfc_tag_id"
                                value="{{ old('nfc_tag_id', optional($user->nfcCard)->tag_id) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter NFC tag ID"
                            >

                            <p class="text-xs text-gray-400 mt-1">
                                Leave blank to remove the NFC card.
                            </p>

                        </div>


                        <!-- Contact Number -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Contact Number
                            </label>

                            <input
                                type="text"
                                name="phone_number"
                                value="{{ old('phone_number', $user->phone_number) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter contact number"
                            >

                        </div>


                        <!-- Parent Contact -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Parent Contact Number
                            </label>

                            <input
                                type="text"
                                name="parent_phone_number"
                                value="{{ old('parent_phone_number', $user->parent_phone_number) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter parent contact number"
                            >

                        </div>

                    </div>

                </div>


               <!-- Academic Information -->
<div class="p-6 border-b border-gray-100">

    <h2 class="text-lg font-bold text-gray-800">
        Academic Information
    </h2>

    <p class="text-sm text-gray-500 mt-1">
        Update the user's grade level and strand.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">

        <!-- Grade Level -->
        <div>

            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Grade Level
            </label>

            <select
                name="grade_level"
                required
                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
            >

                <option value="">
                    Select Grade Level
                </option>

                <option
                    value="11"
                    {{ old('grade_level', $user->grade_level) == '11' ? 'selected' : '' }}
                >
                    Grade 11
                </option>

            </select>

        </div>


        <!-- Strand -->
        <div>

            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Strand / Section
            </label>

            <select
                name="strand"
                required
                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
            >

                <option value="">
                    Select Strand
                </option>

                <option
                    value="HUMSS"
                    {{ old('strand', $user->strand) == 'HUMSS' ? 'selected' : '' }}
                >
                    HUMSS
                </option>

                <option
                    value="GAS"
                    {{ old('strand', $user->strand) == 'GAS' ? 'selected' : '' }}
                >
                    GAS
                </option>

                <option
                    value="STEM"
                    {{ old('strand', $user->strand) == 'STEM' ? 'selected' : '' }}
                >
                    STEM
                </option>

                <option
                    value="ABM"
                    {{ old('strand', $user->strand) == 'ABM' ? 'selected' : '' }}
                >
                    ABM
                </option>

            </select>

        </div>

    </div>

</div>

                </div>


                <!-- Account Security -->
                <div class="p-6 border-b border-gray-100">

                    <h2 class="text-lg font-bold text-gray-800">
                        Account Security
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Leave the password fields blank if you do not want to change the password.
                    </p>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">


                        <!-- Password -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                New Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Enter new password"
                            >

                        </div>


                        <!-- Confirm Password -->
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirm New Password
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                placeholder="Confirm new password"
                            >

                        </div>

                    </div>

                </div>


                <!-- Buttons -->
                <div class="p-6 flex items-center justify-end gap-3">

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold"
                    >
                        Update User
                    </button>

                </div>

            </form>

        </div>

    </main>

</body>
</html>