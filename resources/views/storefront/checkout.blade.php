@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-10 md:py-12 pb-20">
    <div class="text-[28px] md:text-[34px] font-extrabold mb-8">{{ __('storefront.checkout') }}</div>

    @if ($errors->any())
        <div class="mb-6 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl max-w-xl">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}" class="flex flex-col lg:flex-row gap-10 items-start">
        @csrf
        <div class="flex-1 w-full">
            <div class="text-base font-extrabold mb-5">{{ __('storefront.customer_info') }}</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('storefront.first_name') }}" required class="border border-border rounded-xl px-4 py-3.5 text-sm">
                <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('storefront.last_name') }}" required class="border border-border rounded-xl px-4 py-3.5 text-sm">
            </div>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('storefront.phone') }}" required class="w-full border border-border rounded-xl px-4 py-3.5 text-sm mb-4">
            <input type="text" name="address" value="{{ old('address') }}" placeholder="{{ __('storefront.address') }}" required class="w-full border border-border rounded-xl px-4 py-3.5 text-sm mb-4">
            <input type="text" name="city" value="{{ old('city') }}" placeholder="{{ __('storefront.city') }}" required class="w-full border border-border rounded-xl px-4 py-3.5 text-sm mb-4">
            <textarea name="notes" placeholder="{{ __('storefront.notes') }}" rows="3" class="w-full border border-border rounded-xl px-4 py-3.5 text-sm resize-y">{{ old('notes') }}</textarea>
        </div>

        <div class="w-full lg:w-[360px] shrink-0 bg-cream rounded-3xl p-7">
            <div class="text-[17px] font-extrabold mb-5">{{ __('storefront.order_summary') }}</div>
            <div class="flex flex-col gap-3.5 mb-5">
                @foreach ($lines as $line)
                    <div class="flex justify-between text-[13.5px]">
                        <span>{{ $line->product->name }} × {{ $line->qty }}</span>
                        <span class="font-bold">{{ number_format($line->lineTotal, 3) }} {{ __('storefront.currency') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-black/10 pt-3.5 flex justify-between text-[17px] font-extrabold mb-5">
                <span>{{ __('storefront.total') }}</span><span>{{ number_format($subtotal - $discount + $shipping, 3) }} {{ __('storefront.currency') }}</span>
            </div>
            <div class="bg-ink text-white rounded-xl px-3.5 py-3 text-[12.5px] font-bold text-center mb-5">💵 {{ __('storefront.cod_only') }}</div>
            <button type="submit" class="w-full bg-gold text-ink py-4 rounded-full font-bold text-[15px]">{{ __('storefront.place_order') }}</button>
        </div>
    </form>
</div>

<script>
if (typeof fbq === 'function') {
    fbq('track', 'InitiateCheckout', @json($checkoutPayload), {eventID: '{{ $eventId }}'});
}
</script>
@endsection
