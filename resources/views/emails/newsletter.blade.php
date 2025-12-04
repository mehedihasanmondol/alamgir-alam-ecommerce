@extends('emails.layout')

@section('title', 'Newsletter - ' . config('app.name'))

@section('content')
    <div class="greeting">
        <strong>Hi {{ $user->name }},</strong>
    </div>

    <p style="font-size: 16px; line-height: 1.8; color: #555555;">
        {{ $content }}
    </p>

    @if($featuredProducts && $featuredProducts->count() > 0)
    <div style="margin: 40px 0;">
        <h2 style="font-size: 24px; color: #333333; margin-bottom: 20px; text-align: center;">
            ✨ Featured Products This Week
        </h2>
        
        <div class="product-grid">
            @foreach($featuredProducts->chunk(2) as $chunk)
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
                            <div>
                                <span class="product-price">৳{{ number_format($product['price'], 2) }}</span>
                                @if($product['original_price'] > $product['price'])
                                <span class="product-old-price">৳{{ number_format($product['original_price'], 2) }}</span>
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
    @endif

    @if($blogPosts && $blogPosts->count() > 0)
    <div style="margin: 40px 0; background: #f8f9fa; padding: 30px 20px; border-radius: 8px;">
        <h2 style="font-size: 24px; color: #333333; margin-bottom: 20px; text-align: center;">
            📚 Latest from Our Blog
        </h2>
        
        @foreach($blogPosts as $post)
        <div style="background: white; margin-bottom: 20px; padding: 20px; border-radius: 6px; border-left: 4px solid #667eea;">
            <h3 style="font-size: 18px; color: #333333; margin: 0 0 10px 0;">
                {{ $post['title'] }}
            </h3>
            <p style="font-size: 14px; color: #666666; margin: 0 0 10px 0;">
                {{ Str::limit($post['excerpt'], 120) }}
            </p>
            <p style="font-size: 12px; color: #999999; margin: 0 0 15px 0;">
                <span style="background: #e9ecef; padding: 4px 10px; border-radius: 4px; margin-right: 10px;">{{ $post['category'] }}</span>
                {{ $post['published_at'] }}
            </p>
            <a href="{{ $post['url'] }}" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 14px;">
                Read More →
            </a>
        </div>
        @endforeach
    </div>
    @endif

    <div style="text-align: center; margin: 40px 0;">
        <a href="{{ route('shop') }}" class="button" style="font-size: 16px;">
            🛍️ Shop All Products
        </a>
    </div>

    <p style="font-size: 14px; color: #666666; margin-top: 30px;">
        Thank you for being a valued customer!<br>
        <strong>{{ config('app.name') }} Team</strong>
    </p>
@endsection
