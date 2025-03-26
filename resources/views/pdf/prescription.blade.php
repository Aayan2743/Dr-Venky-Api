<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2cm;
            position: relative;
        }
        header {
            text-align: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .footer {
            position: fixed;
            bottom: 1cm;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #aaa;
        }
        .content {
            margin-top: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 5em;
            color: rgba(0, 0, 0, 0.1); /* Light grey transparent text */
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">CONFIDENTIAL</div>
    <header>
        <h1>Clinic Name</h1>
        <p>Address | Phone Number | Email</p>
    </header>
    <div class="content">
        <div class="section">
            <h3>Patient Details</h3>
            <p><strong>Name:</strong> {{ $prescriptions['user']['name'] }}</p>
            <p><strong>Pet:</strong> {{ $prescriptions['pet']['name'] }}</p>
            <p><strong>Date:</strong> {{ now()->format('d M, Y') }}</p>
        </div>

        <div class="section">
            <h3>Selected Services</h3>
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Fee</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subserviceData as $service)
                        <tr>
                            <td>{{ $service['name'] }}</td>
                            <td>{{ $service['type'] }}</td>
                            <td>{{ $service['fee'] }}</td>
                            <td>{{ $service['status'] ? 'Pending' : 'Paid' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>Reports</h3>
            <ul>
                @foreach ($reports as $report)
                    <li>{{ $report['name'] }} - {{ $report['created_at'] }}</li>
                @endforeach
            </ul>
        </div>

        <div class="section">
            <h3>Billing Summary</h3>
            <p><strong>Need to Pay:</strong> {{ $need_to_pay }}</p>
            <p><strong>Already Paid:</strong> {{ $already_paid }}</p>
            <p><strong>Total Amount:</strong> {{ $total_amount }}</p>
            <p><strong>Pending Status:</strong> {{ $pendingstatus ? 'Yes' : 'No' }}</p>
        </div>
    </div>
    <div class="footer">
        <p>Doctor's Signature: _____________________</p>
    </div>
</body>
</html>