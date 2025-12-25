<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
</div>
<div style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Chào {{ $user->name }},</h2>

    <p>Bạn còn sản phẩm trong giỏ hàng. Tổng giá trị tạm tính: <b>{{ number_format($total, 0, ',', '.') }}đ</b></p>

    <ul>
        @foreach($items as $i)
            <li>
                {{ $i->product?->name ?? 'Sản phẩm' }}
                (x{{ $i->quantity }}) -
                {{ number_format((float)$i->price_at_time, 0, ',', '.') }}đ
            </li>
        @endforeach
    </ul>

    @if($voucher)
        <p>
            Mình tặng bạn mã giảm giá: <b>{{ $voucher->code }}</b><br>
            Hiệu lực đến: {{ optional($voucher->end_date)->format('d/m/Y H:i') }}
        </p>
    @endif

    <p>
        Bạn có thể quay lại giỏ hàng tại đây:
        <a href="{{ url('/cart') }}">{{ url('/cart') }}</a>
    </p>

    <p>Cảm ơn bạn!<br>Lenlab</p
</div>
