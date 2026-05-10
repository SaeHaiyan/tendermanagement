<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 uppercase tracking-widest">
            AI Matchmaking: {{ $tenders->title }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- --- SUCCESS ALERT --- --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 font-bold rounded-r-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header Card --}}
            <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex-1">
                        <nav class="flex mb-4" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                                <li>Admin</li>
                                <li><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 11H3a1 1 0 110-2h7.586l-3.293-3.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"/></svg></li>
                                <li class="text-indigo-600">AI Matchmaker</li>
                            </ol>
                        </nav>
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                            {{ $tenders->title }}
                        </h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center ...">
                            Grade {{ is_array($tenders->required_grade) ? implode(', ', $tenders->required_grade) : $tenders->required_grade }}
                        </span>
                        <span class="inline-flex items-center px-4 py-2 rounded-xl {{ $tenders->selected_subcon_id ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} border font-bold text-sm">
                            {{ $tenders->selected_subcon_id ? 'Assigned' : 'Awaiting Selection' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- --- LEFT COLUMN: ACTION PANEL --- --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Assignment Card --}}
                    <div class="bg-indigo-600 p-6 rounded-2xl shadow-lg text-white">
                        <h4 class="font-bold text-lg mb-2">Assign Project</h4>
                        @if($tenders->selected_subcon_id)
                            <div class="mt-4 p-4 bg-white/10 rounded-xl border border-white/20">
                                <p class="text-xs font-bold uppercase opacity-70">Selected Subcontractor:</p>
                                <p class="text-lg font-black mt-1">{{ $tenders->selectedSubcon->company_name ?? 'Assigned' }}</p>
                                <p class="text-[10px] mt-2 opacity-80 italic">Status: {{ ucfirst($tenders->work_status) }}</p>
                            </div>
                        @else
                            <p class="text-indigo-100 text-sm mb-4 opacity-90">Based on the AI report, select the most suitable candidate to begin work.</p>
                            <form action="{{ route('admin.tenders.assign', $tenders->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <select name="subcon_id" required class="w-full rounded-xl border-none text-slate-900 text-sm font-bold focus:ring-2 focus:ring-indigo-300">
                                    <option value="">Select a winner...</option>
                                    @foreach($matchedSubcons as $subcon)
                                        <option value="{{ $subcon->id }}">
                                            {{ $subcon->company_name }} (Grade {{ is_array($subcon->cidb_grades) ? implode(', ', $subcon->cidb_grades) : $subcon->cidb_grades }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" onclick="return confirm('Officially assign this project?')" class="w-full bg-white text-indigo-600 font-bold py-3 rounded-xl hover:bg-indigo-50 transition shadow-md uppercase text-xs tracking-widest">
                                    Finalize Assignment
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Details Card --}}
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Requirement Details</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Target Services</p>
                                <p class="text-sm font-semibold text-slate-800">{{ $tenders->required_services }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Deadline</p>
                                <p class="text-sm font-semibold text-slate-800">{{ \Carbon\Carbon::parse($tenders->deadline)->format('d M, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- --- RIGHT COLUMN: AI REPORT & TABLE --- --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- AI Report --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-900 px-8 py-4 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></div>
                                <span class="text-slate-300 text-xs font-bold uppercase tracking-widest">Gemini Intelligence Report</span>
                            </div>
                        </div>

                        <div class="p-8">
                            @php
                                $displayOutput = preg_replace('/ID:\s?#\d+\s?\|?\s?/', '', $aiResponse);
                                $displayOutput = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-indigo-700 font-extrabold">$1</strong>', $displayOutput);
                                $hasError = str_contains($displayOutput, '🚨');
                            @endphp

                            <article class="ai-content prose prose-slate max-w-none">
                                <div class="whitespace-pre-line text-slate-700 text-lg leading-relaxed {{ $hasError ? 'p-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg' : '' }}">
                                    {!! nl2br($displayOutput) !!}
                                </div>
                            </article>

                            <div class="mt-10 pt-6 border-t border-slate-100 flex justify-between items-center">
                                <a href="{{ route('admin.tenders.index') }}" class="text-slate-400 hover:text-indigo-600 font-bold text-sm transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"></path></svg>
                                    Return to List
                                </a>
                                <a href="{{ route('admin.tenders.match', [$tenders->id, 'force' => 'true']) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2 rounded-xl font-bold text-sm transition">
                                    Regenerate Analysis
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Matched List --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 font-black text-gray-800 uppercase text-sm tracking-widest">
                            Eligible Subcontractors
                        </div>
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-xs uppercase font-black text-gray-500">
                                <tr>
                                    <th class="px-6 py-4">Company</th>
                                    <th class="px-6 py-4">CIDB Grade</th>
                                    <th class="px-6 py-4">Services</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($matchedSubcons as $subcon)
                                    <tr>
                                        <td class="px-6 py-4 font-bold text-slate-800">{{ $subcon->company_name }}</td>
                                        <td class="px-6 py-4">
                                            {{ implode(', ', $subcon->cidb_grades) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ \Illuminate\Support\Str::limit($subcon->services_provided, 50) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-slate-400">
                                                <svg class="w-12 h-12 mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="font-black uppercase text-sm">No compatible candidates found</p>
                                                <p class="text-xs">Adjust the tenders criteria or add more subcontractors to the directory.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .ai-content strong { display: inline-block; margin-top: 1.5rem; border-bottom: 2px solid #e2e8f0; width: 100%; padding-bottom: 0.25rem; }
        @media print {
            .bg-slate-900, .bg-indigo-600, button, a, nav, .lg:col-span-1 { display: none !important; }
            .lg:col-span-2 { width: 100% !important; border: none !important; }
            .shadow-sm { box-shadow: none !important; }
        }
    </style>
</x-app-layout>
