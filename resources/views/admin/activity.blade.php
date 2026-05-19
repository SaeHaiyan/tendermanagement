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
        <div class="space-y-4">
            @forelse($events as $event)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-400 font-black">{{ \Illuminate\Support\Carbon::parse($event['time'])->format('d M Y H:i') }}</p>
                            <h3 class="mt-2 text-lg font-bold text-slate-900">{{ $event['subcon'] ?? 'Subcontractor' }} uploaded {{ $event['category'] }}</h3>
                            <p class="text-sm text-slate-600">Project: <span class="font-semibold text-slate-800">{{ $event['tender'] }}</span></p>
                        </div>
                        <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-black uppercase tracking-[0.24em] {{ $event['status'] === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($event['status'] === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                            {{ $event['status'] ?? 'pending' }}
                        </span>
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
