<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight uppercase">Pending Approvals</h2>
                <p class="text-sm text-slate-500 mt-1">Review subcontractor accounts waiting for admin verification.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-700 hover:bg-slate-50 transition">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase text-xs tracking-widest font-black">
                        <tr>
                            <th class="px-6 py-4">Company</th>
                            <th class="px-6 py-4">PIC</th>
                            <th class="px-6 py-4">Submitted</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pendingSubcontractors as $subcon)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900">{{ $subcon->company_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500 uppercase tracking-[0.18em]">{{ $subcon->company_level ?? 'CIDB N/A' }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900">{{ $subcon->name }}</div>
                                    <div class="text-sm text-slate-500">{{ $subcon->email }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-500">{{ optional($subcon->created_at)->format('d M Y') }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.subcon.show', $subcon->id) }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition">
                                            Review
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-slate-400 font-bold uppercase tracking-widest">
                                    No subcontractors are waiting for approval.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
