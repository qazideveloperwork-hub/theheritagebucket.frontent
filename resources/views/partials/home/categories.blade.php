{{-- Shop by Category --}}
@if ($categorySlides->isNotEmpty() || $categories->isNotEmpty())
    <section class="py-16 lg:py-section-padding px-5 lg:px-margin-desktop max-w-container-max mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <h2 class="font-serif text-headline-md text-on-surface mb-2">Shop by Category</h2>
                <p class="text-body-md text-on-surface-variant max-w-lg">Each collection carries its own craft tradition, from hand-painted pottery to hand-woven textiles.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="text-label-md text-primary border-b border-primary pb-1 hover:text-primary-container transition-colors">View All</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @if ($categorySlides->isNotEmpty())
                @foreach ($categorySlides as $slide)
                    @continue(! $slide->category)
                    <a href="{{ route('shop.index', ['categories' => [$slide->category_id]]) }}" class="relative group cursor-pointer overflow-hidden border border-outline-variant aspect-square">
                        @if ($slide->image)
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('{{ $slide->image->mediumUrl() }}');"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-on-surface/70 via-transparent to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-surface-container"></div>
                        @endif
                        <div class="absolute bottom-0 left-0 p-6">
                            <h3 class="font-serif text-headline-sm text-white">{{ $slide->category->name }}</h3>
                        </div>
                    </a>
                @endforeach
            @else
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index', ['categories' => [$category->id]]) }}" class="relative group cursor-pointer overflow-hidden border border-outline-variant aspect-square">
                        @php $sample = $category->products->first(); @endphp
                        @if ($sample)
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('{{ $sample->cover_image_url }}');"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-on-surface/70 via-transparent to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-surface-container"></div>
                        @endif
                        <div class="absolute bottom-0 left-0 p-6">
                            <h3 class="font-serif text-headline-sm text-white">{{ $category->name }}</h3>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </section>
@endif
