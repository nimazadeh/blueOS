@props([
    'services' => [],   // Presentation data from config/blue.php (real offerings).
])

<section class="section" id="services" aria-labelledby="services-title">
    <div class="container">
        <x-ui.section-heading
            align="center"
            :eyebrow="__('blue.services.eyebrow')"
            :title="__('blue.services.title')"
            :description="__('blue.services.description')"
        />

        <div class="service-grid">
            @foreach ($services as $service)
                <x-services.service-card
                    :icon="$service['icon'] ?? '◆'"
                    :title="$service['title']"
                    :description="$service['description']"
                />
            @endforeach
        </div>
    </div>
</section>
