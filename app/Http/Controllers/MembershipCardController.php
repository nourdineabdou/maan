<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MembershipCardController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        return $this->renderCard($request->user()->latestMembership, route('card.download'));
    }

    public function download(Request $request)
    {
        return $this->renderPdf($request->user()->latestMembership);
    }

    public function showForAdmin(Request $request, Membership $membership): View|RedirectResponse
    {
        abort_unless($request->user()->can('cards.print'), 403);

        return $this->renderCard($membership, route('admin.members.card.pdf', $membership));
    }

    public function downloadForAdmin(Request $request, Membership $membership)
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

    private function renderPdf(?Membership $membership)
    {
        if (! $this->isCardAvailable($membership)) {
            abort(404);
        }

        $pdf = Pdf::loadView('cards.pdf', [
            'membership' => $membership->load('user.profile.region'),
            'qrDataUri' => $this->qrDataUri($membership),
            'logoDataUri' => $this->logoDataUri(),
            'photoDataUri' => $this->photoDataUri($membership),
            'signatureDataUri' => $this->signatureDataUri(),
        ])->setPaper([0, 0, 280, 220]);

        return $pdf->download('carte-membre-'.$membership->member_number.'.pdf');
    }

    private function isCardAvailable(?Membership $membership): bool
    {
        return $membership
            && $membership->status === 'approved'
            && $membership->member_number
            && $membership->card_is_active;
    }

    private function qrDataUri(Membership $membership): string
    {
        $url = route('membership.verify', $membership->qr_token);

        $svg = QrCode::size(160)->format('svg')->generate($url);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function logoDataUri(): string
    {
        $filename = app()->getLocale() === 'ar' ? 'logo_ar.png' : 'logo_fr.png';
        $path = public_path($filename);

        if (! is_file($path)) {
            return '';
        }

        return 'data:image/png;base64,'.base64_encode(file_get_contents($path));
    }

    private function signatureDataUri(): ?string
    {
        $path = public_path('signature.png');

        if (! is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode(file_get_contents($path));
    }

    private function photoDataUri(Membership $membership): ?string
    {
        $photoPath = $membership->user->profile?->photo_path;

        if (! $photoPath || ! Storage::disk('public')->exists($photoPath)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($photoPath) ?: 'image/jpeg';
        $contents = Storage::disk('public')->get($photoPath);

        return "data:{$mime};base64,".base64_encode($contents);
    }
}
