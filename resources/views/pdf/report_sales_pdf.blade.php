<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </title>
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
    <h5 style="text-align: center">Laporan Penjualan dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h5>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Harga Jual</th>
                <th>Harga Beli</th>
                <th>Qty</th>
                <th>Diskon</th>
                <th>Subtotal</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ Carbon\Carbon::parse($sale->created_at)->format('m/d/Y') }}</td>
                    <td>{{ $sale->product_name }}</td>
                    <td style="text-align: right">{{ $sale->price_formatted }}</td>
                    <td style="text-align: right">{{ $sale->cost_formatted }}</td>
                    <td>{{ $sale->qty }}</td>
                    <td style="text-align: right">{{ $sale->discount_formatted }}</td>
                    <td style="text-align: right">{{ $sale->subtotal_formatted }}</td>
                    <td style="text-align: right">{{ $sale->total_formatted }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" style="text-align: right">Total Terjual</th>
                <th style="text-align: right">{{ $total_selled }}
            </tr>
            <tr>
                <th colspan="7" style="text-align: right">Total Penjualan</th>
                <th style="text-align: right">{{ $total_sales }}
            </tr>
            <tr>
                <th colspan="7" style="text-align: right">Total Keuntungan</th>
                <th style="text-align: right">{{ $total_profit }}
            </tr>
        </tfoot>
    </table>
</body>
</html>