<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin-bottom: 10px;
        }
        .section p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .side-by-side {
            width: 100%;
            border-spacing: 30px;
        }
        .side-by-side td {
            vertical-align: top;
        }
        .table-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Prescription Details</h2>

        <!-- Pet and Owner Details Section (side by side) using a table -->
        <div class="section">
            <h3>Pet and Owner Details</h3>
            <table class="side-by-side">
                <tr>
                    <td>
                        <h4>Pet Details</h4>
                        <p><strong>Pet Name:</strong> {{$pet_name}}</p>
                        <p><strong>Age:</strong> {{$pet_petAge}} - {{$petDobOptions}} Old</p>
                        <p><strong>Breed:</strong> {{$pet_petbread}}</p>
                        <p><strong>OP ID:</strong> {{$id}}</p>
                        <p><strong>Date of Appointment:</strong> {{$appointment_data}}</p>
                    </td>
                    <td>
                        <h4>Owner Details</h4>
                        <p><strong>Owner Name:</strong>{{$name}}</p>
                        <p><strong>Contact Number:</strong> {{$phone}}</p>
                        <p><strong>Address:</strong> {{$city}}</p>
                        <hr/>
                        <p><strong>Doctor Name:</strong> {{$doctor_name}}</p>
                    </td>
                    
                   
                </tr>
            </table>
        </div>
        
        
         <div class="section">
            <h3>Notes</h3>
            <ul>
                <li>{{$preceiption}}</li>
               
            </ul>
        </div>

       
        <div class="section">
            <h3>Medications</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>S.No</th>
                             <th>Medicine Type</th>
                            <th>Medicine Name</th>
                            <th>Dose</th>
                            <th>Frequency</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                         @foreach ($medicanes as $key => $medicane)
                               
                         
                        
                        <tr>
                             <td>{{ $key+1 }}</td>
                             <td>{{ $medicane['Type'] }}</td>
                            <td>{{ $medicane['Name'] }}</td>
                            <td>{{ $medicane['dose'] }}</td>
                            <td>{{ $medicane['frequency'] }}</td>
                            <td>{{ $medicane['course'] }} -{{ $medicane['options'] }} </td>
                        </tr>
                           @endforeach
                       
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="section">
            <h3>Diagnostic</h3>
            <ul>
                @foreach ($reports['subserviceData'] as $key => $service)
                    <li>{{ $service['subservicename'] }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Symptoms Section -->
       
       
    </div>

</body>
</html>
