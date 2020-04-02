<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label</title>
</head>
<body>
    <table style="width: 100%; font-family: sans-serif">
    @foreach($data->chunk(3) as $chunk)
        <tr>
            @foreach($chunk as $product)
            <td><small><strong>{{ $product->name }}</strong></small></td>
            @endforeach
        </tr>
        <tr>
            @foreach($chunk as $product)
            <td><h2 style="margin: 0px">{{ $product->price_formatted }}</h2></td>
            @endforeach
        </tr>
        <tr>
            @foreach($chunk as $product)
            <td>
                <div>{!! DNS1D::getBarcodeHTML($product->code, "EAN13", 1.5,33) !!}</div>
                <div style="margin-bottom: 15px; font-size: .8em; letter-spacing: 5px"><small>{{ $product->code }}</small></div>
            </td>
            @endforeach
        </tr>
    @endforeach
    </table>
</body>
</html>