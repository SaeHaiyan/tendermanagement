<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                Subcon <span class="text-indigo-600">Workforce Portal</span>
            </h2>
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Company</p>
                <p class="text-sm font-bold text-slate-700">{{ auth()->user()->company_name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen" x-data="{ tab: 'active' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. Performance Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Tasks</p>
                    <p class="text-3xl font-black text-slate-900">{{ $activeProjects->count() }}</p>
                </div>
                <div class="bg-indigo-600 p-6 rounded-3xl shadow-xl shadow-indigo-100">
                    <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest">Total Completed</p>
                    <p class="text-3xl font-black text-white">{{ $completedProjects->count() }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg. Progress</p>
                    <p class="text-3xl font-black text-slate-900">{{ number_format($activeProjects->avg('progress_percent') ?? 0, 0) }}%</p>
                </div>
            </div>

            {{-- 2. Tab Switcher --}}
            <div class="flex space-x-2 mb-8 bg-slate-200/50 p-1.5 rounded-2xl w-fit">
                <button @click="tab = 'active'" :class="tab === 'active' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
                    Active Projects
                </button>
                <button @click="tab = 'history'" :class="tab === 'history' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
                    Project History
                </button>
            </div>

            {{-- 3. Active Projects Tab --}}
            <div x-show="tab === 'active'" x-transition>
                <div class="grid grid-cols-1 gap-6">
                    @forelse($activeProjects as $project)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-8">

                            {{-- REJECTION ALERT CENTER --}}
                            @php
                                $reportData = is_array($project->report_path) ? $project->report_path : json_decode($project->report_path ?? '', true) ?? [];
                                $submittedFiles = $reportData['files'] ?? [];
                                $hasRejections = false;
                                foreach($submittedFiles as $cat => $items) {
                                    foreach($items as $f) { if(isset($f['status']) && $f['status'] === 'rejected') $hasRejections = true; }
                                }
                            @endphp

                            @if($hasRejections)
                                <div class="mb-8 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-2xl">
                                    <h5 class="text-rose-700 font-black text-[10px] uppercase tracking-widest mb-1">Action Required</h5>
                                    <p class="text-rose-600 text-xs">Some of your submitted documents were rejected. Please check the feedback below and re-upload.</p>
                                </div>
                            @endif

                            <div class="flex flex-col lg:flex-row gap-8">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-black uppercase rounded-lg">#{{ $project->id }}</span>
                                        <span class="text-slate-400 text-xs font-bold italic">Due: {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</span>
                                    </div>
                                    <h4 class="text-2xl font-black text-slate-900">{{ $project->title }}</h4>

                                    {{-- REJECTION FEEDBACK LIST --}}
                                    <div class="mt-6 space-y-3">
                                        @foreach($submittedFiles as $category => $items)
                                            @foreach($items as $index => $file)
                                                @if(is_array($file) && ($file['status'] ?? '') === 'rejected')
                                                    <div class="bg-white border border-rose-200 rounded-2xl p-4 shadow-sm">
                                                        <p class="text-[9px] font-black text-rose-500 uppercase mb-1">{{ str_replace('_', ' ', $category) }} Rejection Feedback:</p>
                                                        <p class="text-xs text-slate-700 italic mb-3">"{{ $file['feedback'] }}"</p>

                                                        <form action="{{ route('subcon.tenders.replace-file', $project->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="category" value="{{ $category }}">
                                                            <input type="hidden" name="file_index" value="{{ $index }}">
                                                            <div class="flex gap-2">
                                                                <input type="file" name="replacement" required class="text-[10px] flex-1">
                                                                <button class="bg-rose-500 text-white px-4 py-1.5 rounded-lg text-[9px] font-black uppercase">Replace</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>

                                    <div class="mt-8">
                                        <div class="flex justify-between mb-2 items-end">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Submission Progress</span>
                                            <span class="text-lg font-bold text-indigo-600">{{ $project->progress_percent }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                                            <div class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ $project->progress_percent }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- UPLOAD COLUMN --}}
                                <div class="lg:w-80 lg:border-l border-slate-100 lg:pl-8 space-y-6">

                                    {{-- SECTION 1: SITE PHOTOS --}}
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                                        <label class="text-[10px] font-black text-slate-700 uppercase tracking-widest mb-3 block">Site Progress Photos</label>
                                        @if(isset($submittedFiles['site_photos']) && !collect($submittedFiles['site_photos'])->contains('status', 'rejected'))
                                            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-xl text-[10px] font-bold flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                                Uploaded ({{ count($submittedFiles['site_photos']) }})
                                            </div>
                                        @else
                                            <form action="{{ route('subcon.tenders.update-progress', $project->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="category_type" value="site_photos">
                                                <input type="file" name="documents[]" multiple class="w-full text-[10px] mb-3">
                                                <button class="w-full py-2 bg-indigo-600 text-white text-[10px] font-black uppercase rounded-lg">Upload Photos</button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- SECTION 2: FINANCIALS --}}
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                                        <label class="text-[10px] font-black text-slate-700 uppercase tracking-widest mb-3 block">Financial Claims</label>
                                        @if(isset($submittedFiles['financial_docs']) && !collect($submittedFiles['financial_docs'])->contains('status', 'rejected'))
                                            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-xl text-[10px] font-bold flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                                Uploaded
                                            </div>
                                        @else
                                            <form action="{{ route('subcon.tenders.update-progress', $project->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="category_type" value="financial_docs">
                                                <input type="file" name="documents[]" multiple class="w-full text-[10px] mb-3">
                                                <button class="w-full py-2 bg-emerald-600 text-white text-[10px] font-black uppercase rounded-lg">Submit Financials</button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- SECTION 3: INVOICES --}}
                                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                                        <label class="text-[10px] font-black text-slate-700 uppercase tracking-widest mb-3 block">Invoices</label>
                                        @if(isset($submittedFiles['invoices']) && !collect($submittedFiles['invoices'])->contains('status', 'rejected'))
                                            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-xl text-[10px] font-bold flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                                Uploaded
                                            </div>
                                        @else
                                            <form action="{{ route('subcon.tenders.update-progress', $project->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="category_type" value="invoices">
                                                <input type="file" name="documents[]" multiple class="w-full text-[10px] mb-3">
                                                <button class="w-full py-2 bg-blue-600 text-white text-[10px] font-black uppercase rounded-lg">Submit Invoices</button>
                                            </form>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl border border-slate-200 p-20 text-center text-slate-400 font-bold italic">No active projects assigned.</div>
                    @endforelse
                </div>
            </div>

            {{-- 4. History Tab (Keep as is) --}}
            <div x-show="tab === 'history'" x-transition style="display: none;">
                {{-- ... rest of your history code ... --}}
            </div>

        </div>
    </div>
</x-app-layout>
