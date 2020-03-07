<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label</title>
</head>
<body>
    <table style="width: 100%">
    @foreach($data->chunk(3) as $chunk)
        <tr>
            @foreach($chunk as $product)
            <td><h4 style="margin: 5px"><strong>{{ $product->price_formatted }}/{{ $product->unit->name }}</strong></h4></td>
            @endforeach
        </tr>
        <tr>
            @foreach($chunk as $product)
            <td>{!! DNS1D::getBarcodeHTML($product->code, "EAN13") !!}</td>
            @endforeach
        </tr>
        <tr>
            @foreach($chunk as $product)
            <td><p style="margin: 5px; margin-bottom: 10px; font-size: .8em">{{ $product->name }}</p></td>
            @endforeach
        </tr>
    @endforeach
    </table>
</body>
</html>