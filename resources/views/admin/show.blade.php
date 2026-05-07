<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-red-600 font-bold uppercase text-xs transition">
                &larr; Back to Directory
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
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

                {{-- Basic Stats --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">CIDB Grade</h4>
                        <p class="text-3xl font-black text-gray-900">
                            {{ is_array($subcon->grade) ? implode(', ', $subcon->grade) : $subcon->grade }}
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
                            <p class="text-sm font-bold text-gray-900">{{ $subcon->phone_office ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">PIC Mobile</h4>
                            <p class="text-sm font-bold text-gray-900">{{ $subcon->phone_pic ?? 'N/A' }}</p>
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
                        @if($subcon->services)
                            {!! nl2br(e($subcon->services)) !!}
                        @else
                            <span class="text-gray-400">No services description provided by this subcon.</span>
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
