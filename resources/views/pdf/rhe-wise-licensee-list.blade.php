<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $estateName }} Licensee List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
        }
        .header h1 {
            font-size: 18px;
            margin: 5px 0;
        }
        .header h2 {
            font-size: 16px;
            margin: 5px 0;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #999999;
            font-weight: bold;
            text-align: center;
        }
        td {
            text-align: left;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <strong>Housing Department</strong><br>
            Government of West Bengal<br>
            1, K. S Roy Road, Kolkata - 700001<br>
            <strong style="font-size: 16px;">e-Allotment of Rental Housing Estate</strong><br>
            <strong style="font-size: 14px;">{{ $estateName }} Licensee List</strong>
        </div>
    </div>
    
    <div class="date">
        <strong>Date : </strong>{{ date('l d-m-Y h:i:s A') }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%">Sl. No.</th>
                <th width="25%" class="center">Licensee Name</th>
                <th width="20%" class="center">Licence No.</th>
                <th width="25%" class="center">Date of Issue</th>
                <th width="25%" class="right">Date of Expiry</th>
            </tr>
        </thead>
        <tbody>
            @foreach($licenses as $index => $license)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="center">{{ $license['applicant_name'] ?? 'N/A' }}</td>
                    <td class="center">{{ $license['license_no'] ?? 'N/A' }}</td>
                    <td class="center">{{ $license['license_issue_date'] ? date('d/m/Y', strtotime($license['license_issue_date'])) : 'N/A' }}</td>
                    <td class="right">{{ $license['license_expiry_date'] ? date('d/m/Y', strtotime($license['license_expiry_date'])) : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
