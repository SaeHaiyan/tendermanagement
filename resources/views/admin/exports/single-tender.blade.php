<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tender_Complete_Report_{{ $tender->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Document Global Print Optimizations */
        @media print {
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-break {
                page-break-after: always;
                break-after: page;
                clear: both;
            }
        }

        /* Cover Page Layout Fix (Sizing Safe Guard untuk A4) */
        .cover-page-container {
            height: 96vh; /* Guna peratusan viewport tinggi cetakan supaya tak melimpah */
            max-height: 275mm; /* Had selamat kertas A4 tolak margin printer */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .outer-border {
            border: 1px solid #1e293b;
            padding: 4px;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .inner-border {
            border: 1px solid #64748b;
            padding: 30px 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans" onload="window.print()">

{{-- First front page --}}
    <div class="p-4 page-break">
        <div class="cover-page-container">
            <div class="outer-border">
                <div class="inner-border text-center pt-14">

                    <div class="space-y-4">
                        <div class="flex justify-center">
                            <img src="https://crm.aito.com.my/uploads/tenants/aito/company/a1249b28605eb8ee926e7db509447715.png"
                                 alt="AITO Logo"
                                 class="h-16 w-auto object-contain" />
                        </div>

                        <div class="space-y-0.5 text-gray-900">
                            <h1 class="text-base font-extrabold tracking-tight uppercase">
                                AITO FIREWORK HOLDING SDN BHD
                            </h1>
                            <p class="text-xs font-bold tracking-normal text-gray-800">
                                (1122958-V)
                            </p>
                            <p class="text-xs font-black italic tracking-wide text-gray-900 pt-1">
                                ‘SAFETY FOREVER FIRE NEVER’
                            </p>
                            <p class="text-[11px] font-semibold text-gray-700 max-w-md mx-auto pt-1 leading-relaxed">
                                22G Jalan Kempas Utama 1/1, Taman Kempas Utama,<br>
                                81300 Johor Bahru, Johor
                            </p>
                        </div>

                        <div class="text-[11px] font-semibold text-gray-800 space-y-0.5">
                            <p>Email: <a href="mailto:sales.johor@aito.com.my" class="text-blue-600 underline">sales.johor@aito.com.my</a></p>
                            <p>Contact: 07-562 5534</p>
                        </div>
                    </div>

                    <div class="mt-20 mb-auto py-4 space-y-8">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Title:</span>
                            <h2 class="text-lg font-black text-black uppercase tracking-tight max-w-2xl mx-auto leading-snug">
                                {{ $tender->title ?? 'SERVICING FIREFIGHTING SYSTEM & FIRE EQUIPMENT' }}
                            </h2>
                        </div>

                        @if(isset($tender->selectedSubcon) && $tender->selectedSubcon->logo_path)
                            <div class="flex justify-center my-4">
                                <img src="{{ asset('storage/' . $tender->selectedSubcon->logo_path) }}"
                                     alt="Subcontractor Logo"
                                     class="max-h-12 w-auto object-contain" />
                            </div>
                        @else
                            <div class="w-12 h-0.5 bg-slate-900 mx-auto opacity-20 my-2"></div>
                        @endif

                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Client:</span>
                            <h3 class="text-base font-black text-black uppercase tracking-tight max-w-xl mx-auto leading-tight">
                                {{ $tender->selectedSubcon->company_name ?? 'ARKEMA COATING RESINS (M) SDN BHD' }}
                            </h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-12 text-left pt-4 mt-auto px-4 mb-2">

                        <div class="flex flex-col justify-end relative min-h-[110px]">
                            <span class="text-xs font-bold text-gray-800 block mb-1">Prepared by:</span>

                            <div class="absolute bottom-[40px] left-4 pointer-events-none opacity-80">
                                <div class="w-20 h-20 border border-dashed border-blue-400 rounded-full flex items-center justify-center text-[7px] text-blue-400 font-bold uppercase tracking-tighter rotate-12">
                                    AITO STAMP
                                </div>
                            </div>

                            <div class="border-b border-slate-400 w-44 mt-6"></div>
                            <div class="text-[11px] font-bold text-gray-900 mt-1 space-y-0.5">
                                <p>Name: <span class="font-black">{{ $tender->selectedSubcon->pic_name ?? 'N/A' }}</span></p>
                                <p>Date: <span class="font-medium text-gray-600">{{ now()->format('d/m/Y') }}</span></p>
                            </div>
                        </div>

                        <div class="flex flex-col justify-end min-h-[110px]">
                            <span class="text-xs font-bold text-gray-800 block mb-1">Received by:</span>
                            <div class="border-b border-dotted border-slate-400 w-44 mt-10"></div>
                            <div class="text-[11px] font-bold text-gray-900 mt-2">
                                <p>Name:</p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

{{-- second page --}}
    <div class="p-8">
        <div class="border-b-2 border-red-600 pb-4 mb-6">
            <table class="w-full table-fixed border-collapse">
                <tr>
                    <td class="w-[22%] align-middle">
                        <div class="flex items-center">
                            <img src="https://crm.aito.com.my/uploads/tenants/aito/company/a1249b28605eb8ee926e7db509447715.png"
                                 alt="AITO Logo"
                                 class="h-12 w-auto object-contain print:max-h-12" />
                        </div>
                    </td>

                    <td class="w-[58%] text-left px-4 align-middle" style="font-family: sans-serif;">
                        <h2 class="text-[12px] font-extrabold text-black tracking-tight uppercase leading-snug">
                            AITO FIREWORK HOLDING SDN BHD <span class="text-[10px] font-normal text-gray-700">(1122958-V)</span>
                        </h2>
                        <p class="text-[9px] text-black font-semibold mt-0.5 leading-tight">
                            <span class="font-bold">BRANCH:</span> No. 22G & 22-01, Jalan Kempas Utama 1/1, Taman Kempas Utama, 81300 Johor Bahru, Johor Darul Takzim, Malaysia.
                        </p>
                        <p class="text-[9px] text-black font-semibold mt-0.5 leading-none">
                            <span class="font-bold">Tel:</span> 07-562 5534 / 010-213 2534
                        </p>
                        <p class="text-[9px] text-black font-semibold mt-0.5 leading-none">
                            <span class="font-bold">Email:</span>
                            <a href="mailto:sales.johor@aito.com.my" class="text-blue-700 underline">sales.johor@aito.com.my</a> /
                            <a href="mailto:admin.johor@aito.com.my" class="text-blue-700 underline">admin.johor@aito.com.my</a>
                        </p>
                    </td>

                    <td class="w-[20%] text-right align-middle">
                        <div class="inline-flex items-center gap-2">
                            <div class="border border-amber-500 rounded p-1 text-center bg-amber-50/20" style="width: 48px;">
                                <span class="text-[8px] font-black text-blue-800 tracking-tighter block leading-none">cARe</span>
                                <span class="text-[5px] text-amber-600 uppercase tracking-widest block font-bold scale-90 mt-0.5">ISO 9001</span>
                            </div>
                            <div class="border border-red-500 rounded-full px-1 py-1 text-center bg-red-50/20" style="width: 48px; height: 48px;">
                                <span class="text-[5px] font-black text-red-600 uppercase tracking-tighter block mt-1.5">Standards</span>
                                <span class="text-[4px] text-gray-500 tracking-tighter block font-medium scale-75 leading-none">MALAYSIA</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-base font-black text-slate-900 uppercase tracking-wider">Project Monitoring Audit Sheet</h1>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tight mt-0.5">AITOTENDER MANAGEMENT ARCHIVE</p>
            </div>
            <div class="text-right text-xs text-slate-500">
                <p><strong>Generated:</strong> {{ now()->format('d M Y, h:i A') }}</p>
                <p><strong>Project ID:</strong> #{{ $tender->id }}</p>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-8 border border-slate-200 rounded-xl p-4 bg-slate-50/80">
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
                <span class="text-sm font-black text-slate-900 truncate block mt-0.5">
                    {{ $tender->selectedSubcon->company_name ?? 'N/A' }}
                </span>
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
                            <td class="py-3 font-bold text-slate-900">{{ $label }}</td>
                            <td class="py-3 text-slate-500">Submission #{{ $index + 1 }}</td>
                            <td class="py-3 uppercase font-black {{ (is_array($file) ? $file['status'] : 'pending') === 'rejected' ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ is_array($file) ? $file['status'] : 'pending' }}
                            </td>
                            <td class="py-3 text-slate-600 italic">"{{ is_array($file) ? ($file['feedback'] ?? '---') : '---' }}"</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

    </div>

</body>
</html>
