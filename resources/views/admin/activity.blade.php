<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight uppercase">Activity Feed</h2>
                <p class="text-sm text-slate-500 mt-1">Recent subcontractor uploads and submissions requiring your attention.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-700 hover:bg-slate-50 transition">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="text-sm text-slate-500">Showing recent activity. Use the filters to narrow results.</div>

            <form method="GET" action="{{ route('admin.activity') }}" class="flex items-center gap-2">
                <label class="sr-only" for="start_date">Start date</label>
                <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">

                <label class="sr-only" for="end_date">End date</label>
                <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">

                <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold uppercase tracking-widest text-slate-700 hover:bg-slate-50">Filter</button>
                <a href="{{ route('admin.activity') }}" class="inline-flex items-center gap-2 rounded-xl border border-transparent bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600">Clear</a>
            </form>
        </div>

        <div class="space-y-4">
            @forelse($events as $event)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-400 font-black">{{ \Illuminate\Support\Carbon::parse($event['time'])->format('d M Y H:i') }}</p>

                            @if(isset($event['type']) && $event['type'] === 'profile_update')
                                <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $event['subcon'] ?? 'Subcontractor' }} updated profile</h3>
                                <p class="text-sm text-slate-600">Profile updated by <span class="font-semibold text-slate-800">{{ $event['uploader'] }}</span></p>
                            @else
                                <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $event['subcon'] ?? 'Subcontractor' }} uploaded {{ $event['category'] }}</h3>
                                <p class="text-sm text-slate-600">Project: <span class="font-semibold text-slate-800">{{ $event['tender'] ?? '—' }}</span></p>
                            @endif
                        </div>

                        @php
                            $status = $event['status'] ?? 'pending';
                            $badgeClass = $status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700');
                            if(isset($event['type']) && $event['type'] === 'profile_update') {
                                $badgeClass = 'bg-sky-50 text-sky-700';
                            }
                        @endphp

                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-black uppercase tracking-[0.24em] {{ $badgeClass }}">
                                {{ $event['status'] ?? (isset($event['type']) && $event['type'] === 'profile_update' ? 'updated' : 'pending') }}
                            </span>

                            <div class="flex items-center gap-2">
                                @if(isset($event['type']) && $event['type'] === 'profile_update' && !empty($event['subcon_id']))
                                    <a href="{{ route('admin.subcon.show', $event['subcon_id']) }}" class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">View Profile</a>
                                @endif

                                @if(isset($event['type']) && $event['type'] === 'tender_file')
                                    @if(!empty($event['tender_id']))
                                        <a href="{{ route('admin.tenders.show', $event['tender_id']) }}" class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">View Tender</a>
                                    @endif
                                    @if(!empty($event['path']))
                                        <a href="{{ asset('storage/' . $event['path']) }}" target="_blank" class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">View Document</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-400 font-bold uppercase tracking-widest">
                    No recent subcontractor activity found.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
