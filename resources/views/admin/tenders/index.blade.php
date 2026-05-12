<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight uppercase">
               <span class="text-red-600">Project Tender</span> Board
            </h2>
        </div>
    </x-slot>

    <div class="py-12 mx-auto sm:px-6 lg:px-8">

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
            <a href="{{ route('admin.tenders.create') }}" class="inline-flex items-center bg-red-600 text-white px-6 py-2.5 rounded shadow-lg hover:bg-red-700 transition-all font-bold text-sm uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Post New Tender
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 border-t-4 border-t-red-600">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Project Details</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Assignee & Progress</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Deadline</th>
                            <th class="px-6 py-5 text-left text-xs font-black text-slate-500 uppercase tracking-widest">Status</th>
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
                                    @if($tender->work_status === 'under_review')
                                        <div class="inline-flex items-center px-3 py-1 rounded bg-blue-50 border border-blue-200">
                                            <span class="relative flex h-2 w-2 mr-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                                            </span>
                                            <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Review Pending</span>
                                        </div>
                                    @else
                                        @php
                                            $statusConfig = [
                                                'open' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-400'],
                                                'in_progress' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'dot' => 'bg-slate-400'],
                                                'completed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-400'],
                                            ];
                                            $config = $statusConfig[$tender->work_status ?? 'open'] ?? $statusConfig['open'];
                                        @endphp
                                        <div class="inline-flex items-center px-3 py-1 rounded {{ $config['bg'] }} {{ $config['border'] }} border">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $config['dot'] }} mr-2"></span>
                                            <span class="text-[10px] font-black {{ $config['text'] }} uppercase tracking-widest">
                                                {{ str_replace('_', ' ', $tender->work_status ?? 'open') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center space-x-2">

                                            <a href="{{ route('admin.tenders.match', $tender) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-black text-[10px] uppercase tracking-widest rounded shadow-sm animate-pulse">
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
    </div>
</x-app-layout>
