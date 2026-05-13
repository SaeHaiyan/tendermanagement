<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subcontractors Report</title>
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
    <div class="title">Subcontractors Report</div>
    <div class="subtitle">Generated on {{ now()->format('Y-m-d H:i') }}</div>
    <table>
        <thead>
            <tr>
                <th>Company Name</th>
                <th>PIC</th>
                <th>Email</th>
                <th>CIDB Grade</th>
                <th>Status</th>
                <th>Services</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->company_name }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ is_array($user->cidb_grades) ? implode(', ', $user->cidb_grades) : $user->cidb_grades }}</td>
                    <td>{{ $user->status }}</td>
                    <td>{{ is_array($user->services_provided) ? implode(', ', $user->services_provided) : $user->services_provided }}</td>
                    <td>{{ optional($user->created_at)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
