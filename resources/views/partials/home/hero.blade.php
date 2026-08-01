{{-- Hero --}}
<section class="relative min-h-[70vh] lg:min-h-[85vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ $hero?->image?->largeUrl() ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuD_zPQkOZ0UDXu6FuZzj-NrMd8CyrROuDsB1jxIqN5QwlU5avH9VAxDy7t8KKIupI_NbkcnDemuS-nXboEENJTjxOQsc_DiKkSQUr72qCMvf_0Czk1dtThvF2_AxTG2iKB2wNp55x9_UmJ_KIuEPqmrB4tuwjtN0VWWZxjBSmcRcoFRwZx7TUnpPfEexJeTQrHvVOlQxKL97Y3NWXgKpn-ZLmq0rp47JiYbZcneKInNT_jSF0y4Hsfpd0vYWdhgG-6aM6RJRILoLGph' }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-on-surface/50 to-transparent"></div>
    </div>
    <div class="relative z-10 px-5 lg:px-margin-desktop max-w-container-max mx-auto w-full">
        <div class="max-w-xl text-white">
            <span class="text-label-md uppercase tracking-[0.2em] mb-4 block text-secondary-fixed">{{ $hero?->heading_1 ?? 'Exquisite Craftsmanship' }}</span>
            <h1 class="font-serif text-headline-lg-mobile lg:text-headline-lg mb-6 leading-tight">{{ $hero?->heading_2 ?? 'Preserving the Soul of Ancient Artistry' }}</h1>
            <p class="text-body-lg mb-10 text-white/90">{{ $hero?->description ?? 'Discover our curated collection of handcrafted treasures, made by master artisans who carry centuries of tradition in every piece.' }}</p>
            <a href="{{ $hero?->button_url ?? route('shop.index') }}" class="inline-flex items-center justify-center px-10 py-5 bg-primary text-white text-button hover:bg-primary-container transition-all active:scale-[0.98]">
                {{ $hero?->button_text ?? 'Shop the Collection' }}
            </a>
        </div>
    </div>
</section>
