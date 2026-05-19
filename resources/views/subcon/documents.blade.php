<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight uppercase">Upload Support Documents</h2>
                <p class="text-sm text-slate-500 mt-1">Submit incomplete documents so admin can verify your account and ongoing work.</p>
            </div>
            <a href="{{ route('subcon.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-700 hover:bg-slate-50 transition">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 space-y-6">
            @if(session('success'))
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('subcon.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 uppercase tracking-widest mb-2">Select files</label>
                    <input type="file" name="documents[]" multiple required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700" />
                    <p class="text-xs text-slate-500 mt-2">Allowed files: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX. Max 5MB each.</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold uppercase tracking-widest text-white hover:bg-slate-800 transition">
                        Upload Documents
                    </button>
                </div>
            </form>

            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-lg font-bold text-slate-900 mb-3">Uploaded Documents</h3>
                @if(count($pendingDocuments) > 0)
                    <div class="space-y-4">
                        @foreach($pendingDocuments as $document)
                            <div class="rounded-3xl bg-slate-50 border border-slate-200 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $document['original_name'] ?? basename($document['path']) }}</p>
                                    <p class="text-xs text-slate-500">Uploaded: {{ \Illuminate\Support\Carbon::parse($document['uploaded_at'])->format('d M Y H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.24em] text-amber-700">
                                        {{ $document['status'] ?? 'pending' }}
                                    </span>
                                    <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-slate-900 font-bold text-sm hover:text-slate-700">View</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-3xl bg-slate-50 border border-slate-200 p-10 text-center text-slate-500 font-bold uppercase tracking-widest">
                        No uploaded documents yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
