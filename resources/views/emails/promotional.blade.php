@extends('emails.layout')

@section('title', 'Special Offer - ' . config('app.name'))

@section('content')
    <div class="greeting">
        <strong>Hi {{ $user->name }},</strong>
    </div>

    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; text-align: center; margin: 30px 0;">
        <h2 style="font-size: 32px; margin: 0 0 15px 0; color: white;">
            {{ $promotionTitle }}
        </h2>
        <p style="font-size: 16px; margin: 0; color: #f0f0f0;">
            {{ $promotionDescription }}
        </p>
    </div>

    @if($discountCode)
    <div style="background: #fff3cd; border: 2px dashed #ffc107; padding: 25px; border-radius: 8px; text-align: center; margin: 30px 0;">
        <p style="font-size: 14px; color: #856404; margin: 0 0 10px 0; font-weight: 600;">
            🎁 USE THIS COUPON CODE
        </p>
        <div style="background: white; padding: 15px 30px; border-radius: 6px; display: inline-block; margin: 10px 0;">
            <span style="font-size: 28px; font-weight: 700; color: #667eea; letter-spacing: 2px;">
                {{ $discountCode }}
            </span>
        </div>
        @if($discountPercentage)
        <p style="font-size: 18px; color: #856404; margin: 15px 0 0 0; font-weight: 600;">
            Save {{ $discountPercentage }}% on your order!
        </p>
        @endif
        @if($expiryDate)
        <p style="font-size: 13px; color: #856404; margin: 10px 0 0 0;">
            ⏰ Offer expires: {{ $expiryDate }}
        </p>
        @endif
    </div>
    @endif

    @if($products && $products->count() > 0)
    <div style="margin: 40px 0;">
        <h2 style="font-size: 24px; color: #333333; margin-bottom: 20px; text-align: center;">
            🔥 Hot Deals for You
        </h2>
        
        <div class="product-grid">
            @foreach($products->chunk(2) as $chunk)
                @foreach($chunk as $product)
                <div class="product-item">
                    <div class="product-card">
                        @if($product['discount_percentage'] > 0)
                        <div style="position: relative;">
                            @if($product['image'])
                            <img src="{{ asset('storage/' . $product['image']) }}" 
                                 alt="{{ $product['name'] }}" 
                                 class="product-image"
                                 style="width: 100%; height: 180px; object-fit: cover;">
                            @else
                            <div style="width: 100%; height: 180px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #999;">
                                No Image
                            </div>
                            @endif
                            <div style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; padding: 6px 12px; border-radius: 20px; font-weight: 700; font-size: 14px;">
                                -{{ $product['discount_percentage'] }}%
                            </div>
                        </div>
                        @else
                        @if($product['image'])
                        <img src="{{ asset('storage/' . $product['image']) }}" 
                             alt="{{ $product['name'] }}" 
                             class="product-image"
                             style="width: 100%; height: 180px; object-fit: cover;">
                        @else
                        <div style="width: 100%; height: 180px; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #999;">
                            No Image
                        </div>
                        @endif
                        @endif
                        <div class="product-details">
                            <p class="product-name">{{ Str::limit($product['name'], 50) }}</p>
                            <div>
                                <span class="product-price">৳{{ number_format($product['price'], 2) }}</span>
                                @if($product['original_price'] > $product['price'])
                                <span class="product-old-price">৳{{ number_format($product['original_price'], 2) }}</span>
                                @endif
                            </div>
                            <a href="{{ $product['url'] }}" class="button" style="margin-top: 15px; display: inline-block; padding: 10px 20px; font-size: 14px;">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
    @endif

    <div style="text-align: center; margin: 40px 0;">
        <a href="{{ route('shop') }}" class="button" style="font-size: 18px; padding: 16px 40px;">
            🛒 Shop All Deals
        </a>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px;">
        <p style="font-size: 13px; color: #666666; margin: 0; text-align: center;">
            ⚡ Hurry! Limited time offer. Don't miss out on these amazing deals!
        </p>
    </div>

    <p style="font-size: 14px; color: #666666; margin-top: 30px;">
        Happy Shopping!<br>
        <strong>{{ config('app.name') }} Team</strong>
    </p>
@endsection
