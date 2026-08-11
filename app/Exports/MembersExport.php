<?php

namespace App\Exports;

use App\Models\Membership;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MembersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $query = Membership::query()->with(['user.profile.region']);

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['region'])) {
            $query->whereHas('user.profile', fn ($q) => $q->where('region_id', $this->filters['region']));
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Matricule',
            'N° inscription',
            'Nom',
            'Prénom',
            'NNI',
            'Téléphone',
            'E-mail',
            'Wilaya',
            'Situation professionnelle',
            'Secteur',
            'Statut',
            'Date d\'inscription',
            'Date de validation',
        ];
    }

    /**
     * @param  Membership  $membership
     */
    public function map($membership): array
    {
        $profile = $membership->user->profile;

        return [
            $membership->member_number,
            $membership->registration_number,
            $profile?->last_name,
            $profile?->first_name,
            $profile?->nni,
            $membership->user->phone,
            $membership->user->email,
            $profile?->region?->getTranslation('name', 'fr'),
            $profile?->employment_status,
            $profile?->employment_sector,
            $membership->status,
            $membership->created_at?->format('d/m/Y'),
            $membership->approved_at?->format('d/m/Y'),
        ];
    }
}
