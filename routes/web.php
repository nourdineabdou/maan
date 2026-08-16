<?php

use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminCommuneController;
use App\Http\Controllers\Admin\AdminDeclarationController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Admin\AdminMemberController;
use App\Http\Controllers\Admin\AdminMembershipController;
use App\Http\Controllers\Admin\AdminMoughataaController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminProblematicController;
use App\Http\Controllers\Admin\AdminRegionController;
use App\Http\Controllers\Admin\AdminSupportMessageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Member\MemberDeclarationController;
use App\Http\Controllers\Member\MemberDocumentController;
use App\Http\Controllers\Member\MemberProblematicController;
use App\Http\Controllers\Member\MyMembershipController;
use App\Http\Controllers\Member\PersonalInfoController;
use App\Http\Controllers\Member\PopulationNeedController;
use App\Http\Controllers\Member\ProfessionalInfoController;
use App\Http\Controllers\Member\ProfileOverviewController;
use App\Http\Controllers\MembershipCardController;
use App\Http\Controllers\MembershipVerificationController;
use App\Http\Controllers\MemberMessageController;
use App\Http\Controllers\MemberNotificationController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/membership/verify/{token}', [MembershipVerificationController::class, 'show'])
    ->name('membership.verify');

// Données de référence publiques (aucune information sensible ni propre à un
// utilisateur) : nécessaires dès la page d'inscription, donc accessibles
// avant authentification, contrairement au reste de l'application.
Route::get('/geo/moughataas', [GeoController::class, 'moughataas'])->name('geo.moughataas');
Route::get('/geo/communes', [GeoController::class, 'communes'])->name('geo.communes');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/card', [MembershipCardController::class, 'show'])->name('card.show');
    Route::get('/card/pdf', [MembershipCardController::class, 'download'])->name('card.download');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

    Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('push.unsubscribe');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileOverviewController::class, 'show'])->name('overview');

        Route::get('/personal', [PersonalInfoController::class, 'edit'])->name('personal.edit');
        Route::put('/personal', [PersonalInfoController::class, 'update'])->name('personal.update');
        Route::post('/photo', [PersonalInfoController::class, 'updatePhoto'])->name('photo.update');

        Route::get('/professional', [ProfessionalInfoController::class, 'edit'])->name('professional.edit');
        Route::put('/professional', [ProfessionalInfoController::class, 'update'])->name('professional.update');

        Route::get('/problematics', [MemberProblematicController::class, 'edit'])->name('problematics.edit');
        Route::put('/problematics', [MemberProblematicController::class, 'update'])->name('problematics.update');

        Route::get('/need', [PopulationNeedController::class, 'index'])->name('need.edit');
        Route::post('/need', [PopulationNeedController::class, 'store'])->name('need.store');

        Route::get('/declarations/needs', [MemberDeclarationController::class, 'needs'])->name('declarations.needs');
        Route::get('/declarations/problematics', [MemberDeclarationController::class, 'problematics'])->name('declarations.problematics');

        Route::get('/documents', [MemberDocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [MemberDocumentController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{document}', [MemberDocumentController::class, 'destroy'])->name('documents.destroy');

        Route::get('/membership', [MyMembershipController::class, 'show'])->name('membership');
        Route::post('/membership/submit', [MyMembershipController::class, 'submit'])->name('membership.submit');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [MemberNotificationController::class, 'index'])->name('index');
        Route::get('/{recipient}', [MemberNotificationController::class, 'show'])->name('show');
        Route::post('/mark-all-read', [MemberNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [MemberMessageController::class, 'index'])->name('index');
        Route::get('/create', [MemberMessageController::class, 'create'])->name('create');
        Route::post('/', [MemberMessageController::class, 'store'])->name('store');
        Route::get('/{message}', [MemberMessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [MemberMessageController::class, 'reply'])->name('reply');
    });

    Route::middleware('role:administrateur')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/members/{membership}/card', [MembershipCardController::class, 'showForAdmin'])
                ->name('members.card');
            Route::get('/members/{membership}/card/pdf', [MembershipCardController::class, 'downloadForAdmin'])
                ->name('members.card.pdf');

            Route::get('/members', [AdminMemberController::class, 'index'])->name('members.index');

            Route::get('/memberships', [AdminMembershipController::class, 'index'])
                ->name('memberships.index');
            Route::get('/memberships/{membership}', [AdminMembershipController::class, 'show'])
                ->name('memberships.show');
            Route::post('/memberships/{membership}/approve', [AdminMembershipController::class, 'approve'])
                ->name('memberships.approve');
            Route::post('/memberships/{membership}/reject', [AdminMembershipController::class, 'reject'])
                ->name('memberships.reject');
            Route::post('/memberships/{membership}/problematics/{problematic}/status', [AdminMembershipController::class, 'updateProblematicStatus'])
                ->name('memberships.problematics.status');
            Route::post('/memberships/{membership}/needs/{need}/status', [AdminMembershipController::class, 'updateNeedStatus'])
                ->name('memberships.needs.status');
            Route::get('/memberships/{membership}/documents/zip', [AdminDocumentController::class, 'downloadZip'])
                ->name('memberships.documents.zip');
            Route::get('/memberships/{membership}/needs/{need}/documents/zip', [AdminDocumentController::class, 'downloadNeedZip'])
                ->name('memberships.needs.documents.zip');
            Route::get('/memberships/{membership}/problematics/{problematic}/documents/zip', [AdminDocumentController::class, 'downloadProblematicZip'])
                ->name('memberships.problematics.documents.zip');
            Route::post('/memberships/{membership}/contact', [AdminSupportMessageController::class, 'store'])
                ->name('memberships.contact');

            Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
            Route::get('/documents/{document}/download', [AdminDocumentController::class, 'download'])->name('documents.download');
            Route::post('/documents/{document}/verify', [AdminDocumentController::class, 'verify'])->name('documents.verify');

            Route::get('/regions', [AdminRegionController::class, 'index'])->name('regions.index');
            Route::get('/regions/{region}/edit', [AdminRegionController::class, 'edit'])->name('regions.edit');
            Route::put('/regions/{region}', [AdminRegionController::class, 'update'])->name('regions.update');
            Route::get('/regions/{region}/moughataas', [AdminMoughataaController::class, 'index'])->name('regions.moughataas');

            Route::get('/moughataas/{moughataa}/edit', [AdminMoughataaController::class, 'edit'])->name('moughataas.edit');
            Route::put('/moughataas/{moughataa}', [AdminMoughataaController::class, 'update'])->name('moughataas.update');
            Route::get('/moughataas/{moughataa}/communes', [AdminCommuneController::class, 'index'])->name('moughataas.communes');
            Route::post('/moughataas/{moughataa}/communes', [AdminCommuneController::class, 'store'])->name('moughataas.communes.store');

            Route::get('/communes/{commune}/edit', [AdminCommuneController::class, 'edit'])->name('communes.edit');
            Route::put('/communes/{commune}', [AdminCommuneController::class, 'update'])->name('communes.update');
            Route::delete('/communes/{commune}', [AdminCommuneController::class, 'destroy'])->name('communes.destroy');

            Route::get('/declarations/needs', [AdminDeclarationController::class, 'needs'])->name('declarations.needs');
            Route::get('/declarations/problematics', [AdminDeclarationController::class, 'problematics'])->name('declarations.problematics');

            Route::get('/problematics', [AdminProblematicController::class, 'index'])->name('problematics.index');
            Route::get('/problematics/create', [AdminProblematicController::class, 'create'])->name('problematics.create');
            Route::post('/problematics', [AdminProblematicController::class, 'store'])->name('problematics.store');
            Route::get('/problematics/{problematic}/edit', [AdminProblematicController::class, 'edit'])->name('problematics.edit');
            Route::put('/problematics/{problematic}', [AdminProblematicController::class, 'update'])->name('problematics.update');
            Route::delete('/problematics/{problematic}', [AdminProblematicController::class, 'destroy'])->name('problematics.destroy');

            Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
            Route::get('/notifications/create', [AdminNotificationController::class, 'create'])->name('notifications.create');
            Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');

            Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
            Route::get('/announcements/create', [AdminAnnouncementController::class, 'create'])->name('announcements.create');
            Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
            Route::get('/announcements/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('announcements.edit');
            Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])->name('announcements.update');
            Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');
            Route::delete('/announcements/{announcement}/images/{image}', [AdminAnnouncementController::class, 'destroyImage'])->name('announcements.images.destroy');

            Route::get('/exports', [AdminExportController::class, 'index'])->name('exports.index');
            Route::get('/exports/excel', [AdminExportController::class, 'excel'])->name('exports.excel');
            Route::get('/exports/pdf', [AdminExportController::class, 'pdf'])->name('exports.pdf');

            Route::get('/support', [AdminSupportMessageController::class, 'index'])->name('support.index');
            Route::get('/support/{message}', [AdminSupportMessageController::class, 'show'])->name('support.show');
            Route::post('/support/{message}/reply', [AdminSupportMessageController::class, 'reply'])->name('support.reply');
            Route::post('/support/{message}/close', [AdminSupportMessageController::class, 'close'])->name('support.close');
        });
});

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));
