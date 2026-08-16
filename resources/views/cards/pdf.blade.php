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
        .card {
            width: {{ $cardWidth }}pt;
            border: 1.5pt solid #1b5e3a;
            border-radius: 12pt;
            box-sizing: border-box;
            overflow: hidden;
        }
        .header {
            border-bottom: 1.5pt solid #1b5e3a;
            padding: 12pt;
        }
        .header img { height: 30pt; width: 30pt; border-radius: 50%; border: 0.75pt solid #e5e7eb; vertical-align: middle; }
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
        .matricule-label { font-size: 7.5pt; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4pt; margin-top: 9pt; }
        .matricule-value { font-size: 15pt; font-weight: bold; color: #1b5e3a; letter-spacing: 0.6pt; }
        .footer {
            border-top: 1.5pt solid #1b5e3a;
            padding: 12pt;
        }
        .footer table { width: 100%; }
        .footer .qr img { display: block; }
        .sign-col { text-align: right; vertical-align: bottom; }
        .sign-line { border-bottom: 0.75pt solid #6b7280; width: 72pt; height: 18pt; display: inline-block; }
        .sign-image { height: 24pt; width: auto; max-width: 72pt; display: inline-block; }
        .signature-label { display: block; margin-top: 3pt; font-size: 7.5pt; color: #6b7280; }
    </style>
</head>
<body>
    @php $profile = $membership->user->profile; @endphp
    <div class="card">
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
                        <div class="member-label">{{ __('card.member_label') }}</div>
                        <div class="name">{{ $profile?->full_name ?? $membership->user->name }}</div>
                        <div class="matricule-label">{{ __('card.matricule_label') }}</div>
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
                    <td class="sign-col">
                        @if ($signatureDataUri)
                            <img src="{{ $signatureDataUri }}" alt="" class="sign-image"><br>
                        @else
                            <div class="sign-line"></div><br>
                        @endif
                        <span class="signature-label">{{ __('card.signature_label') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
