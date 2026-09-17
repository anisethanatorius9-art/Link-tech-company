<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TenderQuoteController;
use App\Livewire\DocumentVault;
use App\Livewire\AdminSettings;
use App\Livewire\QuotesPage;
use App\Livewire\ReportsPage;
use App\Livewire\TendersPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:admin-login')->name('admin.login.store');
    Route::get('admin/register', [AdminAuthController::class, 'showRegister'])->name('admin.register');
    Route::post('admin/register', [AdminAuthController::class, 'register'])->middleware('throttle:admin-login')->name('admin.register.store');
});

Route::middleware(['auth', 'verified', 'active', 'force.password'])->group(function () {
    Route::post('locale', function (Request $request) {
        $locale = $request->validate([
            'locale' => ['required', 'in:en,sw,zh,fr'],
        ])['locale'];

        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return back();
    })->name('locale.update');

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('my-tenders', TendersPage::class)->name('user.tenders');
    Route::get('quotes/{tender?}', QuotesPage::class)->name('quotes.index');
    Route::get('quotes/{tender}/pdf', [TenderQuoteController::class, 'pdf'])->name('quotes.pdf');
    Route::get('quotes/{tender}/excel', [TenderQuoteController::class, 'excel'])->name('quotes.excel');
    Route::get('document-vault', DocumentVault::class)->name('vault.index');
    Route::get('reports', ReportsPage::class)->name('user.reports');
    Route::view('my-quotations', 'customer-offers')->name('customer.offers');
    Route::middleware('admin')->group(function () {
        Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::view('admin/users', 'admin.users')->name('admin.users');
        Route::view('admin/quotes', 'admin.quotes')->name('admin.quotes');
        Route::view('admin/procurement', 'admin.procurement')->name('admin.procurement');
        Route::view('admin/reports', 'admin.reports')->name('admin.reports');
        Route::view('admin/quote-history', 'admin.quote-history')->name('admin.quote-history');
        Route::get('admin/settings', AdminSettings::class)->name('admin.settings');
        Route::redirect('admin/settings/company', 'admin/settings')->name('admin.company.settings');
        Route::view('admin/pos/terminal', 'pos.terminal')->name('admin.pos.terminal');
        Route::get('admin/exports/requests', [AdminDashboardController::class, 'exportRequests'])->name('admin.exports.requests');
        Route::get('admin/exports/requests.pdf', [AdminDashboardController::class, 'exportRequestsPdf'])->name('admin.exports.requests.pdf');
        Route::get('admin/exports/requests.xlsx', [AdminDashboardController::class, 'exportRequestsExcel'])->name('admin.exports.requests.xlsx');
    });
    Route::view('supplier-orders', 'procurement.supplier-orders')->name('supplier-orders');
    Route::view('staff-shifts', 'procurement.staff-shifts')->name('staff-shifts');
    Route::view('inventory-restock', 'procurement.inventory-restock')->name('inventory-restock');
    Route::view('translation', 'procurement.translation')->name('translation');
    Route::view('special-page', 'special-page')->name('special.page');
});

require __DIR__.'/settings.php';
