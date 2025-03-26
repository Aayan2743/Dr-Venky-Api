<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"> --}}

    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}
    <link href="{{ public_path('css/bootstrap.min.css') }}" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <style>

    body{
        font-family: 'Inter', sans-serif;
    }
    .logo-wh{
        width: 150px;
        height: 48px;
    }
    .float-right{
        float: right!important;
    }
    .fs-14{
        font-size: 14px !important;

    }
    .fs-12{
        font-size: 12px !important;
    }
    .fs-10{
        font-size: 10px;
    }
    .bg-info-subtlee{
        background-color:#E8EFFB !important;
    }

    .flex-container {
    display: flex; /* Enables flexbox */
    justify-content: space-between; /* Adds space between elements */
    align-items: center; /* Aligns items vertically in the center (optional) */
}

    @font-face {
        font-family: 'Noto Sans';
        src: url('{{ public_path('fonts/NotoSans-Regular.ttf') }}');
    }



  </style>
  <body>
   
    {{-- nw --}}
    <div style="background-color: #343a40; color: #fff;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
            <div style="background-color: #fff; color: #000; border-radius: 5px;">
                <div style="padding: 20px; min-height: 100vh;">
                   
                    <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                        <tr>
                            <!-- Left Section (Logo) -->
                            <td style="width: 70%; padding-right: 20px;">
                                <img src="{{ public_path('images/logo1.png') }}" style="width: 150px; height: auto;" alt="">
                                <!--<img src="https://demo.drvenkysanimalhospital.com/assets/logo1-BHrfIYPt.png" style="width: 150px; height: auto;" alt="">-->
                            </td>
                    
                            <!-- Right Section (Invoice Number and Text) -->
                            <td style="width: 30%; text-align: right;">
                                <p style="margin-bottom: 0; font-weight: bold; font-size: 12px;">#{{$InvoiceNo}}</p>
                                <p style="font-size: 12px; margin: 0;">INVOICE</p>
                                
                            </td>
                        </tr>
                    </table>
                    

                    <hr style="border: 1px solid #ccc;">
    
                    <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                        <tr>
                            <!-- Left Section -->
                            <td style="width: 70%; vertical-align: top; padding-right: 20px;">
                                <h6 style="font-size: 14px; font-weight: bold; margin: 0;">Patient Details</h6>
                                <p style="font-size: 12px; font-weight: 600; margin: 0 0 5px;">{{$patientname}}</p>

                                <p style="font-size: 12px; margin: 0 0 5px;">{{$state}}</p>
                                <p style="font-size: 12px; margin: 0 0 5px;">{{$city}}</p>
                                <p style="font-size: 12px; margin: 0;">India</p>
                            </td>
                    
                            <!-- Right Section -->
                            <td style="width: 30%; vertical-align: top; text-align: right;">
                                <h6 style="font-size: 12px; font-weight: bold; margin: 0;">Date: 
                                    <span style="font-weight: normal;">{{$date}}</span>
                                </h6>
                                <h6 style="font-size: 12px; font-weight: bold; margin: 0;">Payment method: 
                                    <span style="font-weight: normal;">{{$payment}}</span>
                                </h6>
                            </td>
                        </tr>
                    </table>
                    



                    <div style="margin-top: 30px; overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="border-bottom: 1px solid #ccc; text-align: left; padding: 8px;">Service Name</th>
                                    <th style="border-bottom: 1px solid #ccc; text-align: left; padding: 8px;">Fee</th>
                                    {{-- <th style="border-bottom: 1px solid #ccc; text-align: left; padding: 8px;">GST</th> --}}
                                    <th style="border-bottom: 1px solid #ccc; text-align: right; padding: 8px;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #f8f9fa;">
                                    <td style="padding: 8px;">{{$payment_for}}</td>
                                    <td style="padding: 8px;">  {{$amount}}</td>
                                    {{-- <td style="padding: 8px;">10%</td> --}}
                                    <td style="padding: 8px; text-align: right;"> {{$amount}}</td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div>
    
                    <div style="display: flex; justify-content: space-between; margin-top: 30px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h6 style="font-size: 10px; font-weight: bold;">NOTES</h6>
                            <p style="font-size: 10px; margin: 0;">Sample text</p>
                        </div>
                        <div style="text-align: right;">
                            <h6 style="font-size: 12px; margin: 0;">Subtotal</h6>
                            {{-- <h6 style="font-size: 12px; margin: 0;">TAX (20%)</h6> --}}
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 12px; margin: 0;">{{$amount}}</p>
                            {{-- <p style="font-size: 12px; margin: 0;"> 4100.00</p> --}}
                        </div>
                    </div>
    
                    <div style="text-align: right; margin-top: 20px;">
                        <h6 style="font-size: 12px; font-weight: 600;">FINAL AMOUNT</h6>
                        <h6 style="font-size: 12px; font-weight: 600; color: #dc3545;"> {{$amount}}</h6>
                    </div>
                </div>
    
                
                <table style="width: 100%; padding: 20px; background-color: #f8f9fa; border-top: 0; border-collapse: collapse;">
                    <tr>
                        <!-- Left Section (Hospital Address) -->
                        <td style="width: 50%; padding-right: 20px;">
                            <h6 style="font-size: 12px; font-weight: bold;">Dr.Venky's Pet Hospital</h6>
                            <p style="font-size: 12px; margin: 0;">Above Dwaraka Honda Showroom, Sun City, Hydershakote,</p>
                            <p style="font-size: 12px; margin: 0;"> Bandlaguda Jagir, Telangana 500091</p>
                        </td>
                
                        <!-- Right Section (Contact Information) -->
                        <td style="width: 50%; text-align: right;">
                            <h6 style="font-size: 12px; font-weight: bold;">Questions and Contact</h6>
                            <p style="font-size: 12px; margin: 0;">info@drvenkysanimalhospital.com</p>
                            <p style="font-size: 12px; margin: 0;">+91 78988 87888</p>
                            
                        </td>
                    </tr>
                </table>
                


            </div>
        </div>
    </div>
    
    <script src="{{ public_path('css/bootstrap.bundle.min.js') }}" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>