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
            border: 2pt solid #1b5e3a;
            border-radius: 12pt;
            box-sizing: border-box;
        }
        .header {
            border-bottom: 2pt solid #1b5e3a;
            padding: 8pt 10pt;
        }
        .header img { height: 26pt; width: 26pt; border-radius: 50%; vertical-align: middle; }
        .header .brand {
            display: inline-block;
            vertical-align: middle;
            margin-left: 8pt;
            font-size: 9pt;
            font-weight: bold;
            color: #1b5e3a;
            text-transform: uppercase;
        }
        .body { padding: 8pt 10pt; }
        .photo {
            width: 60pt;
            height: 74pt;
            border: 1pt solid #e5e7eb;
            border-radius: 6pt;
            background: #f7f8f6;
            text-align: center;
            display: table-cell;
            vertical-align: middle;
            font-size: 7pt;
            color: #6b7280;
        }
        .photo img { width: 60pt; height: 74pt; object-fit: cover; border-radius: 6pt; }
        .info { padding-left: 10pt; display: table-cell; vertical-align: top; width: 180pt; }
        .member-label { font-size: 13pt; font-weight: bold; color: #1b5e3a; text-transform: uppercase; letter-spacing: 1pt; }
        .name {
            font-size: 9pt;
            font-weight: bold;
            color: #1f2937;
            margin-top: 2pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .matricule-label { font-size: 6.5pt; color: #6b7280; text-transform: uppercase; margin-top: 8pt; }
        .matricule-value { font-size: 14pt; font-weight: bold; color: #1b5e3a; letter-spacing: 1pt; }
        .footer {
            border-top: 2pt solid #1b5e3a;
            padding: 6pt 10pt;
        }
        .footer table { width: 100%; }
        .footer .qr img { height: 44pt; width: 44pt; }
        .footer .sign { text-align: right; font-size: 6.5pt; color: #6b7280; }
        .sign-line { border-bottom: 1pt solid #6b7280; width: 70pt; height: 14pt; display: inline-block; }
        .sign-image { height: 22pt; width: auto; max-width: 80pt; display: inline-block; }
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
                    <td class="qr"><img src="{{ $qrDataUri }}" alt="QR"></td>
                    <td class="sign">
                        @if ($signatureDataUri)
                            <img src="{{ $signatureDataUri }}" alt="" class="sign-image"><br>
                        @else
                            <div class="sign-line"></div><br>
                        @endif
                        {{ __('card.signature_label') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
