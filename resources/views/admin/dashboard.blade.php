<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight uppercase">
                <span class="text-red-600">Subcontractor List </span> Board
            </h2>
        </div>
    </x-slot>

    <div class="max-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-end mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900 tracking-wide uppercase">
                    {{ ('Registered Sub-Contractors') }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ('Manage and assign tasks to registered sub-contractors.') }}
                </p>
            </div>
            <{{-- Assign New Task Button --}}
            <a href="{{ route('tasks.asign') }}"
            class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded shadow-lg transition duration-200 uppercase tracking-wider text-sm">
                + Assign New Task
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
    <form method="GET" action="{{ url()->current() }}">

        <div class="p-5 flex flex-col md:flex-row items-center justify-between gap-4 border-b border-slate-100">
            <div class="relative w-full md:w-96">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company name, PIC, email or services..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-700 placeholder-slate-400 focus:border-red-500 focus:ring-4 focus:ring-red-50 transition-all">
                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                @if(request()->anyFilled(['search', 'status', 'cidb_grade']))
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition">
                        Reset Filters
                    </a>
                @endif
                <a href="{{ route('admin.dashboard.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-2 border border-slate-200 hover:border-emerald-200 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                    Excel
                </a>
                <a href="{{ route('admin.dashboard.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-2 border border-slate-200 hover:border-slate-900 hover:bg-slate-950 hover:text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                    PDF
                </a>
            </div>
        </div>

        <div class="bg-slate-50/50 px-5 py-4 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Filter Status</label>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-red-500 focus:ring-0 cursor-pointer">
                    <option value="">All Subcontractors</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">CIDB Grade Requirement</label>
                <input type="text" name="cidb_grade" value="{{ request('cidb_grade') }}" placeholder="e.g. G7, G3" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 placeholder-slate-300 focus:border-red-500 focus:ring-0 uppercase">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Sort Metric</label>
                <select name="sort_by" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-red-500 focus:ring-0 cursor-pointer">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date Registered</option>
                    <option value="company_name" {{ request('sort_by') === 'company_name' ? 'selected' : '' }}>Company Name</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Person In Charge</option>
                    <option value="cidb_grade" {{ request('sort_by') === 'cidb_grade' ? 'selected' : '' }}>CIDB Qualification</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Account Status</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Display Order</label>
                <div class="flex gap-2">
                    <select name="sort_dir" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-red-500 focus:ring-0 cursor-pointer">
                        <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>Descending &darr;</option>
                        <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>Ascending &uarr;</option>
                    </select>
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-black uppercase tracking-widest text-[10px] transition shadow-md">
                        Apply
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 border-t-4 border-t-red-600">
            <div class="overflow-x-auto">
                <table class="text-left border-collapse w-full">
                    <thead class="bg-gray-100 border-b border-gray-200 text-gray-700 uppercase text-sm font-bold">
                        <tr>
                            <th class="px-4 py-5">Company Info</th>
                            <th class="px-4 py-5">Person In Charge</th>
                            <th class="px-4 py-5 text-center">CIDB Grade</th>
                            <th class="px-4 py-5 text-center">Status</th>
                            <th class="px-4 py-5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-red-50/30 transition-colors duration-150">
                                <td class="px-8 py-3">
                                    <div class="text-lg font-bold text-gray-900">{{ $user->company_name ?? '---' }}</div>
                                    @if($user->year_established)
                                        <span class="text-xs text-gray-400 uppercase tracking-tighter font-medium">Est. {{ $user->year_established }}</span>
                                    @endif
                                </td>

                                <td class="px-8 py-3">
                                    <div class="text-mid font-bold text-gray-800">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </td>

                                <td class="px-8 py-3 text-center">
                                    @if($user->cidb_grades)
                                        <span class="inline-block bg-gray-100 text-gray-800 text-sm font-bold px-3 py-1 rounded border border-gray-200">
                                            {{ is_array($user->cidb_grades) ? implode(', ', $user->cidb_grades) : $user->cidb_grades }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 italic text-sm">N/A</span>
                                    @endif
                                </td>

                                <td class="px-8 py-3">
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

                                <td class="px-8 py-3 text-center">
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
