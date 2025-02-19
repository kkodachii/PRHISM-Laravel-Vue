<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dispense Report</title>
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

         .space-between {
            border-collapse: separate;
            border-spacing: 0 1em;
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

    <h1 style="font-size: 25px; margin-bottom: 25px; text-align: center;">Dispense Report: <span style="font-weight: 400">{{$date}}</span></h1>
    <table class="borderless">
        <tr>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Generated by: </span>{{ $name ?? 'RHU Main' }}</td>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Barangay: </span>{{ $barangay ?? 'Delivered' }}</td>
        </tr>
        <tr>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Generated at: </span>{{ $barangay_name ?? 'RHU Main' }}</td>
            <td class="borderless" style="text-align: left"><span style="font-weight:300">Date Range:: </span>{{ $dateRange ?? 'All' }}</td>
        </tr>
    </table>

@foreach($Barangaydispenses as $barangayName => $dispenses)
<h2>{{ $barangayName }}</h2>
     @foreach($dispenses as $index => $dispense)
     <span style="font-weight:300">Dispensation #{{ $index + 1 }}</span>
     <table style="margin-bottom: 0.25em;">
         <thead>
             <tr>
                 <th>Barangay</th>
                 <th>Provider Name</th>
                 <th>Provider Position</th>
                 <th>Recipients Name</th>
                 <th>Birthday</th>
                 <th>Age</th>
                 <th>Address</th>
                 <th>Reason</th>
                 <th>Date Delivered</th>
             </tr>
         </thead>
         <tbody>
             <tr>
                 <td>{{ $dispense->barangay->barangay_name }}</td>
                 <td>{{ $dispense->provider_name }}</td>
                 <td>{{ $dispense->position }}</td>
                 <td>{{ $dispense->recipients_name }}</td>
                 <td>{{ $dispense->birthday }}</td>
                 <td>{{ $dispense->age }}</td>
                 <td>{{ $dispense->address }}</td>
                 <td>{{ $dispense->reason }}</td>
                 <td>{{ \Carbon\Carbon::parse($dispense->created_at)->format('F j, Y') }}</td>
             </tr>
         </tbody>
     </table>
     <table style="margin-top: 0.25em;">
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispense->dispenseItems as $item)
            <tr>
                <td>
                    {{ $item->type }}
                </td>
                <td>
                    {{ $item->name }}
                </td>
                <td>
                    {{ $item->quantity }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

     <div style="height: 1em;">&nbsp;</div> {{-- Optional spacer between each request --}}
 @endforeach
@endforeach

<div id="footer">
    <p class="page">Page </p>
  </div>
</body>
</html>
