<?php

namespace App\Models\Concerns;

trait HasTranslatedAttributes
{
    /**
     * Lit une valeur traduite depuis un champ JSON {"fr": "...", "ar": "..."}
     * en se basant sur la locale active, avec repli sur la locale par défaut
     * puis sur la première valeur disponible.
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $values = $this->{$field};

        if (! is_array($values)) {
            return $values;
        }

        $locale ??= app()->getLocale();
        $default = config('localization.default', 'fr');

        return $values[$locale]
            ?? $values[$default]
            ?? (reset($values) ?: null);
    }

    /**
     * Retourne toutes les traductions disponibles pour un champ JSON.
     */
    public function getTranslations(string $field): array
    {
        return (array) $this->{$field};
    }
}
