@props(['showcaseId' => null])
<section class="py-16 bg-white fade-in-section">
    <div class="container mx-auto px-4">
        <div class="company-cam-container my-8 overflow-hidden">

            <div data-showcase-id="{{ $showcaseId ?? config('services.companycam.showcase_id', '01057770-8ca0-47a5-a1dc-40128a20f85b') }}"
                id="companycam-showcase-root" style="margin-top: -45px;"></div>
        </div>
    </div>
</section>
