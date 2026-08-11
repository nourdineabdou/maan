<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MatriculeGeneratorService
{
    /**
     * Génère un matricule unique au format ER-{année}-{6 chiffres}.
     *
     * L'incrément est effectué de façon atomique au niveau de la base de
     * données (INSERT ... ON DUPLICATE KEY UPDATE), ce qui évite les
     * doublons même si plusieurs administrateurs valident des adhésions au
     * même moment.
     */
    public function generate(?int $year = null): string
    {
        $year ??= (int) now()->year;

        return DB::transaction(function () use ($year) {
            DB::statement(
                'INSERT INTO matricule_sequences (year, last_number) VALUES (?, 1)
                 ON DUPLICATE KEY UPDATE last_number = last_number + 1',
                [$year]
            );

            $lastNumber = DB::table('matricule_sequences')
                ->where('year', $year)
                ->value('last_number');

            return sprintf('ER-%d-%06d', $year, $lastNumber);
        });
    }
}
