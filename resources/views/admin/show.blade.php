<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-red-600 font-bold uppercase text-xs transition">
                &larr; Back to Directory
            </a>
        </div>
    </x-slot>

    <div class="bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Status Messages --}}
            @if (session('status'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded shadow-sm font-bold">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Main Profile Card --}}
            <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden border border-gray-200 border-t-4 border-t-red-600">

                {{-- Header Section --}}
                <div class="bg-gray-900 p-8 text-white">
                    <h3 class="text-4xl font-black uppercase tracking-tight">{{ $subcon->company_name ?? 'Individual Professional' }}</h3>
                    <p class="text-red-400 font-bold mt-2 text-lg">{{ $subcon->name }} • {{ $subcon->email }}</p>
                </div>

                {{-- Status Controls --}}
                <div class="bg-gray-100 p-6 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-xs font-black uppercase text-gray-500 tracking-widest">Current Status:</span>
                        <span class="px-3 py-1 rounded text-[10px] font-black uppercase tracking-widest text-white
                            {{ $subcon->status === 'active' ? 'bg-emerald-600' : ($subcon->status === 'inactive' ? 'bg-red-600' : 'bg-amber-500') }}">
                            {{ $subcon->status ?? 'Pending' }}
                        </span>
                    </div>

                    <form action="{{ route('admin.subcon.update-status', $subcon->id) }}" method="POST" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <select name="status" class="rounded border-gray-300 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="pending" {{ $subcon->status == 'pending' ? 'selected' : '' }}>Set to Pending</option>
                            <option value="active" {{ $subcon->status == 'active' ? 'selected' : '' }}>Set to Active</option>
                            <option value="inactive" {{ $subcon->status == 'inactive' ? 'selected' : '' }}>Set to inactive</option>
                        </select>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition text-sm shadow-sm uppercase tracking-wider">
                            Update
                        </button>
                    </form>
                </div>

                {{-- Performance Rating Section --}}
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-8 border-b border-amber-100">
                    <h4 class="text-xs font-black text-amber-600 uppercase tracking-widest mb-6">Admin Performance Rating</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Rating Stars and Average --}}
                        <div class="flex flex-col items-center justify-center">
                            <div class="mb-4">
                                @if($subcon->review_count > 0)
                                    <div class="flex items-center justify-center space-x-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($subcon->average_rating))
                                                <svg class="w-8 h-8 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @elseif($i - 0.5 <= $subcon->average_rating)
                                                <svg class="w-8 h-8 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" opacity="0.5"/></svg>
                                            @else
                                                <svg class="w-8 h-8 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                @else
                                    <div class="flex items-center justify-center space-x-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-8 h-8 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            <p class="text-3xl font-black text-amber-600">{{ $subcon->average_rating > 0 ? $subcon->average_rating : 'N/A' }}</p>
                            <p class="text-sm text-amber-700 font-semibold mt-1">out of 5.0</p>
                        </div>

                        {{-- Review Count --}}
                        <div class="flex flex-col items-center justify-center border-l border-r border-amber-200">
                            <p class="text-4xl font-black text-amber-600">{{ $subcon->review_count }}</p>
                            <p class="text-xs text-amber-700 font-semibold uppercase tracking-wider mt-1">
                                {{ $subcon->review_count === 1 ? 'Review' : 'Reviews' }}
                            </p>
                        </div>

                        {{-- Last Review --}}
                        <div class="flex flex-col items-center justify-center">
                            @php
                                $latestReview = $subcon->reviews()->latest()->first();
                            @endphp
                            @if($latestReview)
                                <p class="text-sm text-amber-700 font-semibold uppercase tracking-wider mb-2">Latest Review</p>
                                <p class="text-2xl font-black text-amber-600">{{ $latestReview->rating }}</p>
                                <p class="text-xs text-gray-600 mt-2">{{ $latestReview->created_at->diffForHumans() }}</p>
                                @if($latestReview->review)
                                    <p class="text-xs text-gray-600 italic mt-3 text-center line-clamp-2">{{ $latestReview->review }}</p>
                                @endif
                            @else
                                <p class="text-sm text-gray-500 italic">No reviews yet</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Basic Stats --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">CIDB Grade</h4>
                        <p class="text-3xl font-black text-gray-900">
                            {{ is_array($subcon->cidb_grades) ? implode(', ', $subcon->cidb_grades) : $subcon->cidb_grades }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Established</h4>
                        <p class="text-3xl font-black text-gray-900">{{ $subcon->year_established ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr class="mx-8 border-gray-100">

                {{-- Business Registration Details --}}
                <div class="px-8 pt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="col-span-3">
                        <h4 class="text-xs font-black text-red-600 uppercase tracking-widest mb-4 border-b pb-2">Business Registration Details</h4>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">CIDB Reg. No</h4>
                        <p class="text-sm font-bold text-gray-900">{{ $subcon->cidb_reg_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">SSM Number</h4>
                        <p class="text-sm font-bold text-gray-900">{{ $subcon->ssm_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Company Level</h4>
                        <p class="text-sm font-bold text-gray-900">{{ $subcon->company_level ?? 'N/A' }}</p>
                    </div>
                    <div class="col-span-3">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Company Address</h4>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $subcon->company_address ?? 'No address provided' }}</p>
                    </div>
                    <div class="col-span-2">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Official Company Email</h4>
                        <p class="text-sm font-bold text-gray-900">{{ $subcon->company_email ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- PIC & Contact Details --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-black text-red-600 uppercase tracking-widest mb-4">Person In Charge (PIC)</h4>
                        <p class="text-sm font-bold text-gray-900">{{ $subcon->pic_name ?? 'N/A' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Office Phone</h4>
                            <p class="text-sm font-bold text-gray-900">{{ $subcon->office_phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">PIC Mobile</h4>
                            <p class="text-sm font-bold text-gray-900">{{ $subcon->pic_phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">PIC Email</h4>
                        <p class="text-sm font-bold text-gray-900">{{ $subcon->email ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Services Provided --}}
                <div class="px-8 pb-8">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Services Provided</h4>
                    <div class="bg-gray-50 p-6 rounded border border-gray-200 text-gray-700 leading-relaxed italic">
                        @if($subcon->services_provided)
                            @php
                                $services = is_array($subcon->services_provided) ? $subcon->services_provided : (string) $subcon->services_provided;
                            @endphp

                            @if(is_array($services))
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($services as $svc)
                                        <li class="text-sm text-gray-800">{{ $svc }}</li>
                                    @endforeach
                                </ul>
                            @else
                                {!! nl2br(e($services)) !!}
                            @endif
                        @else
                            <span class="text-gray-400">No services description provided by this subcon.</span>
                        @endif
                    </div>
                </div>

                <div class="px-8 pb-8">
                    <h4 class="text-xs font-black text-red-600 uppercase tracking-widest mb-4">Uploaded Documents</h4>

                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        @php
                            $pending = collect($subcon->pending_documents ?? []);
                        @endphp

                        @if($pending->isEmpty())
                            <div class="p-6 text-sm text-gray-500 italic">No uploaded documents.</div>
                        @else
                            <div class="space-y-4">
                                @foreach($pending as $document)
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded border">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $document['original_name'] ?? basename($document['path']) }}</p>
                                            <p class="text-xs text-gray-500">Uploaded: {{ \Illuminate\Support\Carbon::parse($document['uploaded_at'])->format('d M Y, H:i') }}</p>
                                            @if(!empty($document['type']))
                                                <p class="text-xs text-slate-400 mt-1">Type: {{ strtoupper($document['type']) }}</p>
                                            @endif
                                        </div>

                                        <div class="text-right">
                                            <div class="text-xs uppercase tracking-widest font-black {{ ($document['status'] ?? '') === 'approved' ? 'text-emerald-600' : (($document['status'] ?? '') === 'rejected' ? 'text-red-600' : 'text-amber-500') }}">
                                                {{ $document['status'] ?? 'pending' }}
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-red-600 hover:text-red-800 text-sm font-semibold">View / Download</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest border-t">
                    Registered on: {{ $subcon->created_at->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
