<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white p-10 shadow-sm sm:rounded-lg border border-yellow-200">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Registration Under Review ⏳</h2>
                <p class="text-lg text-gray-600">
                    Hi {{ auth()->user()->name }}, your sub-contractor profile is currently being verified by our Admin team.
                </p>
                <p class="mt-4 text-sm text-gray-400 italic">
                    You will gain full access to the dashboard once your status is set to "Active".
                </p>
            </div>
        </div>
    </div>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-8 shadow-sm sm:rounded-lg border border-yellow-200">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <h3 class="text-2xl font-bold text-gray-800 mb-4">Upload supporting documents</h3>
            <p class="text-sm text-gray-500 mb-6">Upload the documents requested by admin for your account verification.</p>

            <form action="{{ route('subcon.pending-documents.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">
                    <label class="block text-sm font-semibold text-gray-700">Select files</label>
                    <input type="file" name="documents[]" multiple required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                        class="w-full rounded border border-gray-300 p-2" />

                    <p class="text-xs text-slate-500">Allowed: PDF, JPG, PNG, DOC, XLS. Max 5MB per file.</p>
                </div>

                <button type="submit" class="mt-6 px-5 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Upload Documents
                </button>
            </form>
        </div>
    </div>

    @if(auth()->user()->pending_documents)
        <div class="py-10">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-8 shadow-sm sm:rounded-lg border border-slate-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Uploaded Documents</h3>
                    <div class="space-y-4">
                        @foreach(auth()->user()->pending_documents as $document)
                            <div class="p-4 border rounded-lg bg-slate-50">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $document['original_name'] ?? basename($document['path']) }}</p>
                                        <p class="text-xs text-gray-500">Uploaded: {{ \Illuminate\Support\Carbon::parse($document['uploaded_at'])->format('d M Y H:i') }}</p>
                                    </div>
                                    <div class="text-sm uppercase tracking-widest text-slate-600">
                                        {{ $document['status'] ?? 'pending' }}
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="inline-block mt-3 text-sm text-red-600 hover:text-red-800">
                                    View / Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
