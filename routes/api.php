<?php

use App\Http\Controllers\Admin\AdminDocumentController as WebAdminDocumentController;
use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Api\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Api\Admin\CommuneController as AdminCommuneController;
use App\Http\Controllers\Api\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Api\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Api\Admin\MembershipController as AdminMembershipController;
use App\Http\Controllers\Api\Admin\MoughataaController as AdminMoughataaController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\ProblematicController as AdminProblematicController;
use App\Http\Controllers\Api\Admin\RegionController as AdminRegionController;
use App\Http\Controllers\Api\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\Member\AccountController;
use App\Http\Controllers\Api\Member\DocumentController as MemberDocumentController;
use App\Http\Controllers\Api\Member\MembershipController as MemberMembershipController;
use App\Http\Controllers\Api\Member\NotificationController as MemberNotificationController;
use App\Http\Controllers\Api\Member\PopulationNeedController;
use App\Http\Controllers\Api\Member\ProblematicController as MemberProblematicController;
use App\Http\Controllers\Api\Member\ProfileController;
use App\Http\Controllers\Api\Member\SupportController as MemberSupportController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\MembershipCardController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
|
| Couche API REST pour la future application mobile React Native. Les
| contrôleurs Api\* réutilisent au maximum les mêmes Form Requests,
| Services et Models que les contrôleurs Web (voir app/Http/Controllers/
| Member, Admin) ; certains endpoints pointent même directement vers les
| contrôleurs Web existants lorsqu'ils sont déjà agnostiques de la vue
| (GeoController, PushSubscriptionController, téléchargements de fichiers).
|
*/

Route::prefix('v1')->group(function () {

    // Authentification (public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

    // Référentiels publics (aucune information sensible, nécessaires avant
    // authentification, comme côté Web dès la page d'inscription/connexion)
    Route::get('/geo/regions', [PublicController::class, 'regions']);
    Route::get('/geo/moughataas', [GeoController::class, 'moughataas']);
    Route::get('/geo/communes', [GeoController::class, 'communes']);

    Route::get('/public/statistics', [PublicController::class, 'statistics']);
    Route::get('/public/announcement/active', [PublicController::class, 'activeAnnouncement']);
    Route::get('/public/membership/verify/{token}', [PublicController::class, 'verifyMembership']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::patch('/me/locale', [AccountController::class, 'updateLocale']);
        Route::put('/me/password', [AccountController::class, 'updatePassword']);

        Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe']);
        Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);

        Route::get('/me/profile', [ProfileController::class, 'show']);
        Route::put('/me/profile/personal', [ProfileController::class, 'updatePersonal']);
        Route::put('/me/profile/professional', [ProfileController::class, 'updateProfessional']);
        Route::post('/me/profile/photo', [ProfileController::class, 'updatePhoto']);

        Route::get('/me/problematics', [MemberProblematicController::class, 'index']);
        Route::put('/me/problematics', [MemberProblematicController::class, 'update']);

        Route::get('/me/need', [PopulationNeedController::class, 'show']);
        Route::put('/me/need', [PopulationNeedController::class, 'update']);

        Route::get('/me/documents', [MemberDocumentController::class, 'index']);
        Route::post('/me/documents', [MemberDocumentController::class, 'store']);
        Route::delete('/me/documents/{document}', [MemberDocumentController::class, 'destroy']);

        Route::get('/me/membership', [MemberMembershipController::class, 'show']);
        Route::post('/me/membership/submit', [MemberMembershipController::class, 'submit']);

        Route::get('/me/card', [MembershipCardController::class, 'showJson']);
        Route::get('/me/card/pdf', [MembershipCardController::class, 'download'])->name('api.me.card.pdf');

        Route::get('/me/notifications', [MemberNotificationController::class, 'index']);
        Route::get('/me/notifications/{recipient}', [MemberNotificationController::class, 'show']);
        Route::post('/me/notifications/mark-all-read', [MemberNotificationController::class, 'markAllAsRead']);

        Route::get('/me/support', [MemberSupportController::class, 'index']);
        Route::post('/me/support', [MemberSupportController::class, 'store']);
        Route::get('/me/support/{message}', [MemberSupportController::class, 'show']);
        Route::post('/me/support/{message}/reply', [MemberSupportController::class, 'reply']);

        Route::prefix('admin')->group(function () {
            Route::get('/members', [AdminMemberController::class, 'index']);

            Route::get('/memberships', [AdminMembershipController::class, 'index']);
            Route::get('/memberships/{membership}', [AdminMembershipController::class, 'show']);
            Route::post('/memberships/{membership}/approve', [AdminMembershipController::class, 'approve']);
            Route::post('/memberships/{membership}/reject', [AdminMembershipController::class, 'reject']);
            Route::post('/memberships/{membership}/contact', [AdminSupportController::class, 'store']);
            Route::get('/memberships/{membership}/card', [MembershipCardController::class, 'showJsonForAdmin']);
            Route::get('/memberships/{membership}/card/pdf', [MembershipCardController::class, 'downloadForAdmin'])
                ->name('api.admin.memberships.card.pdf');
            Route::get('/memberships/{membership}/documents/zip', [WebAdminDocumentController::class, 'downloadZip']);

            Route::get('/documents', [AdminDocumentController::class, 'index']);
            Route::get('/documents/{document}/download', [AdminDocumentController::class, 'download']);
            Route::post('/documents/{document}/verify', [AdminDocumentController::class, 'verify']);

            Route::get('/regions', [AdminRegionController::class, 'index']);
            Route::get('/regions/{region}', [AdminRegionController::class, 'show']);
            Route::put('/regions/{region}', [AdminRegionController::class, 'update']);
            Route::get('/regions/{region}/moughataas', [AdminMoughataaController::class, 'index']);

            Route::get('/moughataas/{moughataa}', [AdminMoughataaController::class, 'show']);
            Route::put('/moughataas/{moughataa}', [AdminMoughataaController::class, 'update']);
            Route::get('/moughataas/{moughataa}/communes', [AdminCommuneController::class, 'index']);
            Route::post('/moughataas/{moughataa}/communes', [AdminCommuneController::class, 'store']);

            Route::put('/communes/{commune}', [AdminCommuneController::class, 'update']);
            Route::delete('/communes/{commune}', [AdminCommuneController::class, 'destroy']);

            Route::get('/problematics', [AdminProblematicController::class, 'index']);
            Route::post('/problematics', [AdminProblematicController::class, 'store']);
            Route::get('/problematics/{problematic}', [AdminProblematicController::class, 'show']);
            Route::put('/problematics/{problematic}', [AdminProblematicController::class, 'update']);
            Route::delete('/problematics/{problematic}', [AdminProblematicController::class, 'destroy']);

            Route::get('/notifications', [AdminNotificationController::class, 'index']);
            Route::post('/notifications', [AdminNotificationController::class, 'store']);

            Route::get('/announcements', [AdminAnnouncementController::class, 'index']);
            Route::post('/announcements', [AdminAnnouncementController::class, 'store']);
            Route::get('/announcements/{announcement}', [AdminAnnouncementController::class, 'show']);
            Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update']);
            Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy']);
            Route::delete('/announcements/{announcement}/images/{image}', [AdminAnnouncementController::class, 'destroyImage']);

            Route::get('/support', [AdminSupportController::class, 'index']);
            Route::get('/support/{message}', [AdminSupportController::class, 'show']);
            Route::post('/support/{message}/reply', [AdminSupportController::class, 'reply']);
            Route::post('/support/{message}/close', [AdminSupportController::class, 'close']);

            Route::get('/exports/excel', [AdminExportController::class, 'excel']);
            Route::get('/exports/pdf', [AdminExportController::class, 'pdf']);
        });
    });
});
