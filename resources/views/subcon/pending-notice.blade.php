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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $docTypes = [
                        'ssm' => 'SSM / Company Registration',
                        'cidb' => 'CIDB Certificate',
                        'bank' => 'Bank Statement / Financials',
                        'other' => 'Other',
                    ];
                @endphp

                @foreach($docTypes as $key => $label)
                    <div class="bg-slate-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-bold text-gray-800 mb-2">{{ $label }}</h4>
                        <p class="text-xs text-gray-500 mb-3">Upload your {{ strtolower($label) }} (if applicable).</p>
                        <form action="{{ route('subcon.pending-documents.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="doc_type" value="{{ $key }}" />
                            <div class="flex gap-3 items-center">
                                <input type="file" name="documents[]" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="text-sm" />
                                <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-bold">Upload</button>
                            </div>
                            <p class="text-xs text-slate-400 mt-2">Allowed: PDF, JPG, PNG, DOC, XLS. Max 5MB.</p>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(auth()->user()->pending_documents)
        <div class="py-10">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-8 shadow-sm sm:rounded-lg border border-slate-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Uploaded Documents</h3>
                    @php
                        $pending = collect(auth()->user()->pending_documents ?? []);
                        $grouped = $pending->groupBy(function($d) { return $d['type'] ?? 'other'; });
                    @endphp

                    <div class="space-y-6">
                        @foreach(['ssm','cidb','bank','other'] as $type)
                            <div>
                                <h5 class="text-sm font-black text-slate-600 mb-3">{{ ucfirst($type) }} Documents</h5>
                                @if(isset($grouped[$type]) && count($grouped[$type]))
                                    <div class="space-y-3">
                                        @foreach($grouped[$type] as $document)
                                            <div class="p-4 border rounded-lg bg-slate-50 flex items-center justify-between">
                                                <div>
                                                    <p class="font-semibold text-gray-800">{{ $document['original_name'] ?? basename($document['path']) }}</p>
                                                    <p class="text-xs text-gray-500">Uploaded: {{ \Carbon\Carbon::parse($document['uploaded_at'])->format('d M Y H:i') }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-xs font-black uppercase {{ ($document['status'] ?? '') === 'approved' ? 'text-emerald-600' : (($document['status'] ?? '') === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                                                        {{ $document['status'] ?? 'pending' }}
                                                    </div>
                                                    <div class="mt-2">
                                                        <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-indigo-600 text-xs font-bold">View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 border rounded-lg bg-white text-xs text-slate-400">No {{ $type }} documents uploaded.</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
