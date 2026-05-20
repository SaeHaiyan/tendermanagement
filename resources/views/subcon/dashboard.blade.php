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

    <div class="bg-slate-50 min-h-screen" x-data="{ tab: 'active' }">
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
                                    <h5 class="text-rose-700 font-black text-[10px] uppercase tracking-widest mb-1">Rejected — Need to Resubmit</h5>
                                    <p class="text-rose-600 text-xs">Admin has reviewed some submissions and requested changes. Feedback is shown below — please re-upload corrected files.</p>
                                </div>
                            @endif

                            <div class="flex flex-col lg:flex-row gap-8">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-black uppercase rounded-lg">#{{ $project->id }}</span>
                                        <span class="text-slate-400 text-xs font-bold italic">Due: {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <h4 class="text-2xl font-black text-slate-900">{{ $project->title }}</h4>
                                        <a href="{{ route('subcon.tenders.manage', $project->id) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-700 bg-slate-900 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-slate-800">
                                            Manage Project
                                        </a>
                                    </div>

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


                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl border border-slate-200 p-20 text-center text-slate-400 font-bold italic">No active projects assigned.</div>
                    @endforelse
                </div>

                {{-- Activity Feed --}}
                <div class="mt-8">
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-black uppercase tracking-widest text-slate-400">Recent Activity</h4>
                            <a href="#" class="text-xs text-slate-500 hover:text-indigo-600">View all</a>
                        </div>
                        @if(!empty($events) && $events->count())
                            <ul class="divide-y divide-slate-100">
                                @foreach($events as $ev)
                                    <li class="py-3 flex items-start justify-between">
                                        <div>
                                            <a href="{{ route('subcon.tenders.manage', $ev['tender_id']) }}" class="font-bold text-slate-800 hover:underline">{{ $ev['title'] }}</a>
                                            <p class="text-xs text-slate-500 mt-1">{{ str_replace('_', ' ', ucfirst($ev['category'])) }} —
                                                @if($ev['status'] === 'approved')
                                                    <span class="text-emerald-600 font-black uppercase">Approved</span>
                                                @elseif($ev['status'] === 'rejected')
                                                    <span class="text-rose-600 font-black uppercase">Rejected — Need to Resubmit</span>
                                                @else
                                                    <span class="text-amber-600 font-black uppercase">Pending</span>
                                                @endif
                                            </p>
                                            @if($ev['status'] === 'rejected' && $ev['feedback'])
                                                <p class="text-xs text-rose-500 italic mt-2">"{{ $ev['feedback'] }}"</p>
                                            @endif
                                        </div>
                                        <div class="text-right text-xs text-slate-400">
                                            <div>{{ \Carbon\Carbon::parse($ev['time'])->diffForHumans() }}</div>
                                            <a href="{{ route('subcon.tenders.manage', $ev['tender_id']) }}" class="text-xs text-indigo-600 font-black uppercase mt-2 block">Manage</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="py-6 text-center text-slate-400 text-sm">No recent activity from admin reviews.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 4. History Tab (Keep as is) --}}
            <div x-show="tab === 'history'" x-transition style="display: none;">
                {{-- ... rest of your history code ... --}}
            </div>

        </div>
    </div>
</x-app-layout>
