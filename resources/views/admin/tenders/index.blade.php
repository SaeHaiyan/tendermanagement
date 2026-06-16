<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight uppercase">
               <span class="text-red-600">Project Tender</span> Board
            </h2>
        </div>
    </x-slot>

    <div x-data="{ loading: false }" class="mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-green-100 border border-green-300 text-green-700 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-end mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900 tracking-wide uppercase">
                    {{ ('Active Tenders') }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ ('Manage project tenders and assign to subcontractors.') }}
                </p>
            </div>
            <a href="{{ route('admin.tenders.create') }}" class="inline-flex items-center bg-red-600 text-white px-6 py-3 rounded shadow-lg hover:bg-red-700 transition-all font-bold text-sm uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Post New Tender
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <form method="GET" action="{{ url()->current() }}">

                <div class="p-5 flex flex-col md:flex-row items-center justify-between gap-4 border-b border-slate-100">
                    <div class="relative w-full md:w-96">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tender title, description or reference..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-700 placeholder-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50 transition-all">
                        <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
                    </div>

                    <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                        @if(request()->anyFilled(['search', 'status', 'grade']))
                            <a href="{{ route('admin.tenders.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition">
                                Reset Filters
                            </a>
                        @endif
                        <a href="{{ route('admin.tenders.exportTendersPdf', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-2 border border-slate-200 hover:border-emerald-200 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                            Excel
                        </a>
                        <a href="{{ route('admin.tenders.exportTendersPdf', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-2 border border-slate-200 hover:border-slate-900 hover:bg-slate-950 hover:text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                            PDF
                        </a>
                    </div>
                </div>

                <div class="bg-slate-50/50 px-5 py-4 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Status</label>
                        <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-indigo-500 focus:ring-0 cursor-pointer">
                            <option value="">All Tenders</option>
                            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Required CIDB Grade</label>
                        <select name="grade" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-indigo-500 focus:ring-0 cursor-pointer">
                            <option value="">All Grades</option>
                            @foreach(['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G7'] as $g)
                                <option value="{{ $g }}" {{ request('grade') === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Sort Criteria</label>
                        <select name="sort_by" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-indigo-500 focus:ring-0 cursor-pointer">
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Newest Published</option>
                            <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Project Title</option>
                            <option value="deadline" {{ request('sort_by') === 'deadline' ? 'selected' : '' }}>Deadline Date</option>
                            <option value="required_grade" {{ request('sort_by') === 'required_grade' ? 'selected' : '' }}>CIDB Grade</option>
                            <option value="selected_subcon" {{ request('sort_by') === 'selected_subcon' ? 'selected' : '' }}>Assigned Subcontractor</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Directional Order</label>
                        <div class="flex gap-2">
                            <select name="sort_dir" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 focus:border-indigo-500 focus:ring-0 cursor-pointer">
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
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">
                                <a href="{{ route('admin.tenders.index', array_merge(request()->except('page'), ['sort_by' => 'title', 'sort_dir' => request('sort_by') === 'title' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-2">
                                    Project Details
                                    @if(request('sort_by') === 'title')
                                        <span>{{ request('sort_dir') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">
                                <a href="{{ route('admin.tenders.index', array_merge(request()->except('page'), ['sort_by' => 'selected_subcon', 'sort_dir' => request('sort_by') === 'selected_subcon' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-2">
                                    Assignee & Progress
                                    @if(request('sort_by') === 'selected_subcon')
                                        <span>{{ request('sort_dir') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">
                                <a href="{{ route('admin.tenders.index', array_merge(request()->except('page'), ['sort_by' => 'deadline', 'sort_dir' => request('sort_by') === 'deadline' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-2">
                                    Deadline
                                    @if(request('sort_by') === 'deadline')
                                        <span>{{ request('sort_dir') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">
                                Status
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest text-center">Management</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($tenders as $tender)
                            <tr class="hover:bg-red-50/20 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-slate-900">{{ $tender->title }}</div>
                                    <div class="text-[10px] text-slate-400 font-black mt-1 uppercase tracking-widest">
                                        Grade: {{ $tender->required_grade }}
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    @if($tender->selected_subcon_id)
                                        <div class="flex flex-col space-y-2">
                                            <div class="flex items-center text-sm font-bold text-slate-700">
                                                {{ $tender->selectedSubcon->company_name ?? 'Unknown' }}
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1 bg-slate-100 h-1.5 rounded-full overflow-hidden w-28">
                                                    <div class="bg-red-600 h-full rounded-full transition-all duration-500" style="width: {{ $tender->progress_percent }}%"></div>
                                                </div>
                                                <span class="text-[10px] font-black text-slate-500">{{ $tender->progress_percent }}%</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Unassigned</span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-sm text-slate-600 font-medium">
                                    {{ \Carbon\Carbon::parse($tender->deadline)->format('d M Y') }}
                                </td>

                                <td class="px-6 py-5">
                                    @if($tender->selected_subcon_id)
                                        <div class="inline-flex items-center px-3 py-1 rounded bg-emerald-50 border border-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 mr-2"></span>
                                            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Assigned</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-3 py-1 rounded bg-amber-50 border border-amber-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400 mr-2"></span>
                                            <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Under Review</span>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center space-x-2">

                                            <a href="{{ route('admin.tenders.match', $tender) }}" @click="loading = true" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-black text-[10px] uppercase tracking-widest rounded shadow-sm animate-pulse">
                                                Review
                                            </a>
                                            <a href="{{ route('admin.tenders.show', $tender) }}" class="p-2 text-slate-500 hover:text-red-600 border border-slate-200 rounded hover:border-red-600 transition" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            <a href="{{ route('admin.tenders.edit', $tender) }}" class="p-2 text-slate-500 hover:text-red-600 border border-slate-200 rounded hover:border-red-600 transition" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                        <form action="{{ route('admin.tenders.destroy', $tender) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-slate-400 font-medium">No tenders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex justify-between items-center px-2">
            <div class="text-slate-400 text-xs font-bold uppercase tracking-widest italic">
                {{ date('Y') }} Tender Board &bull; Admin Oversight
            </div>
            <div class="text-slate-600 text-sm font-black bg-white px-4 py-2 rounded border border-slate-200">
                Total: <span class="text-red-600">{{ $tenders->count() }}</span> Tenders
            </div>
        </div>
        <div x-show="loading" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg rounded-[2rem] bg-white border border-slate-200 shadow-[0_30px_60px_-30px_rgba(15,23,42,0.6)] p-8 text-center">
                <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg">
                    <svg class="h-8 w-8 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 0v4m0 14v-4m10-6h-4M6 12H2m15.364 6.364l-2.828-2.828M8.464 8.464L5.636 5.636m12.728 0l-2.828 2.828M8.464 15.536l-2.828 2.828"/></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-3">AI Evaluation Underway</h3>
                <p class="text-sm leading-7 text-slate-600 mx-auto max-w-xl">Your AI review is being generated now. Please keep this tab open until the results are ready.</p>
                <div class="mt-8 inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Loading AI review...
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
