<?php

namespace App\Services;

use App\Models\MemberProfile;
use App\Models\Region;
use App\Models\User;

class PublicStatisticsService
{
    /**
     * Statistiques publiques affichées sur la page de connexion.
     *
     * @return array{
     *     total_members: int,
     *     by_region: array<int, array{name: string, total: int}>,
     *     sector_private: int,
     *     sector_public: int,
     *     sector_other: int,
     *     unemployed: int,
     * }
     */
    public function summary(): array
    {
        $totalMembers = User::role('membre')->count();

        $byRegion = MemberProfile::selectRaw('region_id, count(*) as total')
            ->whereNotNull('region_id')
            ->groupBy('region_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $region = Region::find($row->region_id);

                return [
                    'name' => $region?->getTranslation('name') ?? '—',
                    'total' => (int) $row->total,
                ];
            })
            ->all();

        $sectorPrivate = MemberProfile::where('employment_status', 'employed')
            ->where('employment_sector', 'private')->count();

        $sectorPublic = MemberProfile::where('employment_status', 'employed')
            ->where('employment_sector', 'public')->count();

        $sectorOther = MemberProfile::where('employment_status', 'employed')
            ->whereNotIn('employment_sector', ['private', 'public'])->count();

        $unemployed = MemberProfile::where('employment_status', 'unemployed')->count();

        return [
            'total_members' => $totalMembers,
            'by_region' => $byRegion,
            'sector_private' => $sectorPrivate,
            'sector_public' => $sectorPublic,
            'sector_other' => $sectorOther,
            'unemployed' => $unemployed,
        ];
    }
}
