<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Page publique (non authentifiée) requise par les stores : une URL
 * d'assistance joignable même sans compte ou sans être connecté, distincte
 * de la messagerie interne réservée aux membres (cf. routes/support.*).
 */
class SupportPageController extends Controller
{
    public function show(): View
    {
        return view('support-page.show');
    }
}
