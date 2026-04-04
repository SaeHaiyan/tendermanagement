<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ ('Admin Management Console') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-3xl font-extrabold text-gray-900">Subcon Directory</h3>
                    <p class="text-gray-500 mt-1 text-lg">Manage and monitor all registered service providers.</p>
                </div>
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-200">
                    + Assign New Task
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-sm tracking-widest">
                                <th class="px-8 py-5 font-bold">Person In Charge</th>
                                <th class="px-8 py-5 font-bold">Company Information</th>
                                <th class="px-8 py-5 text-center font-bold">CIDB Grade</th>
                                <th class="px-8 py-5 font-bold">Services Provided</th>
                                <th class="px-8 py-5 text-right font-bold">Registration Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                                    <td class="px-8 py-6">
                                        <div class="text-lg font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-indigo-600 font-medium">{{ $user->email }}</div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="text-lg text-gray-800 font-semibold">
                                            {{ $user->company_name ?? '---' }}
                                        </div>
                                        @if($user->year_established)
                                            <span class="text-xs text-gray-400">Est. {{ $user->year_established }}</span>
                                        @endif
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        @if($user->grade)
                                            <span class="inline-block bg-indigo-100 text-indigo-700 text-sm font-black px-4 py-1.5 rounded-md border border-indigo-200">
                                                {{ $user->grade }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 italic text-sm">No Grade</span>
                                        @endif
                                    </td>

                                    <td class="px-8 py-6">
                                        <p class="text-base text-gray-600 leading-relaxed max-w-sm italic">
                                            "{{ Str::limit($user->services ?? 'No services described yet.', 60) }}"
                                        </p>
                                    </td>

                                    <td class="px-8 py-6 text-right text-gray-500 font-medium">
                                        <div class="text-base">{{ $user->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center text-gray-400 text-xl italic">
                                        No subcons found in the database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-right text-gray-400 text-sm italic">
                Showing {{ $users->count() }} total registered users.
            </div>

        </div>
    </div>
</x-app-layout>
