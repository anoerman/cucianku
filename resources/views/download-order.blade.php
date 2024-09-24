<style>
    table {
        font-size: 8px;
        width: 100% !important;
        margin-left: -24px;
        /* margin-right: 10px !important; */
    }
</style>
<table style="">
    <tr>
        <td style="width: 50%">Date</td>
        <td></td>
        <td align="right">{{ $record->created_at }}</td>
    </tr>
    <tr>
        <td>Name</td>
        <td></td>
        <td align="right">{{ $record->customer->name }}</td>
    </tr>
    <tr>
        <td>Phone</td>
        <td></td>
        <td align="right">{{ $record->customer->phone }}</td>
    </tr>
    <tr>
        <td colspan="3">
            <hr>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <h3>Details</h3>
        </td>
    </tr>
    <tr>
        <th align="left">Product</th>
        <th align="right">Qty</th>
        <th align="right">Price</th>
    </tr>
    @foreach ($record->details as $detail)
        <tr>
            <td>{{ $detail->product->name }}</td>
            <td align="right">{{ $detail->qty }}</td>
            <td align="right">{{ number_format($detail->price, 0, ',', '.') }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="3">
            <hr>
        </td>
    </tr>
    <tr>
        <th align="left" colspan="2">Subtotal Price</th>
        <th align="right">{{ number_format($record->subtotal_price, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th align="left" colspan="2">Discount</th>
        <th align="right">{{ number_format($record->total_discount, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th align="left" colspan="2">Total Price</th>
        <th align="right">{{ number_format($record->total_price, 0, ',', '.') }}</th>
    </tr>
</table>
