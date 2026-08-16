<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MembershipCardController extends Controller
{
    /**
     * Dimensions calquées sur la carte affichée à l'écran (cards/member-card
     * .blade.php) : max-w-sm = 384px, h-16/w-16 pour le QR = 64px. Converties
     * en points (1px = 0.75pt) pour que le PDF soit un copie conforme.
     */
    private const CARD_WIDTH_PT = 288;

    private const QR_SIZE_PT = 48;

    public function show(Request $request): View|RedirectResponse
    {
        return $this->renderCard($request->user()->latestMembership, route('card.download'));
    }

    public function download(Request $request): Response
    {
        return $this->renderPdf($request->user()->latestMembership);
    }

    public function showForAdmin(Request $request, Membership $membership): View|RedirectResponse
    {
        abort_unless($request->user()->can('cards.print'), 403);

        return $this->renderCard($membership, route('admin.members.card.pdf', $membership));
    }

    public function downloadForAdmin(Request $request, Membership $membership): Response
    {
        abort_unless($request->user()->can('cards.print'), 403);

        return $this->renderPdf($membership);
    }

    public function showJson(Request $request): JsonResponse
    {
        return $this->cardJson($request->user()->latestMembership);
    }

    public function showJsonForAdmin(Request $request, Membership $membership): JsonResponse
    {
        abort_unless($request->user()->can('cards.print'), 403);

        return $this->cardJson($membership);
    }

    private function cardJson(?Membership $membership): JsonResponse
    {
        if (! $this->isCardAvailable($membership)) {
            return response()->json(['message' => __('card.not_available')], 404);
        }

        $membership->load('user.profile.region');

        return response()->json([
            'data' => [
                'member_number' => $membership->member_number,
                'full_name' => $membership->user->profile?->full_name ?? $membership->user->display_name,
                'photo_url' => $membership->user->profile?->photo_url,
                'card_generated_at' => $membership->card_generated_at,
                'verify_url' => route('membership.verify', $membership->qr_token),
                'download_pdf_url' => $membership->user_id === request()->user()?->id
                    ? route('api.me.card.pdf')
                    : route('api.admin.memberships.card.pdf', $membership),
            ],
        ]);
    }

    private function renderCard(?Membership $membership, string $downloadUrl): View|RedirectResponse
    {
        if (! $this->isCardAvailable($membership)) {
            return redirect()
                ->route('dashboard')
                ->with('status', __('card.not_available'));
        }

        return view('cards.member-card', [
            'membership' => $membership->load('user.profile.region'),
            'qrDataUri' => $this->qrDataUri($membership),
            'downloadUrl' => $downloadUrl,
        ]);
    }

    /**
     * Générée avec mPDF plutôt que dompdf : contrairement à dompdf, mPDF
     * gère nativement la mise en forme du texte arabe (lettres liées selon
     * leur position, ordre de lecture RTL) sans quoi les caractères arabes
     * s'affichaient déconnectés et dans le désordre sur la carte en PDF.
     */
    private function renderPdf(?Membership $membership): Response
    {
        if (! $this->isCardAvailable($membership)) {
            abort(404);
        }

        $html = view('cards.pdf', [
            'membership' => $membership->load('user.profile.region'),
            'qrDataUri' => $this->qrDataUri($membership),
            'qrSize' => self::QR_SIZE_PT,
            'cardWidth' => self::CARD_WIDTH_PT,
            'logoDataUri' => $this->logoDataUri(),
            'photoDataUri' => $this->photoDataUri($membership),
            'signatureDataUri' => $this->signatureDataUri(),
        ])->render();

        $mpdf = new Mpdf([
            'format' => [$this->ptToMm(self::CARD_WIDTH_PT), $this->ptToMm(244)],
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            // Sélectionne automatiquement une police adaptée à chaque script
            // détecté (latin, arabe...) plutôt qu'une seule police pour tout
            // le document.
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $mpdf->WriteHTML($html);

        $filename = 'carte-membre-'.$membership->member_number.'.pdf';

        return response(
            $mpdf->Output($filename, Destination::STRING_RETURN),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        );
    }

    private function ptToMm(float $points): float
    {
        return $points * 25.4 / 72;
    }

    private function isCardAvailable(?Membership $membership): bool
    {
        return $membership
            && $membership->status === 'approved'
            && $membership->member_number
            && $membership->card_is_active;
    }

    /**
     * Généré directement à la taille d'affichage finale (contrairement à
     * un gros SVG redimensionné en CSS) : mPDF se base sur les attributs
     * width/height intrinsèques du SVG plutôt que sur le CSS, un QR généré
     * à 160px s'affichait donc bien plus grand que prévu sur la carte.
     */
    private function qrDataUri(Membership $membership): string
    {
        $url = route('membership.verify', $membership->qr_token);

        $svg = QrCode::size(self::QR_SIZE_PT)->format('svg')->generate($url);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function logoDataUri(): string
    {
        $filename = app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png';
        $path = public_path($filename);

        if (! is_file($path)) {
            return '';
        }

        return $this->resizedImageDataUri(file_get_contents($path), 120);
    }

    private function signatureDataUri(): ?string
    {
        $path = public_path('signature.png');

        if (! is_file($path)) {
            return null;
        }

        return $this->resizedImageDataUri(file_get_contents($path), 200);
    }

    private function photoDataUri(Membership $membership): ?string
    {
        $photoPath = $membership->user->profile?->photo_path;

        if (! $photoPath || ! Storage::disk('public')->exists($photoPath)) {
            return null;
        }

        return $this->resizedImageDataUri(Storage::disk('public')->get($photoPath), 200);
    }

    /**
     * Redimensionne une image avant de l'embarquer en base64 dans le HTML
     * du PDF. Les photos de profil et le logo sont affichés en tout petit
     * sur la carte (quelques dizaines de points) mais peuvent peser
     * plusieurs Mo en taille d'origine ; les embarquer telles quelles fait
     * exploser la taille du HTML généré, ce que mPDF refuse de traiter
     * (« pcre.backtrack_limit » dépassé) au-delà d'une certaine taille.
     */
    private function resizedImageDataUri(string $contents, int $maxDimension): string
    {
        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            return 'data:image/png;base64,'.base64_encode($contents);
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxDimension / max($width, $height));

        if ($scale >= 1) {
            imagedestroy($source);

            return 'data:image/png;base64,'.base64_encode($contents);
        }

        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        ob_start();
        imagepng($resized, null, 6);
        $data = ob_get_clean();
        imagedestroy($resized);

        return 'data:image/png;base64,'.base64_encode($data);
    }
}
