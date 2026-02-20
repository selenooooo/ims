<!DOCTYPE html>
<html>
<head>
    <title>Leave Approval</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
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
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 4px 0;
        }

        .content {
            margin-top: 20px;
        }

        .content p {
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 30px;
        }

        .signature {
            margin-top: 50px;
            text-align: left;
        }

        .signature p {
            margin: 30px 0 0 0;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>LEAVE APPROVAL</h2>
    <h3>B2BE GSS SDN BHD</h3>
</div>

<div class="info">
    <p><strong>Intern Name:</strong> {{ strtoupper($leave->user->name) }}</p>
    <p><strong>Supervisor Name:</strong> SHANNON HO CHARLES</p>
    <p><strong>Applied Date:</strong> {{ \Carbon\Carbon::parse($leave->created_at)->format('d M Y') }}</p>
</div>

<div class="content">
    <p>
        This is to certify that the leave applied by <strong>{{ strtoupper($leave->user->name) }}</strong> 
        for <strong>{{ \Carbon\Carbon::parse($leave->leave_date)->format('d M Y') }}</strong> 
        has been reviewed and <strong>{{ strtoupper($leave->status) }}</strong> by the supervisor. 
        <!-- The leave type is <strong>{{ $leave->leaveType->name }} ({{ $leave->leaveType->code }})</strong> 
        and the employee will be on <strong>{{ strtoupper($leave->half_day) }}</strong> leave. -->
    </p>
</div>

<table>
    <thead>
        <tr>
            <th>Intern Name</th>
            <th>Supervisor Name</th>
            <th>Leave Date</th>
            <th>Leave Type</th>
            <th>Half Day</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ strtoupper($leave->user->name) }}</td>
            <td>SHANNON HO CHARLES</td>
            <td>{{ \Carbon\Carbon::parse($leave->leave_date)->format('d M Y') }}</td>
            <td>{{ $leave->leaveType->name }} ({{ $leave->leaveType->code }})</td>
            <td>{{ strtoupper($leave->half_day) }}</td>
        </tr>
    </tbody>
</table>

<div class="content" style="margin-top: 15px;">
    <p>
        <strong>Reason for Leave:</strong> 
        {{ $leave->reason ?? 'No reason provided' }}
    </p>
</div>

<div class="signature">
    <p>Approved by:</p>
    <p>SHANNON HO CHARLES</p>
    <p>Supervisor</p>
</div>

<div class="footer">
    <p>Generated on: {{ now()->format('d M Y') }}</p>
</div>

</body>
</html>
