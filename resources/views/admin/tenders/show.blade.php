<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.tenders.index') }}" class="text-gray-500 hover:text-red-600 font-bold uppercase text-xs transition">
                &larr; Back to Directory
            </a>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen" x-data="{ activePdf: null, reassignModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- --- SUCCESS ALERT --- --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 font-bold rounded-r-xl shadow-sm flex items-center gap-3" id="success-alert">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() { window.location.reload(); }, 1500);
            </script>
            @endif

            {{-- Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <nav class="flex text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">
                        <a href="{{ route('admin.tenders.index') }}" class="hover:text-indigo-600 transition">Tender Board</a>
                        <span class="mx-2">/</span>
                        <span class="text-slate-600">Project Monitoring</span>
                    </nav>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight flex items-center">
                        {{ $tender->title }}
                    </h2>
                </div>

                {{-- DYNAMIC SINGLE TENDER DOCUMENT GENERATION UTILITIES --}}
                <div class="flex items-center gap-2 self-start md:self-auto">
                    <a href="{{ route('admin.tenders.export-single', ['tender' => $tender->id, 'format' => 'excel']) }}"
                       class="flex items-center gap-2 border border-slate-200 hover:border-emerald-200 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 px-4 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition shadow-sm bg-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                        Export Sheet
                    </a>
                    <a href="{{ route('admin.tenders.export-single', ['tender' => $tender->id, 'format' => 'pdf']) }}"
                       class="flex items-center gap-2 border border-slate-200 hover:border-slate-900 hover:bg-slate-950 hover:text-white px-4 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition shadow-sm bg-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                        Print Summary
                    </a>
                </div>
            </div>

            {{-- Real-time Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-slate-900 p-6 rounded-3xl text-white shadow-xl shadow-slate-200">
                    <p class="text-[15px] font-black text-indigo-300 uppercase tracking-widest mb-1">Total Progress</p>
                    <div class="flex items-end gap-1">
                        <span class="text-3xl font-black leading-none">{{ $tender->progress_percent }}</span>
                        <span class="text-sm font-bold text-indigo-400">%</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <p class="text-[15px] font-black text-slate-400 uppercase tracking-widest mb-1">Required Grade</p>
                    <p class="text-xl font-black text-slate-900 uppercase">{{ $tender->required_grade }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    @php $deadline = \Carbon\Carbon::parse($tender->deadline); $daysLeft = (int) now()->diffInDays($deadline, false); @endphp
                    <p class="text-[15px] font-black text-slate-400 uppercase tracking-widest mb-1">Remaining Time</p>
                    <p class="text-xl font-black {{ $daysLeft < 0 ? 'text-red-500' : 'text-amber-500' }}">
                        {{ $daysLeft < 0 ? 'OVERDUE' : $daysLeft . ' Days' }}
                    </p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 col-span-1 md:col-span-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[15px] font-black text-slate-400 uppercase tracking-widest mb-1">Partner Subcon</p>
                            <p class="text-XL font-black text-slate-900 group-hover:text-indigo-600 transition truncate">{{ $tender->selectedSubcon->company_name ?? 'No Subcon Assigned' }}</p>
                        </div>
                        <button @click="reassignModal = true" class="text-XL font-bold text-slate-500 hover:text-indigo-600 transition">Reassign</button>
                    </div>

                </div>
            </div>

            {{-- PDF Previewer --}}
            <div x-show="activePdf" x-cloak x-transition class="bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-2xl border-8 border-white mb-8">
                <div class="bg-slate-800 px-6 py-3 flex justify-between items-center text-white">
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-300">Document Review Mode</span>
                    <button @click="activePdf = null" class="text-xs font-black uppercase hover:text-red-400 transition flex items-center gap-1">Close Preview</button>
                </div>
                <iframe :src="activePdf" class="w-full h-[600px]" frameborder="0"></iframe>
            </div>

            {{-- Main Workspace --}}
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    @php
                        $reportData = is_array($tender->report_path) ? $tender->report_path : json_decode($tender->report_path ?? '', true) ?? [];
                        $categories = $reportData['files'] ?? [];
                    @endphp

                    @forelse(['site_photos' => 'Site Progress Photos', 'financial_docs' => 'Financial Claims', 'invoices' => 'Tax Invoices'] as $key => $label)
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                            <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4 border-b pb-2">{{ $label }}</h5>
                            <div class="space-y-3">
                                @forelse($categories[$key] ?? [] as $index => $file)
                                    @php
                                        $fPath = is_array($file) ? ($file['path'] ?? '') : $file;
                                        $fStatus = is_array($file) ? ($file['status'] ?? 'pending') : 'pending';
                                        $fFeedback = is_array($file) ? ($file['feedback'] ?? '') : '';
                                    @endphp
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-2xl {{ $fStatus === 'rejected' ? 'bg-rose-50 border border-rose-100' : 'bg-slate-50 border border-slate-100' }}" x-data="{ reviewOpen: false }">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2"/></svg>
                                            </div>
                                            <div>
                                                <button @click="activePdf = '{{ Storage::url($fPath) }}'" class="text-sm font-bold text-indigo-600 hover:underline text-left">Review Submission #{{ $index + 1 }}</button>
                                                <p class="text-[15px] uppercase font-bold text-slate-400 tracking-widest">Status: <span class="{{ $fStatus === 'rejected' ? 'text-rose-500' : 'text-emerald-500' }}">{{ $fStatus }}</span></p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            @if($fStatus === 'rejected')
                                                <div class="text-[10px] text-rose-500 italic max-w-[200px] text-right">"{{ $fFeedback }}"</div>
                                            @endif
                                            @if($tender->work_status !== 'completed')
                                                @if($fStatus !== 'approved' && $fStatus !== 'rejected')
                                                    <form action="{{ route('admin.tenders.update-file-status', $tender->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="category" value="{{ $key }}">
                                                        <input type="hidden" name="index" value="{{ $index }}">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-[10px] uppercase tracking-widest rounded shadow-sm">Approve</button>
                                                    </form>
                                                    <button @click="reviewOpen = !reviewOpen" class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-slate-100 transition">Review</button>
                                                @endif
                                            @endif
                                        </div>

                                        <div x-show="reviewOpen" x-transition class="w-full mt-4 pt-4 border-t border-slate-200">
                                            <form action="{{ route('admin.tenders.reject-file', $tender->id) }}" method="POST" class="flex gap-2">
                                                @csrf
                                                <input type="hidden" name="category" value="{{ $key }}">
                                                <input type="hidden" name="file_index" value="{{ $index }}">
                                                <input name="feedback" required placeholder="Reason (required) — explain what to fix" class="flex-1 text-xs border-slate-200 rounded-xl focus:ring-rose-500 py-2">
                                                <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase">Reject Submission</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-[10px] text-slate-300 font-bold uppercase italic py-4">No documents submitted</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Action Sidebar --}}
                <div class="space-y-6">
                    @if($tender->work_status === 'under_review')
                        <div class="bg-emerald-500 rounded-[2rem] p-8 text-white shadow-xl shadow-emerald-200">
                            <h4 class="text-[10px] font-black uppercase tracking-widest mb-4 opacity-80">Final Approval Action</h4>
                            <p class="text-xs font-bold mb-6">Verify that all submissions meet site requirements before final closure.</p>
                            <form action="{{ route('admin.tenders.approve', $tender->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" onclick="return confirm('Complete project?')" class="w-full bg-white text-emerald-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-50 transition">Approve & Complete</button>
                            </form>
                        </div>
                    @endif

                    {{-- RATING SECTION --}}
                    @if(($tender->work_status === 'completed' || $tender->work_status === 'assigned') && $tender->selected_subcon_id)
                        <div class="bg-amber-50 border border-amber-200 rounded-[2rem] p-8 shadow-sm">
                            <h4 class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-4">Rate Subcontractor</h4>
                            @php
                                $existingReview = $tender->review;
                            @endphp
                            @if($existingReview)
                                <div class="mb-4 p-4 bg-white rounded-2xl border border-amber-100">
                                    <div class="flex items-center mb-2">
                                        <span class="text-sm font-black text-amber-600">Rating:</span>
                                        <div class="flex gap-1 ml-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="text-lg {{ $i <= $existingReview->rating ? 'text-amber-500' : 'text-slate-300' }}">★</span>
                                            @endfor
                                        </div>
                                        <span class="text-xs font-bold text-amber-600 ml-2">({{ $existingReview->rating }}/5)</span>
                                    </div>
                                    @if($existingReview->review)
                                        <p class="text-xs text-slate-600 italic">{{ $existingReview->review }}</p>
                                    @endif
                                    <p class="text-[10px] text-slate-400 mt-2">Rated {{ $existingReview->updated_at->diffForHumans() }}</p>
                                </div>
                            @endif
                            <form action="{{ route('admin.tenders.rate', $tender->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="text-[10px] font-black text-amber-700 uppercase tracking-widest block mb-2">Select Rating</label>
                                    <div class="flex gap-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer flex-1">
                                                <input type="radio" name="rating" value="{{ $i }}" {{ $existingReview && $existingReview->rating == $i ? 'checked' : '' }} class="sr-only peer" />
                                                <div class="text-3xl text-center peer-checked:text-amber-500 text-slate-300 hover:text-amber-400 transition">★</div>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-amber-700 uppercase tracking-widest block mb-2">Comments (Optional)</label>
                                    <textarea name="review" rows="3" placeholder="Add your feedback..." class="w-full text-sm border-amber-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 placeholder:text-slate-300">{{ $existingReview?->review }}</textarea>
                                </div>
                                <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-amber-700 transition">{{ $existingReview ? 'Update Rating' : 'Submit Rating' }}</button>
                            </form>
                        </div>
                    @endif

                    <div class="bg-white rounded-[2rem] p-8 border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Phase Monitoring</p>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Project Phase</span>
                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ str_replace('_', ' ', $tender->work_status) }}</span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Quality Gate</span>
                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Verified</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reassign Modal --}}
            <div x-show="reassignModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div @click.away="reassignModal = false" class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl">
                    <h3 class="text-xl font-black text-slate-900 mb-2">Reassign Project</h3>
                    <p class="text-xs text-slate-500 mb-6 italic leading-relaxed">This will reallocate the contract to a different subcontractor. All previous review history for this contract will remain linked.</p>
                    <form action="{{ route('admin.tenders.reassign', $tender->id) }}" method="POST" class="space-y-4">
                        @csrf @method('PATCH')
                        <select name="selected_subcon_id" required class="w-full border-slate-200 rounded-xl focus:ring-indigo-500 text-sm font-bold py-3 uppercase">
                            @foreach($subcons as $subcon)
                                <option value="{{ $subcon->id }}" {{ $tender->selected_subcon_id == $subcon->id ? 'selected' : '' }}>{{ $subcon->company_name }} (G{{ is_array($subcon->cidb_grades) ? implode(', ', $subcon->cidb_grades) : $subcon->cidb_grades }})</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="reassignModal = false" class="flex-1 py-3 text-xs font-black uppercase text-slate-400">Cancel</button>
                            <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl text-xs font-black uppercase hover:bg-indigo-700 transition">Confirm Change</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
