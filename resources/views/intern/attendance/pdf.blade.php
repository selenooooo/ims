<!DOCTYPE html>
<html>
<head>
    <title>Attendance Record</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header h3 {
            margin: 2px 0 0 0;
            font-size: 14px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 4px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 40%;
            text-align: center;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>ATTENDANCE RECORD</h2>
    <h3>B2BE GSS SDN BHD</h3>
</div>

<div class="info">
    <p><strong>Intern Name:</strong> {{ strtoupper($user->name) }}</p>
    <p><strong>Supervisor Name:</strong> SHANNON HO CHARLES</p>

    @if($report_date && $end_date)
        <p><strong>Training Period:</strong>
            {{ \Carbon\Carbon::parse($report_date)->format('d M Y') }}
            -
            {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
        </p>

        <p><strong>Training Duration:</strong> {{ $intern_duration }} Months</p>
    @endif
</div>

<table>
    <thead>
        <tr>
            <th>DATE</th>
            <th>TIME-IN</th>
            <th>TIME-OUT</th>
            <th>STATUS</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendances as $attendance)
            <tr>
                <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</td>
                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                <td>{{ strtoupper($attendance->status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No attendance records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="signature-section">
    <div class="signature">
        <p>Intern</p>
        <div class="signature-line"></div>
    </div>
    <div class="signature">
        <p>Supervisor</p>
        <div class="signature-line"></div>
    </div>
</div>

<div class="footer">
    <p>Generated on: {{ now()->format('d M Y') }}</p>
</div>

</body>
</html>
