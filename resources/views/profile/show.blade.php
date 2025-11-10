@extends('layouts.app')

@section('title', 'Profile - Reloved')

@section('content')
<div class="container mx-auto px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-xl font-bold text-text-primary mb-6">Profile Saya</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-10">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-16 border border-border p-6">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Avatar Section -->
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-semibold text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-text-secondary mb-1">Nama</label>
                            <p class="text-lg text-text-primary">{{ $user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-secondary mb-1">Email</label>
                            <p class="text-lg text-text-primary">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                                <span class="text-sm text-green-600">✓ Terverifikasi</span>
                            @else
                                <span class="text-sm text-red-600">Belum terverifikasi</span>
                            @endif
                        </div>

                        @if($user->phone)
                        <div>
                            <label class="block text-sm font-medium text-text-secondary mb-1">No. Telepon</label>
                            <p class="text-lg text-text-primary">{{ $user->phone }}</p>
                            @if($user->is_phone_verified)
                                <span class="text-sm text-green-600">✓ Terverifikasi</span>
                            @endif
                        </div>
                        @endif

                        @if($user->city || $user->province)
                        <div>
                            <label class="block text-sm font-medium text-text-secondary mb-1">Lokasi</label>
                            <p class="text-lg text-text-primary">
                                @php
                                    // Check if province/city is still an ID (numeric)
                                    $province = $user->province;
                                    $city = $user->city;
                                    $isProvinceId = $province && is_numeric($province);
                                    $isCityId = $city && is_numeric($city);
                                @endphp
                                @if($city && $province)
                                    @if($isCityId || $isProvinceId)
                                        <span class="text-yellow-600 text-sm">(Silakan edit profile untuk memperbarui lokasi)</span>
                                    @else
                                        {{ $city }}, {{ $province }}
                                    @endif
                                @elseif($city)
                                    @if($isCityId)
                                        <span class="text-yellow-600 text-sm">(Silakan edit profile untuk memperbarui lokasi)</span>
                                    @else
                                        {{ $city }}
                                    @endif
                                @elseif($province)
                                    @if($isProvinceId)
                                        <span class="text-yellow-600 text-sm">(Silakan edit profile untuk memperbarui lokasi)</span>
                                    @else
                                        {{ $province }}
                                    @endif
                                @endif
                            </p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-text-secondary mb-1">Rating</label>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-semibold text-text-primary">{{ number_format($user->rating_avg, 1) }}</span>
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($user->rating_avg))
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @elseif($i - 0.5 <= $user->rating_avg)
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-sm text-text-tertiary">({{ $user->review_count }} review)</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('profile.edit') }}" class="inline-block bg-primary text-white px-6 py-2 rounded-8 font-semibold hover:bg-primary/90 transition">
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        @if($user->review_count > 0)
        <div class="bg-white rounded-8 border border-border p-8 mt-8">
            <h2 class="text-2xl font-bold text-text-primary mb-6">Reviews</h2>
            <div class="space-y-4">
                @foreach($user->reviews()->with('reviewer', 'product')->latest()->limit(10)->get() as $review)
                    <div class="bg-white rounded-8 border border-border p-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                                @if($review->reviewer->avatar)
                                    <img src="{{ $review->reviewer->avatar }}" alt="{{ $review->reviewer->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    <span class="text-gray-600 font-semibold">{{ substr($review->reviewer->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-semibold text-text-primary">{{ $review->reviewer->name }}</p>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($review->product)
                                    <p class="text-sm text-text-secondary mb-1">Untuk produk: <a href="{{ route('products.show', $review->product->id) }}" class="text-primary hover:underline">{{ $review->product->title }}</a></p>
                                @endif
                                @if($review->comment)
                                    <p class="text-sm text-text-secondary mb-2">{{ $review->comment }}</p>
                                @endif
                                @if($review->images && count($review->images) > 0)
                                    <div class="flex gap-2 mb-2">
                                        @foreach($review->images as $image)
                                            <img src="{{ $image }}" alt="Review Image" class="w-16 h-16 object-cover rounded-8 border border-border">
                                        @endforeach
                                    </div>
                                @endif
                                <p class="text-xs text-text-tertiary">{{ $review->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

