@extends('layouts.app')
@section('content')
<section class="bg-[#0f0f0a] text-white">
    <div class="max-w-5xl mx-auto px-4 py-12">

        {{-- Category + Date --}}
        <div class="text-xs uppercase tracking-widest text-[#d6b46a] font-semibold mb-3">
            {{ $post->category ?? 'Blog' }}
            <span class="mx-2">•</span>
            {{ optional($post->published_at)->format('d/m/Y') }}
        </div>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mb-4">
            {{ $post->title }}
        </h1>

        {{-- Excerpt --}}
        @if($post->excerpt)
            <p class="text-gray-300 text-lg max-w-3xl mb-8">
                {{ $post->excerpt }}
            </p>
        @endif

        {{-- Thumbnail --}}
        @if($post->thumbnail)
            <div class="rounded-2xl overflow-hidden mb-10">
                <img
                    src="{{ asset('storage/' . $post->thumbnail) }}"
                    alt="{{ $post->title }}"
                    class="w-full object-cover"
                >
            </div>
        @endif

        {{-- Content --}}
        <article class="prose prose-invert max-w-none leading-relaxed">
            @php
                $rendered = \App\Helpers\PostContentHelper::render($post->content ?? '');
            @endphp
            {!! $rendered !!}
        </article>

    </div>
</section>

<script>
(function () {
    function getCsrfToken() {
        const el = document.querySelector('meta[name="csrf-token"]');
        return el ? el.getAttribute('content') : '';
    }

    async function addToCart(productId, qty, btn) {
        btn.disabled = true;
        const oldText = btn.textContent;
        btn.textContent = 'Đang thêm...';

        try {
            const res = await fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin', // ✅ quan trọng để gửi session cookie
                body: JSON.stringify({ product_id: Number(productId), quantity: Number(qty || 1) })
            });

            // Nếu API bạn trả JSON
            let data = null;
            try { data = await res.json(); } catch(e) {}

            if (!res.ok) {
                const msg = (data && (data.message || data.error)) ? (data.message || data.error) : 'Thêm vào giỏ thất bại';
                throw new Error(msg);
            }

            // UI feedback đơn giản
            const wrapper = btn.closest('div')?.parentElement;
            const msgEl = wrapper ? wrapper.querySelector('.js-cart-msg') : null;
            if (msgEl) {
                msgEl.style.display = 'block';
                msgEl.textContent = '✅ Đã thêm vào giỏ!';
                setTimeout(() => msgEl.style.display = 'none', 2500);
            } else {
                alert('Đã thêm vào giỏ!');
            }

            // (optional) nếu bạn có badge giỏ hàng, mình có thể giúp cập nhật luôn
        } catch (err) {
            alert(err.message || 'Có lỗi xảy ra');
        } finally {
            btn.disabled = false;
            btn.textContent = oldText;
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-add-to-cart');
        if (!btn) return;

        const productId = btn.dataset.productId;
        const qty = btn.dataset.qty || 1;
        addToCart(productId, qty, btn);
    });
})();
</script>
@endsection
