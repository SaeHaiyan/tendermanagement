<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenders Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; }
        th { background: #f7f7f7; text-align: left; font-weight: 700; }
        td { vertical-align: top; }
        .title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .subtitle { margin-bottom: 16px; color: #555; }
    </style>
</head>
<body>
    <div class="title">Tenders Report</div>
    <div class="subtitle">Generated on {{ now()->format('Y-m-d H:i') }}</div>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Reference</th>
                <th>Grade</th>
                <th>Services</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Assignee</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tenders as $tender)
                <tr>
                    <td>{{ $tender->title }}</td>
                    <td>{{ $tender->tender_ref_number }}</td>
                    <td>{{ $tender->required_grade }}</td>
                    <td>{{ $tender->required_services }}</td>
                    <td>{{ optional($tender->deadline)->format('Y-m-d') }}</td>
                    <td>{{ $tender->work_status }}</td>
                    <td>{{ optional($tender->selectedSubcon)->company_name }}</td>
                    <td>{{ $tender->progress_percent }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
