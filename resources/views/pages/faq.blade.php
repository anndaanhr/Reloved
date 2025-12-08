@extends('layouts.app')

@section('title', 'FAQ - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Pertanyaan yang Sering Diajukan</h1>
        
        <div class="bg-white rounded-8 border border-border p-8 space-y-8">
            @foreach($faqs as $category)
            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">{{ $category['category'] }}</h2>
                <div class="space-y-4">
                    @foreach($category['questions'] as $faq)
                    <div class="bg-gray-50 rounded-8 p-6 border border-border">
                        <h3 class="font-bold text-lg text-text-primary mb-2">{{ $faq['question'] }}</h3>
                        <p class="text-text-secondary text-sm">{{ $faq['answer'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="pt-6 border-t border-border">
                <h3 class="font-bold text-lg text-text-primary mb-4">Masih punya pertanyaan?</h3>
                <p class="text-text-secondary mb-4">Jika pertanyaan Anda belum terjawab, jangan ragu untuk menghubungi kami.</p>
                <a href="{{ route('pages.contact') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

