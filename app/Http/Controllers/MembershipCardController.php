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
                'is_ambassador' => $membership->user->isAmbassador(),
                'gender' => $membership->user->profile?->gender,
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
            'roleLabel' => $membership->user->roleLabel(),
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
            'stampDataUri' => $this->stampDataUri(),
            'roleLabel' => $membership->user->roleLabel(),
        ])->render();

        $mpdf = new Mpdf([
            'format' => [$this->ptToMm(self::CARD_WIDTH_PT), $this->measureContentHeightMm($html)],
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

    /**
     * mPDF ne propose pas de hauteur de page "automatique" : avec une hauteur
     * fixe devinée à l'avance, un nom un peu long ou une photo/signature qui
     * gonfle une ligne pouvait pousser le bas de la carte sur une 2e page
     * (quasi vide). On mesure donc la hauteur réellement occupée par le
     * rendu sur une page volontairement très haute, pour générer ensuite la
     * vraie carte à une hauteur qui lui correspond exactement.
     */
    private function measureContentHeightMm(string $html): float
    {
        $probe = new Mpdf([
            'format' => [$this->ptToMm(self::CARD_WIDTH_PT), 1000],
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        if (app()->getLocale() === 'ar') {
            $probe->SetDirectionality('rtl');
        }

        $probe->WriteHTML($html);

        return $probe->y + 2;
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

    /**
     * logo_fr.png/logo_ar.png sont le logo institutionnel complet (blason +
     * nom de la plateforme en dessous), pensés pour un usage en pleine
     * largeur — pas pour l'avatar rond de l'en-tête de la carte. Sans ce
     * recadrage, écraser ces ~1254×1254px dans un cercle de 34pt déforme le
     * blason et rend le texte en dessous illisible. On isole donc d'abord le
     * carré contenant le blason avant de le redimensionner.
     */
    private function logoDataUri(): string
    {
        $filename = app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png';
        $path = public_path($filename);

        if (! is_file($path)) {
            return '';
        }

        $emblem = $this->circularMask($this->cropLogoEmblem(file_get_contents($path)));

        return $this->resizedImageDataUri($emblem, 160);
    }

    /**
     * mPDF ne clippe pas correctement border-radius sur un <img> (le carré
     * reste visible autour de l'emblème, rendu flou/peu visible) : on
     * applique donc le masque circulaire directement dans le PNG plutôt que
     * de compter sur le CSS. $opacity (0-1) réduit en plus l'alpha des
     * pixels conservés — utilisé pour le cachet officiel, qui doit se
     * fondre sur la carte comme un vrai tampon plutôt qu'un autocollant opaque.
     */
    private function circularMask(string $contents, float $opacity = 1.0): string
    {
        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            return $contents;
        }

        $size = min(imagesx($source), imagesy($source));
        $radius = $size / 2;

        imagealphablending($source, false);
        imagesavealpha($source, true);
        $transparent = imagecolorallocatealpha($source, 0, 0, 0, 127);

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $dx = $x - $radius;
                $dy = $y - $radius;

                if (($dx * $dx + $dy * $dy) > ($radius * $radius)) {
                    imagesetpixel($source, $x, $y, $transparent);
                } elseif ($opacity < 1.0) {
                    $rgba = imagecolorat($source, $x, $y);
                    $alpha = ($rgba >> 24) & 0x7F;
                    $opaqueness = (127 - $alpha) / 127;
                    $newAlpha = (int) round(127 - ($opaqueness * $opacity * 127));
                    $color = imagecolorallocatealpha($source, ($rgba >> 16) & 0xFF, ($rgba >> 8) & 0xFF, $rgba & 0xFF, $newAlpha);
                    imagesetpixel($source, $x, $y, $color);
                }
            }
        }

        ob_start();
        imagepng($source, null, 6);
        $data = ob_get_clean();
        imagedestroy($source);

        return $data;
    }

    private function cropLogoEmblem(string $contents): string
    {
        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            return $contents;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        // Le blason (couronne de laurier + silhouettes) occupe environ les
        // 65% supérieurs du canevas (le reste étant le nom de la plateforme
        // sous le blason) — ratio vérifié sur logo_fr.png/logo_ar.png, pas
        // une valeur générique.
        $side = (int) round($height * 0.65);
        $x = (int) round(($width - $side) / 2);

        $crop = imagecreatetruecolor($side, $side);
        imagealphablending($crop, false);
        imagesavealpha($crop, true);
        $transparent = imagecolorallocatealpha($crop, 0, 0, 0, 127);
        imagefill($crop, 0, 0, $transparent);
        imagecopy($crop, $source, 0, 0, $x, 0, $side, $side);
        imagedestroy($source);

        ob_start();
        imagepng($crop, null, 6);
        $data = ob_get_clean();
        imagedestroy($crop);

        return $data;
    }

    /**
     * Cachet officiel affiché à l'emplacement historiquement réservé à la
     * signature manuscrite : watermark.png contient déjà le tampon rond
     * (texte fr/ar circulaire) centré dans un canevas plus large, avec une
     * marge blanche autour. On recadre ce disque puis on le rend
     * légèrement translucide (cf. circularMask) pour un rendu de vrai
     * tampon encré plutôt qu'un autocollant plaqué sur la carte.
     */
    private function stampDataUri(): ?string
    {
        $path = public_path('watermark.png');

        if (! is_file($path)) {
            return null;
        }

        $stamp = $this->circularMask($this->cropAndResizeStamp(file_get_contents($path), 240), 0.8);

        return $this->resizedImageDataUri($stamp, 240);
    }

    /**
     * Recadre un carré centré (le tampon occupe ~90% de la plus petite
     * dimension du canevas source, marge blanche vérifiée sur watermark.png)
     * et redimensionne en un seul passage GD — plus rapide qu'un recadrage
     * pleine résolution suivi d'un masque circulaire pixel par pixel.
     */
    private function cropAndResizeStamp(string $contents, int $targetSize): string
    {
        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            return $contents;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $side = (int) round(min($width, $height) * 0.9);
        $x = (int) round(($width - $side) / 2);
        $y = (int) round(($height - $side) / 2);

        $resized = imagecreatetruecolor($targetSize, $targetSize);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);
        imagecopyresampled($resized, $source, 0, 0, $x, $y, $targetSize, $targetSize, $side, $side);
        imagedestroy($source);

        ob_start();
        imagepng($resized, null, 6);
        $data = ob_get_clean();
        imagedestroy($resized);

        return $data;
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
