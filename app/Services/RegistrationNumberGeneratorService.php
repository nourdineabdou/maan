<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RegistrationNumberGeneratorService
{
    /**
     * Génère un numéro d'inscription unique au format REG-{année}-{6 chiffres},
     * de façon atomique (même logique que MatriculeGeneratorService).
     */
    public function generate(?int $year = null): string
    {
        $year ??= (int) now()->year;

        return DB::transaction(function () use ($year) {
            DB::statement(
                'INSERT INTO registration_sequences (year, last_number) VALUES (?, 1)
                 ON DUPLICATE KEY UPDATE last_number = last_number + 1',
                [$year]
            );

            $lastNumber = DB::table('registration_sequences')
                ->where('year', $year)
                ->value('last_number');

            return sprintf('REG-%d-%06d', $year, $lastNumber);
        });
    }
}
