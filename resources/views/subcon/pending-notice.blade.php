<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white p-8 shadow-sm sm:rounded-lg">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4 animate-pulse">
                    <span class="text-3xl">⏳</span>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Registration Under Review</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Hi <span class="font-semibold text-gray-900">{{ auth()->user()->name }}</span>, your sub-contractor profile is currently being verified by our Admin team.
                </p>
                <p class="mt-4 text-sm text-amber-600 font-medium bg-amber-50 inline-block px-4 py-1.5 rounded-full">
                    You will gain full access to the dashboard once your status is set to "Active".
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-12">
        <div class="bg-white p-8 shadow-sm sm:rounded-2xl border border-gray-100">
            @if(session('success'))
                <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="mb-8">
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Upload Supporting Documents</h3>
                <p class="text-sm text-gray-500 mt-1">Stage and review files independently within each category box before finalizing your upload.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @php
                    $docTypes = [
                        'ssm' => ['title' => 'SSM Registration', 'desc' => 'Official company registration profile documentation.', 'icon' => '🏢'],
                        'cidb' => ['title' => 'CIDB Certificate', 'desc' => 'Valid CIDB green card or company certification documents.', 'icon' => '👷'],
                        'bank' => ['title' => 'Bank Statement', 'desc' => 'Recent corporate financial summaries or bank statements.', 'icon' => '📊'],
                        'other' => ['title' => 'Other Documents', 'desc' => 'Any additional supporting items required by the admin team.', 'icon' => '📁'],
                    ];
                @endphp

                @foreach($docTypes as $key => $meta)
                    <div class="bg-gray-50/60 p-6 rounded-2xl border border-gray-200/70 hover:border-indigo-200 transition-all shadow-xs flex flex-col justify-between"
                         x-data="cardUploadComponent('{{ $key }}')">

                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl p-2 bg-white rounded-xl shadow-xs border border-gray-100">{{ $meta['icon'] }}</span>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">{{ $meta['title'] }}</h4>
                                        <p class="text-xs text-gray-500 max-w-sm mt-0.5">{{ $meta['desc'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative mb-4">
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-white hover:border-indigo-400 hover:bg-indigo-50/10 transition-all">
                                    <div class="flex flex-col items-center justify-center pt-4 pb-4 px-2 text-center">
                                        <svg class="w-5 h-5 mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <p class="text-[11px] text-gray-500 font-medium">Click to select files to stage</p>
                                    </div>
                                    <input type="file"
                                           :id="'file_input_' + categoryKey"
                                           multiple
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                           class="hidden"
                                           @change="handleFileSelection($event)" />
                                </label>
                            </div>

                            <div x-show="stagedList.length > 0" x-cloak class="mb-4 space-y-2">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">Staged for Upload</p>
                                <div class="max-h-36 overflow-y-auto space-y-1.5 pr-1">
                                    <template x-for="(stagedFile, index) in stagedList" :key="index">
                                        <div class="p-2 border border-indigo-100 rounded-xl bg-white flex items-center justify-between gap-3 text-xs shadow-xs animate-fade-in">
                                            <div class="min-w-0 flex items-center gap-2">
                                                <span class="shrink-0">📄</span>
                                                <div class="truncate">
                                                    <p class="font-medium text-gray-800 truncate" x-text="stagedFile.name"></p>
                                                    <p class="text-[10px] text-gray-400" x-text="stagedFile.friendlySize"></p>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeStagedFile(index)" class="p-1 text-gray-400 hover:text-rose-600 rounded hover:bg-gray-50 transition-colors shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('subcon.pending-documents.upload') }}" method="POST" enctype="multipart/form-data" @submit="submitIntercept($event)">
                            @csrf
                            <input type="hidden" name="doc_type" value="{{ $key }}" />

                            <div :id="'hidden_inputs_' + categoryKey" class="hidden"></div>

                            <div class="flex items-center justify-between gap-4 pt-2 border-t border-gray-100">
                                <span class="text-[11px] text-gray-400 italic">Max size: 5MB</span>
                                <button type="submit"
                                        :disabled="stagedList.length === 0"
                                        :class="stagedList.length === 0 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-slate-900 hover:bg-indigo-600 text-white shadow-sm'"
                                        class="px-4 py-2 rounded-xl text-xs font-semibold tracking-wide transition-colors duration-150">
                                    Upload (<span x-text="stagedList.length">0</span>) Files
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-center text-xs text-gray-400">
                <span>Supported file formats: PDF, JPG, PNG, DOC, XLS</span>
            </div>
        </div>
    </div>

    @if(auth()->user()->pending_documents)
        <div class="pb-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-8 shadow-sm sm:rounded-2xl border border-gray-100">
                    <div class="flex items-center gap-2 mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Upload History & Status</h3>
                        <span class="bg-slate-100 text-slate-700 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ count(auth()->user()->pending_documents) }} Filed
                        </span>
                    </div>

                    @php
                        $pending = collect(auth()->user()->pending_documents ?? []);
                        $grouped = $pending->groupBy(function($d) { return $d['type'] ?? 'other'; });
                        $typesOrdered = ['ssm' => 'SSM Registration', 'cidb' => 'CIDB Certificate', 'bank' => 'Bank Statement', 'other' => 'Other Documents'];
                    @endphp

                    <div class="space-y-6">
                        @foreach($typesOrdered as $typeKey => $typeLabel)
                            <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">{{ $typeLabel }}</h5>

                                @if(isset($grouped[$typeKey]) && count($grouped[$typeKey]))
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($grouped[$typeKey] as $document)
                                            <div class="p-3 border border-gray-200/70 rounded-xl bg-white flex items-center justify-between gap-4 shadow-xs">
                                                <div class="min-w-0 flex items-center gap-3">
                                                    <div class="p-2 bg-slate-100 rounded-lg text-slate-500 shrink-0">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </div>
                                                    <div class="truncate">
                                                        <p class="font-medium text-sm text-gray-800 truncate">{{ $document['original_name'] ?? basename($document['path']) }}</p>
                                                        <p class="text-[11px] text-gray-400 mt-0.5">Uploaded: {{ \Carbon\Carbon::parse($document['uploaded_at'])->format('d M Y, h:i A') }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-end shrink-0 gap-2">
                                                    @php
                                                        $status = strtolower($document['status'] ?? 'pending');
                                                        $statusClasses = [
                                                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                            'pending'  => 'bg-amber-50 text-amber-700 border-amber-200'
                                                        ][$status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                                    @endphp
                                                    <span class="text-[10px] font-bold uppercase tracking-wide px-2.5 py-0.5 rounded-full border {{ $statusClasses }}">
                                                        {{ $status }}
                                                    </span>
                                                    <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold inline-flex items-center gap-1 transition-colors">
                                                        View file ↗
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 border border-dashed border-gray-200 rounded-xl bg-white text-xs text-gray-400 italic">
                                        No file uploaded yet.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function cardUploadComponent(key) {
            return {
                categoryKey: key,
                stagedList: [],

                handleFileSelection(event) {
                    const files = Array.from(event.target.files);
                    files.forEach(file => {
                        // Prevent adding the exact same file duplicate inside this specific card
                        const alreadyExists = this.stagedList.some(item => item.name === file.name && item.size === file.size);
                        if (!alreadyExists) {
                            this.stagedList.push({
                                fileInstance: file,
                                name: file.name,
                                friendlySize: (file.size / (1024 * 1024)).toFixed(2) + ' MB'
                            });
                        }
                    });
                },

                removeStagedFile(index) {
                    this.stagedList.splice(index, 1);
                    // Clear out browser input element state if user deleted everything manually
                    if(this.stagedList.length === 0) {
                        document.getElementById('file_input_' + this.categoryKey).value = '';
                    }
                },

                submitIntercept(event) {
                    // Stop submission if empty
                    if (this.stagedList.length === 0) {
                        event.preventDefault();
                        return;
                    }

                    // Dynamically attach the confirmed staged files list to the targeted card's submission form
                    const hiddenContainer = document.getElementById('hidden_inputs_' + this.categoryKey);
                    hiddenContainer.innerHTML = ''; // Fresh sync

                    const dataTransfer = new DataTransfer();
                    this.stagedList.forEach(item => {
                        dataTransfer.items.add(item.fileInstance);
                    });

                    // Construct a virtual input field layout matching standard array syntax
                    const virtualFileInput = document.createElement('input');
                    virtualFileInput.type = 'file';
                    virtualFileInput.multiple = true;
                    virtualFileInput.name = 'documents[]'; // Match your back-end handler variable array expectations
                    virtualFileInput.files = dataTransfer.files;

                    hiddenContainer.appendChild(virtualFileInput);
                }
            }
        }
    </script>
</x-app-layout>
