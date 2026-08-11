@php $problematic = $problematic ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-text">{{ __('problematics.label_code') }}</label>
    <input
        type="text" name="code" value="{{ old('code', $problematic->code ?? '') }}" required
        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
    >
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-text">{{ __('problematics.label_name_fr') }}</label>
        <input
            type="text" name="name_fr" value="{{ old('name_fr', $problematic?->getTranslation('name', 'fr')) }}" required
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >
    </div>

    <div>
        <label class="block text-sm font-medium text-text">{{ __('problematics.label_name_ar') }}</label>
        <input
            type="text" name="name_ar" dir="rtl" value="{{ old('name_ar', $problematic?->getTranslation('name', 'ar')) }}" required
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-text">{{ __('problematics.label_description_fr') }}</label>
        <textarea name="description_fr" rows="2"
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >{{ old('description_fr', $problematic?->getTranslation('description', 'fr')) }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-text">{{ __('problematics.label_description_ar') }}</label>
        <textarea name="description_ar" dir="rtl" rows="2"
            class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >{{ old('description_ar', $problematic?->getTranslation('description', 'ar')) }}</textarea>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-text">{{ __('problematics.label_icon') }}</label>
    <input
        type="text" name="icon" value="{{ old('icon', $problematic->icon ?? '') }}" placeholder="bi-flag"
        class="mt-1 block w-full rounded-lg border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
    >
</div>

<label class="flex items-center gap-2 text-sm text-text">
    <input type="checkbox" name="requires_justification" value="1" @checked(old('requires_justification', $problematic->requires_justification ?? false)) class="rounded border-border text-primary focus:ring-primary">
    {{ __('problematics.label_requires_justification') }}
</label>

@if (isset($problematic))
    <label class="flex items-center gap-2 text-sm text-text">
        <input type="checkbox" name="is_active" value="1" @checked($problematic->is_active) class="rounded border-border text-primary focus:ring-primary">
        {{ __('problematics.label_active') }}
    </label>
@endif
