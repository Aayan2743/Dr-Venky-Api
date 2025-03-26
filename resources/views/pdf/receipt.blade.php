<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Receipt</title>
    <style>
        @media print {
            .hidden-print {
                display: none !important;
            }
        }
        body {
            margin: 0;
            font-family: 'Courier New', Courier, monospace; /* Thermal printer-friendly font */
        }
        .receipt {
            width: 80mm; /* Standard thermal printer width */
            margin: 0 auto; /* Center the content */
        }
        .header, .footer {
            text-align: center;
        }
        .header h3, .footer h5 {
            margin: 0;
            font-size: 14px;
        }
        .text-xs {
            font-size: 12px;
        }
        .border-b {
            border-bottom: 1px dashed #333;
            margin: 4px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table td {
            padding: 2px 0;
            word-wrap: break-word; /* Prevent long text overflow */
        }
        .font-bold {
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header Section -->
        <div class="header">
            <h3>Dr. Venky's Animal Hospital</h3>
            <br>
               @if($status_payment==0 || $status_payment==1 )
                    <h3>Thank you for Booking Appointment</h3>
              @endif
              </br>
        </div>
        <!--//now()->format('d-m-Y h:i A')-->

        <!-- Patient & Invoice Details -->
        <table class="table">
            <tr>
                <td class="text-xs text-left">OP#:</td>
                <td class="text-xs text-right">{{$InvoiceNo}}</td>
            </tr>
            <tr>
                <td class="text-xs text-left">Date & Time:</td>
                <td class="text-xs text-right">{{ $date }}</td>
            </tr>
            <tr>
                <td class="text-xs text-left">Patient:</td>
                <td class="text-xs text-right">{{$patient_name}} - {{$pet_category}}</td>
            </tr>
            <tr>
                <td class="text-xs text-left">Phone:</td>
                <td class="text-xs text-right">{{$phone}}</td>
            </tr>
            <tr>
                <td class="text-xs text-left">Location:</td>
                <td class="text-xs text-right">{{$city}}</td>
            </tr>
            
            @if($status_payment==0 || $status_payment==1 )
            <!-- <tr>-->
            <!--    <td class="text-xs text-left">Doctor Name:</td>-->
            <!--    <td class="text-xs text-right">{{$dr_name}}</td>-->
            <!--</tr>-->
            @endif
           
        </table>

        <!-- Separator -->
        <div class="border-b"></div>

        <!-- Services Table -->
        <table class="table">
            <thead>
                <tr>
                    <th class="text-xs text-left">Service</th>
                    <th class="text-xs text-right">Fee</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount = 0;
                    $inHouseAmount = 0;
                @endphp
                
                
                @foreach ($payment_for as $detail)
                
              
                   @if($detail->payment_for == "In_House" || $detail->payment_for == "E-Consultancy")
                         @php $inHouseAmount += $detail->amount; @endphp
                        @continue
                    @endif
                
                  @php
                        $totalAmount += $detail->amount;
                        //$totalAmount ;
                    @endphp

                <tr>
                    <td class="text-xs text-left">{{ $detail->payment_for }}</td>
                    <td class="text-xs text-right">{{ $detail->amount }}</td>
                </tr>
                @endforeach
                
                @php
                    $finalTotal = $totalAmount ;
                @endphp
                
                
            </tbody>
        </table>

        <!-- Separator -->
        <div class="border-b"></div>

        <!-- Total and Payment -->
        <table class="table">
            <tr>
                <td class="text-xs text-left font-bold">Total:</td>
                <td class="text-xs text-right font-bold">{{ $finalTotal }}  </td>
            </tr>
        </table>
          @if($status_payment==2)
                <p class="text-xs text-center">
                    Payment Type: {{$paymentData}} - {{$payment}}
                </p>
            @else         
                 <p class="text-xs text-center">
                    Payment Type: Rozarpay - {{$payment}}
                </p>
          @endif
        <!-- Footer Section -->
        <div class="footer">
            <p class="text-xs">Dr. Venky's Animal Hospital</p>
        </div>
    </div>
</body>
</html>
