<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <style>
        {{--
            Copie conforme de resources/views/cards/member-card.blade.php
            (l'aperçu écran à l'adresse /admin/members/{id}/card), converti
            de classes Tailwind vers du CSS explicite en points : 1px = 0.75pt
            (96dpi écran -> 72dpi PDF). mPDF ne supporte pas flexbox de façon
            fiable, donc les lignes "flex" du gabarit écran sont ici des
            tableaux, mais tailles, couleurs et espacements sont identiques.
        --}}
        @page { margin: 0; }
        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            background: #ffffff;
            color: #1f2937;
        }
        {{--
            La carte est un gabarit graphique fixe (photo/matricule/QR/
            cachet toujours au même endroit), pas un bloc de texte : sans
            dir="ltr" explicite ici, mPDF inverse l'ordre des colonnes de ses
            tableaux internes en arabe (SetDirectionality('rtl') côté
            contrôleur), ce qui déplaçait la photo, le QR et le cachet de
            l'autre côté de la carte selon la langue. Seuls les libellés
            restent traduits ; la mise en page reste identique fr/ar.
        --}}
        .card {
            width: {{ $cardWidth }}pt;
            border: 1.5pt solid #1b5e3a;
            border-radius: 12pt;
            box-sizing: border-box;
            overflow: hidden;
            direction: ltr;
        }
        .header {
            border-bottom: 1.5pt solid #1b5e3a;
            padding: 12pt;
        }
        {{--
            L'emblème est déjà découpé en cercle (alpha transparent hors du
            disque, cf. MembershipCardController::circularMask()) : la bordure
            ronde ici n'est qu'une garniture décorative, elle ne dépend plus
            du support incertain de border-radius+overflow sur mPDF pour que
            le logo reste visible et rond.
        --}}
        .header img { height: 34pt; width: 34pt; border-radius: 50%; border: 0.75pt solid #e5e7eb; vertical-align: middle; background: #ffffff; }
        .header .brand {
            display: inline-block;
            vertical-align: middle;
            padding-left: 9pt;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1b5e3a;
        }
        .body { padding: 12pt; }
        .photo {
            width: 60pt;
            height: 72pt;
            border: 0.75pt solid #e5e7eb;
            border-radius: 6pt;
            background: #f7f8f6;
            text-align: center;
            display: table-cell;
            vertical-align: middle;
            font-size: 7pt;
            color: #6b7280;
        }
        .photo img { width: 60pt; height: 72pt; object-fit: cover; border-radius: 6pt; }
        .info { padding-left: 12pt; display: table-cell; vertical-align: top; width: {{ $cardWidth - 12 - 60 - 24 }}pt; }
        .member-label { font-size: 13.5pt; font-weight: bold; color: #1b5e3a; text-transform: uppercase; letter-spacing: 0.4pt; }
        .name {
            font-size: 10.5pt;
            font-weight: 600;
            color: #1f2937;
            margin-top: 1.5pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .matricule-value { font-size: 15pt; font-weight: bold; color: #1b5e3a; letter-spacing: 0.6pt; margin-top: 9pt; }
        .footer {
            border-top: 1.5pt solid #1b5e3a;
            padding: 12pt;
        }
        .footer table { width: 100%; }
        .footer .qr { text-align: left; }
        .footer .qr img { display: block; }
        .stamp-col { text-align: right; vertical-align: bottom; }
        {{--
            stamp-image est déjà un disque semi-transparent avec coins alpha
            (cf. MembershipCardController::stampDataUri()) : pas de bordure ni
            de fond ici, pour garder l'effet "tampon encré posé sur la carte"
            plutôt qu'un badge encadré comme le logo de l'en-tête.
        --}}
        .stamp-image { width: 42pt; height: 42pt; display: inline-block; }
        .stamp-label { display: block; margin-top: 3pt; font-size: 7.5pt; color: #6b7280; }
    </style>
</head>
<body>
    @php $profile = $membership->user->profile; @endphp
    <div class="card" dir="ltr">
        <div class="header">
            @if ($logoDataUri)
                <img src="{{ $logoDataUri }}" alt="logo">
            @endif
            <span class="brand">{{ __('messages.platform_name') }}</span>
        </div>

        <div class="body">
            <table style="width: 100%;">
                <tr>
                    <td class="photo">
                        @if ($photoDataUri)
                            <img src="{{ $photoDataUri }}" alt="photo">
                        @else
                            {{ __('card.title') }}
                        @endif
                    </td>
                    <td class="info">
                        <div class="member-label">{{ $isAmbassador ? __('card.ambassador_label') : __('card.member_label') }}</div>
                        <div class="name">{{ $profile?->full_name ?? $membership->user->name }}</div>
                        <div class="matricule-value">{{ $membership->member_number }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <table>
                <tr>
                    <td class="qr" style="width: {{ $qrSize + 4 }}pt;">
                        <img
                            src="{{ $qrDataUri }}" alt="QR"
                            width="{{ $qrSize }}" height="{{ $qrSize }}"
                            style="width: {{ $qrSize }}pt; height: {{ $qrSize }}pt;"
                        >
                    </td>
                    <td class="stamp-col">
                        @if ($stampDataUri)
                            <img src="{{ $stampDataUri }}" alt="" class="stamp-image"><br>
                        @endif
                        <span class="stamp-label">{{ __('card.stamp_label') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
