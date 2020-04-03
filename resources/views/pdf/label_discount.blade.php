<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label</title>
</head>
<style>
    h2 sup {
    font-size: .5em;
    vertical-align: top;
    text-decoration: line-through
  }

  .small {
      font-size: .7em;
  }

  table > tr  {
    padding-bottom: 15px;
  }

</style>
<body>
    <table style="width: 100%; font-family: sans-serif">
    @foreach($data->chunk(3) as $chunk)
        <tr>
            @foreach($chunk as $discount)
            <td><small><strong>{{ $discount->product->name }}</strong></small></td>
            @endforeach
        </tr>
        <tr>
            @foreach($chunk as $discount)
            <td><h2 style="margin: 0px"><sup>{{ $discount->product->price_formatted  }}</sup>{{ Common::formattedNumber($discount->type == 'fix' ? $discount->product->price - $discount->amount : $discount->product->price  - ($discount->product->price * ($discount->amount / 100))) }}</h2></td>
            @endforeach
        </tr>
        <tr>
            @foreach($chunk as $discount)
            <td style="padding-bottom: 20px;">
                <div>
                    <small class="small">*) Setiap pembelian {{ $discount->term == '>' ? 'lebih dari' : '' }} {{ $discount->total_qty }} {{ $discount->product->unit ? $discount->product->unit->name : '' }}</small>
                </div>
                <div>
                    <small class="small">*) Berlaku hannya untuk satu barang</small>
                </div>
                <div>
                    <small class="small">*) Berlaku sampai {{ $discount->valid_thru_formatted }}</small>
                </div>
                @if (!empty($discount->customer_type))
                    @if ($discount->customer_type == 'wholesaler')
                        <small class="small">*) Khusus member grosir</small>
                    @else
                        <small class="small">*) Khusus member pengecer</small>
                    @endif
                @endif
            </td>
            @endforeach
        </tr>
    @endforeach
    </table>
</body>
</html>