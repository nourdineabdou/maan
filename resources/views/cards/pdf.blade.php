<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            background: #ffffff;
        }
        .card {
            width: 280pt;
            margin: 0;
            border: 1.4pt solid #1b5e3a;
            border-radius: 14pt;
            box-sizing: border-box;
            overflow: hidden;
        }
        .header {
            background: #eaf3ee;
            padding: 11pt 12pt 10pt;
            text-align: center;
            border-bottom: 1.4pt solid #1b5e3a;
        }
        .header img { height: 30pt; width: 30pt; border-radius: 50%; }
        .header .brand {
            display: block;
            margin-top: 6pt;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.6pt;
            color: #123f27;
            text-transform: uppercase;
        }
        .header .brand-rule {
            display: block;
            margin: 5pt auto 0;
            width: 32pt;
            height: 1.6pt;
            background: #f2b705;
        }
        .body { padding: 13pt 12pt; }
        .photo {
            width: 64pt;
            height: 80pt;
            border: 1pt solid #d8ded9;
            border-radius: 8pt;
            background: #f7f8f6;
            text-align: center;
            display: table-cell;
            vertical-align: middle;
            font-size: 6.5pt;
            color: #6b7280;
        }
        .photo img { width: 64pt; height: 80pt; object-fit: cover; border-radius: 8pt; }
        .info { padding-left: 13pt; display: table-cell; vertical-align: middle; width: 175pt; }
        .eyebrow {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.6pt;
            color: #f2b705;
            background: #123f27;
            display: inline-block;
            padding: 2.5pt 7pt;
            border-radius: 8pt;
            text-transform: uppercase;
        }
        .name {
            font-size: 11.5pt;
            font-weight: bold;
            color: #1f2937;
            margin-top: 7pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .matricule-box {
            margin-top: 10pt;
            background: #eaf3ee;
            border-radius: 7pt;
            padding: 6pt 9pt;
        }
        .matricule-label {
            font-size: 6pt;
            color: #4b6b5a;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
        }
        .matricule-value {
            font-size: 14pt;
            font-weight: bold;
            color: #123f27;
            letter-spacing: 1pt;
            margin-top: 1pt;
        }
        .footer {
            border-top: 1pt solid #e5e7eb;
            padding: 10pt 12pt;
        }
        .footer table { width: 100%; }
        .qr-col { width: {{ $qrSize + 4 }}pt; }
        .sign-col { text-align: right; vertical-align: middle; }
        .sign-line { border-bottom: 0.8pt solid #9aa5a0; width: 74pt; height: 16pt; display: inline-block; }
        .sign-image { height: 20pt; width: auto; max-width: 80pt; display: inline-block; }
        .signature-label {
            display: block;
            margin-top: 3pt;
            font-size: 6.3pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
        }
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
            <span class="brand-rule"></span>
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
                        <span class="eyebrow">{{ __('card.member_label') }}</span>
                        <div class="name">{{ $profile?->full_name ?? $membership->user->name }}</div>
                        <div class="matricule-box">
                            <div class="matricule-label">{{ __('card.matricule_label') }}</div>
                            <div class="matricule-value">{{ $membership->member_number }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <table>
                <tr>
                    <td class="qr-col">
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
