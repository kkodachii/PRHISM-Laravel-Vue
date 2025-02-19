<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Expiring Medicine Report</title>
    <style>
        #footer { position: fixed; right: 0px; bottom: -100px; text-align: center;border-top: 1px solid black;}
        #footer .page:after { content: counter(page, decimal); }
        @page { margin: 140px 50px; }

         body {
             font-family: "Helvetica";
             font-weight: 400;
             font-style: normal;
         }

         .header {
             position: fixed;
             left: 0px;
             top: -120px;
             right: 0px;
             width: 100%;
             align-items: center;
             justify-content: space-between;
             border-bottom: 3px solid #000;
         }

         .header img {
             border-radius: 50%;
             max-width: 10rem;
             padding: 0.25rem;
             aspect-ratio: 1;
         }

         .header h3 {
             font-size: 1.25rem;
             font-weight: bold;
             margin-bottom: 0.25rem;
         }

         .header p {
             padding: 0rem;
             font-size: 0.8rem;
         }

         h1 {
             font-family: "Helvetica";
             font-size: 2rem;
             font-weight: bold;
             margin-bottom: 1rem;
             margin-top: 0rem;
         }

         h2 {
             font-family: "Helvetica";
             font-size: 1.25rem;
             font-weight: 600;
             margin-bottom: 0.5rem;
         }

         table {
             width: 100%;
             background-color: #fff;
             border: 1px solid #000;
             border-collapse: collapse;
         }
         th{
             border: 1px solid #000;
             background-color: rgb(187, 187, 187);
         }

         td{
             border: 1px solid #000;
             text-align: center;
         }

         .borderless{
             border: none;
         }
     </style>
 </head>
 <body>
     <div class="header" width="100%">
         <table class="borderless">
             <tr>
                 <td class="borderless" style="width: 30%; text-align:left;">
                     <img src="{{ public_path('prhism/logo.svg') }}" alt="Logo" style="width: 100px;">
                 </td>
                 <td class="borderless" style="width: 70%; text-align: right;">
                     <h3>RHU Paombong</h3>
                     <table class="borderless">
                             <tr>
                                 <td class="borderless" style="text-align: right;">Email: mho.paombong@gmail.com</td>
                             </tr>
                             <tr>
                                 <td class="borderless" style="text-align: right;">Phone: +63 446651202</td>
                             </tr>
                             <tr>
                                 <td class="borderless" style="text-align: right;">Location: Brgy. Poblacion, Paombong, Bulacan</td>
                             </tr>
                     </table>
                 </td>
             </tr>
         </table>
     </div>

    <h1 style="font-size: 25px; margin-bottom: 25px; text-align: center;">Expiring Medicine Report: <span style="font-weight: 400">{{$date}}</span></h1>
    <table class="borderless">
        <tr>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Generated by: </span>{{ $name ?? 'RHU Main' }}</td>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Days: </span>{{ $days }}</td>
        </tr>
        <tr>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Generated at: </span>{{ $barangay_name ?? 'RHU Main' }}</td>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Date Range:: </span>{{ $dateRange ?? 'All' }}</td>
        </tr>
    </table>
@if($medicines->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th>Expiration Date</th>
                <th>Batch</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Date Acquired</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicines as $medicine)
            <tr>
                <td>{{ \Carbon\Carbon::parse($medicine->expiration_date)->format('F j, Y') }}</td>
                <td>{{ $medicine->batch->batch_number }}</td>
                <td>{{ $medicine->generic_name->generic_name }}</td>
                <td>{{ $medicine->quantity }}</td>
                <td>{{ $medicine->brand }}</td>
                <td>{{ $medicine->category->category }}</td>
                <td>{{ \Carbon\Carbon::parse($medicine->rawDateAcquired)->format('F j, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif

<div id="footer">
    <p class="page">Page </p>
  </div>

</body>
</html>
