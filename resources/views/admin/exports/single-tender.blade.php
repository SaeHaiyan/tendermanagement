<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tender_Report_{{ $tender->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white p-10 text-slate-800" onload="window.print()">

    <div class="border-b-2 border-slate-900 pb-6 mb-8 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-slate-950 rounded-2xl flex flex-col items-center justify-center shadow-md">
                <span class="text-white font-black text-2xl tracking-tighter leading-none">AI</span>
                <span class="text-red-500 font-black text-[9px] uppercase tracking-widest mt-1">TO</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-950 tracking-tight uppercase leading-none">AITOTENDER</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">Project Monitoring Audit Sheet</p>
            </div>
        </div>
        <div class="text-right text-xs text-slate-500">
            <p><strong>Generated:</strong> {{ now()->format('d M Y, h:i A') }}</p>
            <p><strong>Project ID:</strong> #{{ $tender->id }}</p>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-8 border border-slate-200 rounded-xl p-4 bg-slate-50">
        <div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Progress</span>
            <span class="text-xl font-black text-slate-900">{{ $tender->progress_percent }}%</span>
        </div>
        <div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Required Qualification</span>
            <span class="text-xl font-black text-slate-900 uppercase">{{ $tender->required_grade }}</span>
        </div>
        <div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Phase Status</span>
            <span class="text-xl font-black text-indigo-600 uppercase">{{ str_replace('_', ' ', $tender->work_status) }}</span>
        </div>
        <div>
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Allocated Partner</span>
            <span class="text-sm font-black text-slate-900 truncate block">{{ $tender->selectedSubcon->company_name ?? 'N/A' }}</span>
        </div>
    </div>

    <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 mb-4">Submitted Documentation Checklist</h3>
    <table class="w-full text-left text-xs border-collapse">
        <thead>
            <tr class="border-b border-slate-300 text-slate-400 font-bold uppercase">
                <th class="py-2">Category</th>
                <th class="py-2">Item Index</th>
                <th class="py-2">Status</th>
                <th class="py-2">Review Feedback Remarks</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach(['site_photos' => 'Site Progress Photos', 'financial_docs' => 'Financial Claims', 'invoices' => 'Tax Invoices'] as $key => $label)
                @foreach($categories[$key] ?? [] as $index => $file)
                    <tr>
                        <td class="py-3 font-bold">{{ $label }}</td>
                        <td class="py-3 text-slate-500">Submission #{{ $index + 1 }}</td>
                        <td class="py-3 uppercase font-black {{ (is_array($file) ? $file['status'] : 'pending') === 'rejected' ? 'text-red-500' : 'text-emerald-600' }}">
                            {{ is_array($file) ? $file['status'] : 'pending' }}
                        </td>
                        <td class="py-3 text-slate-600 italic">"{{ is_array($file) ? ($file['feedback'] ?? '---') : '---' }}"</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

</body>
</html>
