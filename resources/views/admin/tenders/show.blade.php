<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.tenders.index') }}" class="text-gray-500 hover:text-red-600 font-bold uppercase text-xs transition">
                &larr; Back to Directory
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen" x-data="{ activePdf: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- --- SUCCESS ALERT --- --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 font-bold rounded-r-xl shadow-sm flex items-center gap-3" id="success-alert">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>

            {{-- Script untuk reload otomatis setelah 1.5 detik --}}
            <script>
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            </script>
        @endif

            {{-- Breadcrumbs & Header --}}
            <div class="flex md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <nav class="flex text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">
                        <a href="{{ route('admin.tenders.index') }}" class="hover:text-indigo-600 transition">Tender Board</a>
                        <span class="mx-2">/</span>
                        <span class="text-slate-600">Project Monitoring</span>
                    </nav>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center">
                        {{ $tender->title }}
                        @if($tender->work_status === 'completed')
                            <svg class="w-6 h-6 ml-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        @endif
                    </h2>
                </div>

                @if($tender->work_status === 'under_review' || $tender->work_status === 'completed')
                    <div class="flex flex-wrap gap-3">
                        @php
                            $reportData = is_array($tender->report_path) ? $tender->report_path : json_decode($tender->report_path ?? '', true) ?? [];
                            $categories = $reportData['files'] ?? [];
                        @endphp

                        @foreach($categories as $catName => $items)
                            @foreach($items as $idx => $f)
                                @php
                                    $p = is_array($f) ? ($f['path'] ?? '') : $f;
                                @endphp
                                @if($p)
                                    <button @click="activePdf = '{{ Storage::url($p) }}'"
                                        class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl font-bold text-sm hover:bg-indigo-50 hover:border-indigo-200 transition shadow-sm flex items-center group">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg>
                                        Review {{ ucfirst(str_replace('_', ' ', $catName)) }} {{ count($items) > 1 ? ($idx + 1) : '' }}
                                    </button>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:grid-cols-3 gap-8">
                {{-- Live PDF Viewer --}}
                <div x-show="activePdf" x-cloak x-transition class="bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-2xl border-8 border-white mb-8">
                    <div class="bg-slate-800 px-6 py-3 flex justify-between items-center">
                        <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest">Document Preview Mode</span>
                        <button @click="activePdf = null" class="text-slate-400 hover:text-white text-xs font-bold uppercase tracking-widest flex items-center gap-1">
                            Close Preview <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                        </button>
                    </div>
                    <div class="h-[600px] bg-slate-200">
                        <iframe :src="activePdf" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                </div>

                {{-- Detailed File Review --}}
                @if(!empty($categories))
                <div class="space-y-6">
                    @foreach($categories as $category => $items)
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                            <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4 border-b pb-2">
                                {{ str_replace('_', ' ', $category) }}
                            </h5>
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($items as $index => $file)
                                    @php
                                        $filePath = is_array($file) ? ($file['path'] ?? '') : $file;
                                        $fileStatus = is_array($file) ? ($file['status'] ?? 'pending') : 'pending';
                                        $fileFeedback = is_array($file) ? ($file['feedback'] ?? '') : '';
                                    @endphp
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-2xl {{ $fileStatus === 'rejected' ? 'bg-rose-50 border border-rose-100' : 'bg-slate-50 border border-slate-100' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2"/></svg>
                                            </div>
                                            <div>
                                                <a href="{{ Storage::url($filePath) }}" target="_blank" class="text-sm font-bold text-indigo-600 hover:underline">View Document</a>
                                                <p class="text-[9px] uppercase font-bold text-slate-400">Status: <span class="{{ $fileStatus === 'rejected' ? 'text-rose-500' : 'text-emerald-500' }}">{{ $fileStatus }}</span></p>
                                            </div>
                                        </div>

                                        @if($fileStatus !== 'rejected' && $tender->work_status !== 'completed')
                                            <form action="{{ route('admin.tenders.reject-file', $tender->id) }}" method="POST" class="flex flex-1 gap-2 items-center">
                                                @csrf
                                                <input type="hidden" name="category" value="{{ $category }}">
                                                <input type="hidden" name="file_index" value="{{ $index }}">
                                                <textarea name="feedback" placeholder="Reason for rejection..." class="flex-1 text-[11px] border-slate-200 rounded-xl focus:ring-rose-500 py-2" rows="1" required></textarea>
                                                <button type="submit" class="bg-rose-500 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-rose-600 transition shadow-sm">Reject</button>
                                            </form>
                                        @elseif($fileStatus === 'rejected')
                                            <div class="flex-1 bg-white p-3 rounded-xl border border-rose-100 shadow-inner">
                                                <p class="text-[10px] font-black text-rose-500 uppercase mb-1">Feedback Given:</p>
                                                <p class="text-xs text-slate-600 italic">"{{ $fileFeedback }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                <div class="space-y-8">

                    <div class="grid sm:grid-cols-5 gap-7">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Required Grade</p>
                            <p class="text-xl font-black text-slate-900">Grade {{ $tender->required_grade }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Timeline Status</p>
                            @php
                                $deadline = \Carbon\Carbon::parse($tender->deadline);
                                $daysLeft = (int) now()->diffInDays($deadline, false);
                            @endphp
                            <p class="text-xl font-black {{ $daysLeft < 0 ? 'text-red-500' : 'text-amber-500' }}">
                                {{ $daysLeft < 0 ? 'Overdue' : $daysLeft . ' Days Left' }}
                            </p>
                        </div>
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Subcon Quality</p>
                            <p class="text-xl font-black text-indigo-600">Verified</p>
                        </div>
                    </div>

                    {{-- Column 3: Progress & Partner --}}
                    {{-- FINAL APPROVAL ACTION (NEW) --}}
                    @if($tender->work_status === 'under_review')
                        <div class="bg-emerald-500 rounded-[2rem] p-8 text-white shadow-xl shadow-emerald-200">
                            <h4 class="text-[10px] font-black uppercase tracking-widest mb-4 opacity-80">Action Required</h4>
                            <p class="text-xs font-bold mb-6">All documents have been submitted for final review.</p>
                            <form action="{{ route('admin.tenders.approve', $tender->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    onclick="return confirm('Confirm project completion?')"
                                    class="w-full bg-white text-emerald-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-50 transition shadow-lg">
                                    Approve & Complete
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
                            <div class="relative z-10">
                                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em] mb-6">Real-time Progress</p>
                                <div class="flex items-baseline gap-2 mb-4">
                                    <span class="text-7xl font-black tracking-tighter">{{ $tender->progress_percent }}</span>
                                    <span class="text-2xl font-bold text-indigo-400">%</span>
                                </div>
                                <div class="w-full bg-white/10 h-3 rounded-full mb-8 p-0.5">
                                    <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000 ease-out {{ $tender->progress_percent < 100 ? 'glow-bar' : '' }}"
                                            style="width: {{ $tender->progress_percent }}%"></div>
                                </div>
                                <div class="flex justify-between items-center bg-white/5 p-4 rounded-2xl border border-white/10">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Phase Status</span>
                                    <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest px-2 py-1 bg-indigo-500/20 rounded-md">
                                        {{ str_replace('_', ' ', $tender->work_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2rem] p-8 border border-slate-200 shadow-sm relative">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Assigned Partner</p>
                            <div class="flex items-center space-x-4 mb-8">
                                <div class="w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-2xl shadow-inner">
                                    {{ substr($tender->selectedSubcon->company_name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <h4 class="font-black text-slate-900 leading-tight truncate">{{ $tender->selectedSubcon->company_name ?? 'No Subcon' }}</h4>
                                    <p class="text-[10px] text-indigo-600 font-black uppercase tracking-tighter mt-1">
                                        Grade:
                                        @if($tender->selectedSubcon && $tender->selectedSubcon->cidb_grades)
                                            {{ is_array($tender->selectedSubcon->cidb_grades) ? implode(', ', $tender->selectedSubcon->cidb_grades) : $tender->selectedSubcon->cidb_grades }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Reassign Button - Only visible if not completed --}}
                            @if($tender->work_status !== 'completed')
                                <div class="mt-8 pt-4 border-t border-slate-50" x-data="{ reassignModal: false }">
                                    <button @click="reassignModal = true"
                                        type="button"
                                        class="w-full bg-slate-50 hover:bg-red-50 hover:text-red-600 text-slate-400 py-3 rounded-2xl font-black text-[9px] uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Reassign Project
                                    </button>

                                    <div x-show="reassignModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                        <div @click.away="reassignModal = false" class="bg-white rounded-2xl p-6 w-full max-w-md">
                                            <h3 class="text-lg font-bold text-slate-900 mb-4">Reassign Project</h3>

                                            <form action="{{ route('admin.tenders.reassign', $tender->id) }}" method="POST" class="space-y-4">
                                                @csrf
                                                @method('PATCH')

                                                <label class="block text-sm font-medium text-slate-700 mb-1">Select New Subcontractor</label>
                                                <select name="selected_subcon_id" required class="w-full border-slate-300 rounded-xl focus:ring-indigo-500 py-2">
                                                    <option value="" disabled selected>Choose a subcontractor</option>
                                                    @foreach($subcons as $subcon)
                                                        <option value="{{ $subcon->id }}" {{ $tender->selected_subcon_id == $subcon->id ? 'selected' : '' }}>
                                                            {{ $subcon->company_name }} (Grade: {{ is_array($subcon->cidb_grades) ? implode(', ', $subcon->cidb_grades) : $subcon->cidb_grades }})
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="reassignModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">Confirm Reassignment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
