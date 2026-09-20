@if ($announcement)
    <div
        id="announcement-banner"
        data-announcement-id="{{ $announcement->id }}"
        class="relative mb-6 overflow-hidden rounded-2xl bg-linear-to-br from-primary via-primary to-primary-dark p-5 text-white shadow-md sm:p-6"
    >
        <div class="pointer-events-none absolute -end-8 -top-8 h-32 w-32 rounded-full bg-secondary/25 blur-3xl"></div>

        <div class="relative z-10 flex items-start gap-4">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/20">
                <i class="bi bi-megaphone-fill text-lg"></i>
            </span>

            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold uppercase tracking-wide text-secondary">{{ __('announcements.badge_label') }}</p>
                <p class="mt-0.5 text-lg font-bold leading-snug">{{ $announcement->getTranslation('title') }}</p>
                <p class="mt-1 text-sm font-medium leading-snug text-white/95">{{ $announcement->getTranslation('message') }}</p>
            </div>

            <button
                type="button" id="announcement-dismiss"
                class="shrink-0 rounded-full p-1.5 text-white/70 transition-colors hover:bg-white/15 hover:text-white"
                aria-label="{{ __('announcements.dismiss') }}"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        @if ($announcement->images->isNotEmpty())
            <div class="relative z-10 mt-4 flex gap-2.5 overflow-x-auto pb-1">
                @foreach ($announcement->images as $image)
                    <button
                        type="button" data-announcement-image-trigger data-image-src="{{ $image->url }}"
                        class="shrink-0"
                    >
                        <img
                            src="{{ $image->url }}" alt=""
                            class="h-24 w-32 rounded-lg border border-white/20 object-cover shadow-sm transition-transform hover:scale-[1.03] sm:h-28 sm:w-36"
                        >
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <div
        id="announcement-image-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4"
    >
        <div class="relative h-96 w-full max-w-lg">
            <button
                type="button" id="announcement-image-modal-close"
                class="absolute -end-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white text-text shadow-md hover:bg-background"
                aria-label="{{ __('announcements.dismiss') }}"
            >
                <i class="bi bi-x-lg text-sm"></i>
            </button>
            <img id="announcement-image-modal-img" src="" alt="" class="h-full w-full rounded-xl object-contain">
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                var banner = document.getElementById('announcement-banner');
                var dismissKey = 'dismissed-announcement-{{ $announcement->id }}';

                if (! banner) {
                    return;
                }

                if (localStorage.getItem(dismissKey)) {
                    banner.remove();
                    return;
                }

                document.getElementById('announcement-dismiss')?.addEventListener('click', function () {
                    localStorage.setItem(dismissKey, '1');
                    banner.remove();
                });

                var modal = document.getElementById('announcement-image-modal');
                var modalImg = document.getElementById('announcement-image-modal-img');

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modalImg.src = '';
                }

                document.querySelectorAll('[data-announcement-image-trigger]').forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        modalImg.src = trigger.dataset.imageSrc;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                });

                document.getElementById('announcement-image-modal-close')?.addEventListener('click', closeModal);

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });
            })();
        </script>
    @endpush
@endif
