++<?blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl">Notifications</h2>
            <form method="POST" action="{{ route('admin.notifications.readAll') }}">@csrf<button type="submit" class="text-sm px-3 py-2 bg-slate-100 rounded">Mark all read</button></form>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6">
        @if($items->isEmpty())
            <div class="text-sm text-gray-500">No notifications.</div>
        @else
            <div class="space-y-3">
                @foreach($items as $item)
                    <div class="p-4 bg-white rounded border {{ $item['read'] ? 'opacity-60' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($item['created_at'])->diffForHumans() }}</p>
                                <p class="font-semibold">{{ $item['subcon'] ?? 'Subcontractor' }} — {{ $item['message'] ?? '' }}</p>
                                @if(!empty($item['link']))
                                    <a href="{{ $item['link'] }}" class="text-red-600 text-sm">Open</a>
                                @endif
                            </div>
                            <div class="text-right">
                                @if(empty($item['read']))
                                    <form method="POST" action="{{ route('admin.notifications.read', $item['id']) }}">@csrf<button class="text-sm px-3 py-2 rounded bg-amber-50">Mark read</button></form>
                                @else
                                    <div class="text-xs text-slate-400">Read</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
