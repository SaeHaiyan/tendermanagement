<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenders_Management_Report_{{ now()->format('Ymd') }}</title>
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
            height: 96vh;
            max-height: 275mm;
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
    <div class="p-4 page-break">
        <div class="cover-page-container">
            <div class="outer-border">
                <div class="inner-border text-center pt-14">

                    <!-- Top Segment: Corporate Branding Details -->
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

                    <!-- Middle Segment: Report Title & Filter Summary -->
                    <div class="mt-20 mb-auto py-4 space-y-6">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Document Type:</span>
                            <h2 class="text-xl font-black text-black uppercase tracking-tight max-w-2xl mx-auto leading-snug">
                                COMPREHENSIVE TENDERS LIST REPORT
                            </h2>
                        </div>

                        <div class="w-12 h-0.5 bg-slate-900 mx-auto opacity-20"></div>

                        <div class="space-y-2 max-w-md mx-auto bg-slate-50 border border-slate-200 rounded-lg p-3 text-left">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block text-center border-b border-slate-200 pb-1 mb-1">Report Context & Filters</span>
                            <div class="grid grid-cols-2 gap-y-1 gap-x-4 text-xs">
                                <p class="text-gray-500 font-medium">Total Tenders:</p>
                                <p class="font-bold text-gray-900 text-right">{{ count($tenders) }} Records</p>

                                <p class="text-gray-500 font-medium">Export Date:</p>
                                <p class="font-bold text-gray-900 text-right">{{ now()->format('d/m/Y') }}</p>

                                <p class="text-gray-500 font-medium">Status Scope:</p>
                                <p class="font-bold text-indigo-600 text-right uppercase">All Active/Filtered</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Segment: Signature Footer Box -->
                    <div class="grid grid-cols-2 gap-12 text-left pt-4 mt-auto px-4 mb-2">
                        <!-- Left Node: Generated By System -->
                        <div class="flex flex-col justify-end relative min-h-[110px]">
                            <span class="text-xs font-bold text-gray-800 block mb-1">Generated By:</span>
                            <div class="border-b border-slate-400 w-44 mt-6"></div>
                            <div class="text-[11px] font-bold text-gray-900 mt-1 space-y-0.5">
                                <p>System Authority: <span class="font-black">AITO HQ Portal</span></p>
                                <p>Time: <span class="font-medium text-gray-600">{{ now()->format('h:i A') }}</span></p>
                            </div>
                        </div>

                        <!-- Right Node: Acknowledged Line -->
                        <div class="flex flex-col justify-end min-h-[110px]">
                            <span class="text-xs font-bold text-gray-800 block mb-1">Reviewed & Verified by:</span>
                            <div class="border-b border-dotted border-slate-400 w-44 mt-10"></div>
                            <div class="text-[11px] font-bold text-gray-900 mt-2">
                                <p>Name & Designation:</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- ========================================================= -->
    <!-- PAGE 2: MAIN TENDER LIST TABLE SECTION                     -->
    <!-- ========================================================= -->
    <div class="p-8">

        <!-- Official Horizontal Corporate Header Structure (Sama dengan Page 2 Laporan Sebelum Ini) -->
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

        <!-- Sheet Intent Target Header Flags -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-base font-black text-slate-900 uppercase tracking-wider">Tenders Listing Worksheet</h1>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tight mt-0.5">AITOTENDER MANAGEMENT ARCHIVE</p>
            </div>
            <div class="text-right text-xs text-slate-500">
                <p><strong>Generated:</strong> {{ now()->format('d M Y, h:i A') }}</p>
                <p><strong>Total Row Count:</strong> {{ count($tenders) }} Items</p>
            </div>
        </div>

        <!-- Dynamic Filter Status Indicators (Bagus untuk admin semak bila dah filter) -->
        <div class="flex gap-2 mb-4 text-[10px] font-bold uppercase">
            <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded border border-slate-200">Active Database</span>
            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded border border-indigo-100">Tailwind Optimized</span>
        </div>

        <!-- Main Tenders List Table Structure -->
        <table class="w-full text-left text-[11px] border-collapse border border-slate-200">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-300 text-slate-600 font-bold uppercase tracking-tight">
                    <th class="p-2 border border-slate-200 w-[20%]">Title</th>
                    <th class="p-2 border border-slate-200 w-[12%]">Reference</th>
                    <th class="p-2 border border-slate-200 w-[8%] text-center">Grade</th>
                    <th class="p-2 border border-slate-200 w-[18%]">Services Required</th>
                    <th class="p-2 border border-slate-200 w-[10%] text-center">Deadline</th>
                    <th class="p-2 border border-slate-200 w-[10%] text-center">Status</th>
                    <th class="p-2 border border-slate-200 w-[15%]">Assignee Partner</th>
                    <th class="p-2 border border-slate-200 w-[7%] text-center">Progress</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($tenders as $tender)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-2 border border-slate-200 font-bold text-slate-900 uppercase leading-tight">
                            {{ $tender->title }}
                        </td>
                        <td class="p-2 border border-slate-200 font-mono text-slate-600 tracking-tighter">
                            {{ $tender->tender_ref_number }}
                        </td>
                        <td class="p-2 border border-slate-200 text-center font-black text-gray-800">
                            {{ $tender->required_grade }}
                        </td>
                        <td class="p-2 border border-slate-200 text-slate-600 leading-normal">
                            {{ $tender->required_services }}
                        </td>
                        <td class="p-2 border border-slate-200 text-center text-slate-600 whitespace-nowrap">
                            {{ optional($tender->deadline)->format('d/m/Y') }}
                        </td>
                        <td class="p-2 border border-slate-200 text-center font-bold">
                            @if($tender->selected_subcon_id)
                                <span class="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 text-[10px]">Assigned</span>
                            @else
                                <span class="text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 text-[10px]">Reviewing</span>
                            @endif
                        </td>
                        <td class="p-2 border border-slate-200 text-slate-700 font-semibold truncate max-w-[140px]">
                            {{ optional($tender->selectedSubcon)->company_name ?? '---' }}
                        </td>
                        <td class="p-2 border border-slate-200 text-center font-black text-slate-900">
                            {{ $tender->progress_percent }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400 italic font-medium">
                            No matching tender records found for current filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</body>
</html>
