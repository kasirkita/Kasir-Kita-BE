<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </title>
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
    <h5 style="text-align: center">Laporan Stok dari tanggal {{ Carbon\Carbon::parse($start_date)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end_date)->format('d/m/Y') }} </h5>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Stok</th>
                <th>Keterangan</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocks as $stock)
                <tr>
                    <td>{{ Carbon\Carbon::parse($stock->created_at)->format('m/d/Y') }}</td>
                    <td>{{ $stock->stock->product->name }}</td>
                    <td style="text-align: right">{!! $stock->type == '+' ? '<span style="color: #28a745">+'.$stock->amount.'</span>'  : '<span style="color: #dc3545">-'.$stock->amount.'</span>' !!}</td>
                    <td>{{ $stock->description }}</td>
                    <td style="text-align: right">{{ !empty($stock->user) ? $stock->user->name : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>