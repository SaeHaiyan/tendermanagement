<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight uppercase">
                <span class="text-red-600">Subcontractor List </span> Board
            </h2>
        </div>
    </x-slot>

    <div class="py-12 max-w-[100%] mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-end mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900 tracking-wide uppercase">
                    {{ ('Registered Sub-Contractors') }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ('Manage and assign tasks to registered sub-contractors.') }}
                </p>
            </div>
            <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded shadow-lg transition duration-200 uppercase tracking-wider text-sm">
                + Assign New Task
            </button>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 border-t-4 border-t-red-600">
            <div class="overflow-x-auto">
                <table class="text-left border-collapse w-full">
                    <thead class="bg-gray-100 border-b border-gray-200 text-gray-700 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-8 py-5">Company Info</th>
                            <th class="px-8 py-5">Person In Charge</th>
                            <th class="px-8 py-5 text-center">CIDB Grade</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5">Core Services</th>
                            <th class="px-8 py-5 text-right">Registered</th>
                            <th class="px-8 py-5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-red-50/30 transition-colors duration-150">
                                <td class="px-8 py-6">
                                    <div class="text-md font-semibold text-gray-800">{{ $user->company_name ?? '---' }}</div>
                                    @if($user->year_established)
                                        <span class="text-xs text-gray-400 uppercase tracking-tighter font-medium">Est. {{ $user->year_established }}</span>
                                    @endif
                                </td>

                                <td class="px-8 py-6">
                                    <div class="text-lg font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    @if($user->cidb_grades)
                                        <span class="inline-block bg-gray-100 text-gray-800 text-sm font-bold px-3 py-1 rounded border border-gray-200">
                                            {{ is_array($user->cidb_grades) ? implode(', ', $user->cidb_grades) : $user->cidb_grades }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 italic text-sm">N/A</span>
                                    @endif
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex justify-center">
                                        @php
                                            $status = strtolower($user->status ?? 'pending');
                                            $config = match($status) {
                                                'active'   => ['base' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                                'pending'  => ['base' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
                                                'inactive' => ['base' => 'bg-red-50 text-red-700 border-red-200', 'dot' => 'bg-red-500'],
                                                default    => ['base' => 'bg-gray-50 text-gray-600 border-gray-200', 'dot' => 'bg-gray-400'],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center py-1 px-3 rounded text-[10px] font-black uppercase tracking-widest border {{ $config['base'] }}">
                                            <span class="w-1.5 h-1.5 mr-2 rounded-full {{ $config['dot'] }}"></span>
                                            {{ $status }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <p class="text-sm text-gray-600 italic max-w-xs">
                                        "{{ Str::limit($user->services_provided ?? 'No services listed', 40) }}"
                                    </p>
                                </td>

                                <td class="px-8 py-6 text-right text-gray-500">
                                    <div class="text-sm font-medium">{{ $user->created_at->format('M d, Y') }}</div>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.subcon.show', ['id' => $user->id]) }}"
                                            class="text-gray-600 hover:text-red-600 font-bold px-3 py-1 border border-gray-300 rounded hover:border-red-600 transition">
                                            View
                                        </a>
                                        <form action="{{ route('admin.subcon.destroy', ['id' => $user->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-600 hover:text-red-600 font-bold px-3 py-1 border border-gray-300 rounded hover:border-red-600 transition">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center text-gray-400 text-lg italic">
                                    No registered sub-contractors found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex justify-between items-center px-2">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-widest italic">
                {{ date('Y') }} SubContractor List Board &bull; Admin Oversight
            </div>
            <div class="text-slate-600 text-sm font-black bg-white px-4 py-2 rounded border border-slate-200">
            Total: <span class="text-red-600">{{ \App\Models\User::where('role', 'subcon')->count() }}</span> Sub-contractors            </div>
        </div>
    </div>
</x-app-layout>
