@extends('emails.layout')

@section('title', 'Products You Might Like - ' . config('app.name'))

@section('content')
    <div class="greeting">
        <strong>Hi {{ $user->name }},</strong>
    </div>

    <p style="font-size: 16px; line-height: 1.8; color: #555555;">
        We've handpicked some products just for you based on {{ $basedOn }}. 
        Check them out and discover your next favorite item!
    </p>

    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; margin: 30px 0;">
        <h2 style="font-size: 28px; margin: 0; color: white;">
            ⭐ Recommended Just For You
        </h2>
    </div>

    @if($recommendedProducts && $recommendedProducts->count() > 0)
    <div style="margin: 40px 0;">
        <div class="product-grid">
            @foreach($recommendedProducts->chunk(2) as $chunk)
                @foreach($chunk as $product)
                <div class="product-item">
                    <div class="product-card">
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
                        <div class="product-details">
                            <p class="product-name">{{ Str::limit($product['name'], 50) }}</p>
                            
                            @if($product['rating'] > 0)
                            <div style="margin: 8px 0;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($product['rating']))
                                    <span style="color: #ffc107; font-size: 14px;">★</span>
                                    @else
                                    <span style="color: #e0e0e0; font-size: 14px;">★</span>
                                    @endif
                                @endfor
                                <span style="font-size: 12px; color: #999999; margin-left: 5px;">
                                    ({{ $product['reviews_count'] }})
                                </span>
                            </div>
                            @endif
                            
                            <div>
                                <span class="product-price">৳{{ number_format($product['price'], 2) }}</span>
                                @if($product['original_price'] > $product['price'])
                                <span class="product-old-price">৳{{ number_format($product['original_price'], 2) }}</span>
                                <div style="margin-top: 5px;">
                                    <span style="background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        Save ৳{{ number_format($product['original_price'] - $product['price'], 2) }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            <a href="{{ $product['url'] }}" class="button" style="margin-top: 15px; display: inline-block; padding: 10px 20px; font-size: 14px;">
                                View Product
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
    @else
    <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
        <p style="font-size: 16px; color: #666666;">
            We're curating personalized recommendations for you. Check back soon!
        </p>
    </div>
    @endif

    <div style="background: #e7f3ff; border-left: 4px solid #2196F3; padding: 20px; border-radius: 6px; margin: 30px 0;">
        <h3 style="font-size: 18px; color: #1976D2; margin: 0 0 10px 0;">
            💡 Why These Recommendations?
        </h3>
        <p style="font-size: 14px; color: #555555; margin: 0; line-height: 1.6;">
            Our smart recommendation system analyzes {{ $basedOn }} to suggest products that match your interests and needs. 
            We're constantly learning to provide you with the best shopping experience!
        </p>
    </div>

    <div style="text-align: center; margin: 40px 0;">
        <a href="{{ route('shop') }}" class="button" style="font-size: 18px; padding: 16px 40px;">
            🔍 Explore More Products
        </a>
    </div>

    <div style="background: #fff8e1; padding: 20px; border-radius: 8px; margin-top: 30px; text-align: center;">
        <p style="font-size: 14px; color: #f57c00; margin: 0;">
            <strong>🎁 Pro Tip:</strong> Save your favorite products to your wishlist for easy access later!
        </p>
    </div>

    <p style="font-size: 14px; color: #666666; margin-top: 30px;">
        Happy Shopping!<br>
        <strong>{{ config('app.name') }} Team</strong>
    </p>
@endsection
