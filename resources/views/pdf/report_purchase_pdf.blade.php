<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian Barang dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </title>
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
    <h5 style="text-align: center">Laporan Pembelian Barang dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h5>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Harga Jual</th>
                <th>Harga Beli</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td>{{ Carbon\Carbon::parse($purchase->purchase->payment_date)->format('m/d/Y') }}</td>
                    <td>{{ $purchase->product_name }}</td>
                    <td style="text-align: right">{{ $purchase->price_formatted }}</td>
                    <td style="text-align: right">{{ $purchase->cost_formatted }}</td>
                    <td>{{ $purchase->qty }}</td>
                    <td style="text-align: right">{{ $purchase->subtotal_formatted }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right">Total Barang dibeli</th>
                <th style="text-align: right">{{ $total_purchased }}
            </tr>
            <tr>
                <th colspan="5" style="text-align: right">Total Pembelian Barang</th>
                <th style="text-align: right">{{ $total_purchase }}
            </tr>
        </tfoot>
    </table>
</body>
</html>