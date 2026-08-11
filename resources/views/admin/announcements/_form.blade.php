@php $announcement = $announcement ?? null; @endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-text">{{ __('announcements.label_title_fr') }}</label>
        <input
            type="text" name="title_fr" value="{{ old('title_fr', $announcement?->getTranslation('title', 'fr')) }}" required maxlength="255"
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >
    </div>

    <div>
        <label class="block text-sm font-medium text-text">{{ __('announcements.label_title_ar') }}</label>
        <input
            type="text" name="title_ar" dir="rtl" value="{{ old('title_ar', $announcement?->getTranslation('title', 'ar')) }}" required maxlength="255"
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-text">{{ __('announcements.label_message_fr') }}</label>
        <textarea name="message_fr" rows="4" required
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >{{ old('message_fr', $announcement?->getTranslation('message', 'fr')) }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-text">{{ __('announcements.label_message_ar') }}</label>
        <textarea name="message_ar" dir="rtl" rows="4" required
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >{{ old('message_ar', $announcement?->getTranslation('message', 'ar')) }}</textarea>
    </div>
</div>

<label class="mt-4 flex items-center gap-2 text-sm text-text">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $announcement->is_active ?? true)) class="rounded border-border text-primary focus:ring-primary">
    {{ __('announcements.label_active') }}
</label>
<p class="mt-1 text-xs text-muted">{{ __('announcements.active_hint') }}</p>

<div class="mt-4">
    <label class="block text-sm font-medium text-text">{{ __('announcements.label_images') }}</label>
    <p class="mt-1 text-xs text-muted">{{ __('announcements.images_hint') }}</p>

    <div id="images-picker-grid" class="mt-3 flex flex-wrap gap-3">
        <button
            type="button" id="images-picker-trigger"
            class="flex h-[100px] w-[100px] shrink-0 flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-primary/40 bg-primary-light text-primary transition-colors hover:bg-primary/10"
        >
            <i class="bi bi-plus-lg text-xl"></i>
            <span class="text-[11px] font-medium">{{ __('announcements.add_photo') }}</span>
        </button>
    </div>

    <input type="file" id="images-input" name="images[]" accept="image/png,image/jpeg" multiple class="hidden">
</div>

@push('scripts')
    <script>
        (function () {
            const trigger = document.getElementById('images-picker-trigger');
            const input = document.getElementById('images-input');
            const grid = document.getElementById('images-picker-grid');
            let selectedFiles = [];

            function refreshInputFiles() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach((file) => dataTransfer.items.add(file));
                input.files = dataTransfer.files;
            }

            function renderThumbnails() {
                grid.querySelectorAll('[data-pending-thumb]').forEach((el) => el.remove());

                selectedFiles.forEach((file, index) => {
                    const tile = document.createElement('div');
                    tile.className = 'relative h-[100px] w-[100px] shrink-0 overflow-hidden rounded-xl border border-border';
                    tile.dataset.pendingThumb = '';

                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'h-full w-full object-cover';
                    tile.appendChild(img);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'absolute end-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white hover:bg-accent';
                    removeBtn.innerHTML = '<i class="bi bi-x-lg text-[10px]"></i>';
                    removeBtn.addEventListener('click', () => {
                        selectedFiles.splice(index, 1);
                        refreshInputFiles();
                        renderThumbnails();
                    });
                    tile.appendChild(removeBtn);

                    grid.insertBefore(tile, trigger);
                });
            }

            trigger.addEventListener('click', () => input.click());

            input.addEventListener('change', () => {
                selectedFiles = selectedFiles.concat(Array.from(input.files));
                refreshInputFiles();
                renderThumbnails();
            });
        })();
    </script>
@endpush
