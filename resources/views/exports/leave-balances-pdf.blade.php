<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Balances Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .status-available {
            color: #059669;
        }
        .status-exhausted {
            color: #dc2626;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Leave Balances Report</h1>
        <p>Generated on: {{ now()->format('F j, Y H:i:s') }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h3>Applied Filters:</h3>
        <ul>
            @if(!empty($filters['search']))
                <li>Search: {{ $filters['search'] }}</li>
            @endif
            @if(!empty($filters['department']))
                <li>Department: {{ \App\Models\Department::find($filters['department'])->name }}</li>
            @endif
            @if(!empty($filters['leaveType']))
                <li>Leave Type: {{ \App\Models\LeaveType::find($filters['leaveType'])->name }}</li>
            @endif
            @if(!empty($filters['year']))
                <li>Year: {{ $filters['year'] }}</li>
            @endif
        </ul>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>Year</th>
                <th>Entitled Days</th>
                <th>Days Taken</th>
                <th>Remaining Days</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($balances as $balance)
            <tr>
                <td>{{ $balance->user->staff_id }}</td>
                <td>{{ $balance->user->firstname }} {{ $balance->user->lastname }}</td>
                <td>{{ $balance->user->department->name ?? 'N/A' }}</td>
                <td>{{ $balance->leaveType->name }}</td>
                <td>{{ $balance->year }}</td>
                <td>{{ $balance->total_entitled_days }}</td>
                <td>{{ $balance->days_taken }}</td>
                <td>{{ $balance->total_entitled_days - $balance->days_taken }}</td>
                <td class="{{ ($balance->total_entitled_days - $balance->days_taken) > 0 ? 'status-available' : 'status-exhausted' }}">
                    {{ ($balance->total_entitled_days - $balance->days_taken) > 0 ? 'Available' : 'Exhausted' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 