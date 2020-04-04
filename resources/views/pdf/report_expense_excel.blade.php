<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian Peralatan dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </title>
</head>
<style>
    body {
        font-family: sans-serif
    }
    table {
        width: 100%;
        border: 1px solid #333;
        border-collapse: collapse;
    }

    table > tbody > tr, table > tbody > tr > td, table > tfoot > tr > th {
        border: 1px solid #333;
        border-collapse: collapse;
    }

    table > thead > tr > th {
        background-color: #e1e1e1;
        border: 1px solid #333;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        border-collapse: collapse;
    }

</style>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="5" style="text-align: center">Laporan Pembelian Peralatan dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->payment_date }}</td>
                    <td>{{ $expense->product_name }}</td>
                    <td style="text-align: right">{{ $expense->price }}</td>
                    <td>{{ $expense->qty }}</td>
                    <td style="text-align: right">{{ $expense->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right">Total Peralatan dibeli</th>
                <th style="text-align: right">{{ $expenses->sum('qty') }}
            </tr>
            <tr>
                <th colspan="4" style="text-align: right">Total Pembelian</th>
                <th style="text-align: right">{{ $expenses->sum('total') }}
            </tr>
        </tfoot>
    </table>
</body>
</html>