<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1f2937; }
        h1 { font-size: 13pt; color: #1b5e3a; margin-bottom: 4pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10pt; }
        th, td { border: 0.5pt solid #e5e7eb; padding: 4pt 6pt; text-align: left; }
        th { background: #e7f1ec; color: #1b5e3a; }
    </style>
</head>
<body>
    <h1>Ensembles pour la République — Liste des membres</h1>
    <p>Généré le {{ now()->format('d/m/Y H:i') }} — {{ $memberships->count() }} résultat(s)</p>

    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>N° inscription</th>
                <th>Nom et prénom</th>
                <th>Téléphone</th>
                <th>Wilaya</th>
                <th>Statut</th>
                <th>Date d'inscription</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($memberships as $membership)
                @php $profile = $membership->user->profile; @endphp
                <tr>
                    <td>{{ $membership->member_number ?? '-' }}</td>
                    <td>{{ $membership->registration_number }}</td>
                    <td>{{ $profile?->full_name ?? $membership->user->name }}</td>
                    <td>{{ $membership->user->phone }}</td>
                    <td>{{ $profile?->region?->getTranslation('name', 'fr') ?? '-' }}</td>
                    <td>{{ $membership->status }}</td>
                    <td>{{ $membership->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
