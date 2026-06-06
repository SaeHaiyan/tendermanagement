<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('subcon.dashboard') }}" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400 hover:text-slate-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Project <span class="text-indigo-600">Submissions</span></h2>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN: STATUS & REJECTIONS --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Project Card --}}
                    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-1">Project #{{ $project->id }}</p>
                        <h1 class="text-2xl font-black text-slate-900 mb-6 leading-tight">{{ $project->title }}</h1>

                        <div class="space-y-4 mb-6">
                            <div>
                                <div class="flex justify-between mb-2 items-end">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Overall Progress</span>
                                    <span class="text-lg font-bold text-indigo-600">{{ $project->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                                    <div class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ $project->progress_percent }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Deadline</p>
                                <p class="text-sm font-bold text-rose-600">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
                                <span class="text-[10px] px-2 py-1 bg-slate-100 rounded-md font-bold text-slate-600 uppercase">{{ $project->work_status }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Rejection Alerts --}}
                    @if($hasRejections)
                        <div class="bg-rose-50 border border-rose-200 rounded-3xl p-6 ring-4 ring-rose-50">
                            <h3 class="text-rose-700 font-black text-[10px] uppercase tracking-widest mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                Action Required
                            </h3>
                            <div class="space-y-4">
                                @foreach($submittedFiles as $category => $items)
                                    @foreach($items as $index => $file)
                                        @if(is_array($file) && ($file['status'] ?? '') === 'rejected')
                                            <div class="bg-white border border-rose-100 rounded-2xl p-4 shadow-sm">
                                                <p class="text-[9px] font-black text-rose-500 uppercase mb-1">{{ str_replace('_', ' ', $category) }}</p>
                                                <p class="text-xs text-slate-700 italic mb-3">"{{ $file['feedback'] }}"</p>

                                                <form action="{{ route('subcon.tenders.replace-file', $project->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="category" value="{{ $category }}">
                                                    <input type="hidden" name="file_index" value="{{ $index }}">
                                                    <input type="file" name="replacement" required
                                                        class="text-[10px] w-full mb-2 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer transition-all">
                                                    <button class="w-full bg-rose-500 text-white py-2 rounded-lg text-[10px] font-black uppercase hover:bg-rose-600 transition-all active:scale-95 shadow-sm">Replace File</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- RIGHT COLUMN: UPLOAD WORKSPACE --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Section 1: SITE PHOTOS --}}
                    <div class="group bg-white p-8 rounded-3xl border border-slate-200 shadow-sm transition-all hover:border-indigo-400 hover:shadow-md">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-slate-900">Site Progress Photos</h4>
                                    <p class="text-xs text-slate-400">Upload JPG/PNG images of current work status.</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('subcon.tenders.update-progress', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf @method('PATCH')
                            <input type="hidden" name="category_type" value="site_photos">

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Select Files</label>
                                <input type="file" name="documents[]" multiple required
                                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 file:font-bold file:cursor-pointer hover:file:bg-indigo-50 hover:file:text-indigo-700 transition-all">
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Submission Notes</label>
                                <textarea name="description" rows="2" placeholder="e.g. Completed ground floor column casting..."
                                    class="w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-slate-300 transition-all"></textarea>
                            </div>

                            <button class="w-full bg-sky-700 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg active:scale-[0.98]">
                                Upload Progress Photos
                            </button>
                        </form>
                    </div>

                    {{-- Section 2: FINANCIAL CLAIMS --}}
                    <div class="group bg-white p-8 rounded-3xl border border-slate-300 hover:border-amber-500 hover:shadow-md">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-slate-900">Financial Claims</h4>
                                    <p class="text-xs text-slate-400">Measurement sheets or progress claim documents.</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('subcon.tenders.update-progress', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf @method('PATCH')
                            <input type="hidden" name="category_type" value="financial_docs">

                            <input type="file" name="documents[]" multiple required
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 file:font-bold file:cursor-pointer hover:file:bg-amber-400 hover:file:text-amber-400 transition-all">

                            <textarea name="description" rows="2" placeholder="Details regarding this month's measurements..."
                                class="w-full rounded-2xl border-slate-200 text-sm focus:border-amber-500 focus:ring-amber-500 placeholder:text-slate-300 transition-all"></textarea>

                            <button class="w-full bg-amber-400 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg active:scale-[0.98]">
                                Submit Financial Claim
                            </button>
                        </form>
                    </div>

                    {{-- Section 3: INVOICES --}}
                    <div class="group bg-white p-8 rounded-3xl border border-slate-200 shadow-sm transition-all hover:border-emerald-400 hover:shadow-md">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-slate-900">Invoices</h4>
                                    <p class="text-xs text-slate-400">Formal tax invoices for payment processing.</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('subcon.tenders.update-progress', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf @method('PATCH')
                            <input type="hidden" name="category_type" value="invoices">

                            <input type="file" name="documents[]" multiple required
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 file:font-bold file:cursor-pointer hover:file:bg-emerald-50 hover:file:text-emerald-700 transition-all">

                            <textarea name="description" rows="2" placeholder="Invoice #REF, Bank details, or other notes..."
                                class="w-full rounded-2xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 placeholder:text-slate-300 transition-all"></textarea>

                            <button class="w-full bg-emerald-600 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg active:scale-[0.98]">
                                Submit Final Invoice
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
