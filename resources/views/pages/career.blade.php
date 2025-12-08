@extends('layouts.app')

@section('title', 'Karir - Reloved')

@section('content')
<div class="container mx-auto px-8 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-text-primary mb-8">Bergabung dengan Tim Reloved</h1>
        
        <div class="bg-white rounded-8 border border-border p-8 space-y-8">
            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">Mengapa Bergabung dengan Kami?</h2>
                <p class="text-text-secondary leading-relaxed mb-4">
                    Reloved adalah tempat di mana passion untuk teknologi bertemu dengan komitmen terhadap lingkungan. 
                    Kami mencari individu yang bersemangat untuk membuat perubahan positif di dunia e-commerce.
                </p>
                <ul class="list-disc list-inside space-y-2 text-text-secondary">
                    <li>Lingkungan kerja yang inovatif dan kolaboratif</li>
                    <li>Kesempatan untuk berkembang dan belajar</li>
                    <li>Berkontribusi pada misi ramah lingkungan</li>
                    <li>Tim yang beragam dan inklusif</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-text-primary mb-4">Posisi yang Tersedia</h2>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-8 p-6 border border-border">
                        <h3 class="font-bold text-lg text-text-primary mb-2">Full Stack Developer</h3>
                        <p class="text-text-secondary text-sm mb-3">Kami mencari developer berpengalaman dengan Laravel dan Vue.js untuk mengembangkan fitur-fitur baru platform.</p>
                        <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-semibold">Full-time</span>
                        <span class="inline-block bg-gray-200 text-text-secondary px-3 py-1 rounded-full text-xs font-semibold ml-2">Remote</span>
                    </div>
                    <div class="bg-gray-50 rounded-8 p-6 border border-border">
                        <h3 class="font-bold text-lg text-text-primary mb-2">UI/UX Designer</h3>
                        <p class="text-text-secondary text-sm mb-3">Cari designer kreatif untuk meningkatkan pengalaman pengguna dan desain visual platform kami.</p>
                        <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-semibold">Full-time</span>
                        <span class="inline-block bg-gray-200 text-text-secondary px-3 py-1 rounded-full text-xs font-semibold ml-2">Hybrid</span>
                    </div>
                    <div class="bg-gray-50 rounded-8 p-6 border border-border">
                        <h3 class="font-bold text-lg text-text-primary mb-2">Marketing Specialist</h3>
                        <p class="text-text-secondary text-sm mb-3">Butuh ahli marketing digital untuk mengembangkan strategi pemasaran dan meningkatkan brand awareness.</p>
                        <span class="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-semibold">Full-time</span>
                        <span class="inline-block bg-gray-200 text-text-secondary px-3 py-1 rounded-full text-xs font-semibold ml-2">On-site</span>
                    </div>
                </div>
            </div>

            <div class="bg-primary/5 rounded-8 p-6 border border-primary/20">
                <h3 class="font-bold text-lg text-text-primary mb-3">Tidak menemukan posisi yang sesuai?</h3>
                <p class="text-text-secondary text-sm mb-4">
                    Kami selalu mencari talenta baru! Kirimkan CV dan portfolio Anda ke <a href="mailto:career@reloved.com" class="text-primary hover:underline">career@reloved.com</a>
                </p>
                <a href="{{ route('pages.contact') }}" class="inline-block bg-primary text-white px-6 py-3 rounded-8 font-semibold hover:bg-primary/90 transition text-sm">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

