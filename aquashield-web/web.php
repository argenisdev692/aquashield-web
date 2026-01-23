<?php

use Illuminate\Support\Facades\Route;


use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FacebookLeadFormController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\ContactSupportController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LanguageController;

// Language switching routes
Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact-support', [ContactSupportController::class, 'showForm'])->name('contact-support');
Route::post('/contact-support', [ContactSupportController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact-support.store');
Route::post('/contact-support/validate', [ContactSupportController::class, 'validateField'])->name('contact-support.validate');

// Google Authentication Routes
Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/google-auth/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->user();
        
        // Check if user exists with the same email address
        $existingUser = User::where('email', $googleUser->email)->first();
        
        if ($existingUser) {
            // If user exists but email isn't verified, set verification to now
            if (!$existingUser->email_verified_at) {
                $existingUser->email_verified_at = now();
                $existingUser->save();
            }
            
            // Log in the existing user
            Auth::login($existingUser);
            return redirect(secure_url('/dashboard'));
        } else {
            // New users are not allowed, only login with existing emails
            return redirect(secure_url('/'))->with('error', 'This email is not registered in our system. Please contact the administrator for access.');
        }
    } catch (\Exception $e) {
        return redirect(secure_url('/'))->with('error', 'Error authenticating with Google: ' . $e->getMessage());
    }
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'throttle:60,1', // 60 requests per minute
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Users Resource Routes (CRUD) - NEW
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('/{uuid}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('restore');
        Route::post('/check-email', [App\Http\Controllers\UserController::class, 'checkEmailExists'])->name('check-email');
        Route::post('/check-phone', [App\Http\Controllers\UserController::class, 'checkPhoneExists'])->name('check-phone');
        Route::post('/check-username', [App\Http\Controllers\UserController::class, 'checkUsernameExists'])->name('check-username');
    });



    // Company Data Resource Routes (CRUD) - Single record mode
    Route::prefix('company-data')->name('company-data.')->group(function () {
        Route::get('/', [App\Http\Controllers\CompanyDataController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\CompanyDataController::class, 'store'])->name('store');
        Route::get('/{uuid}/edit', [App\Http\Controllers\CompanyDataController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\CompanyDataController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\CompanyDataController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\CompanyDataController::class, 'restore'])->name('restore');
        Route::post('/check-email', [App\Http\Controllers\CompanyDataController::class, 'checkEmailExists'])->name('check-email');
        Route::post('/check-phone', [App\Http\Controllers\CompanyDataController::class, 'checkPhoneExists'])->name('check-phone');
    });

    // Service Categories Resource Routes (CRUD) - NEW
    Route::prefix('service-categories')->name('service-categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\ServiceCategoryController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\ServiceCategoryController::class, 'store'])->name('store');
        Route::get('/{uuid}/edit', [App\Http\Controllers\ServiceCategoryController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\ServiceCategoryController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\ServiceCategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\ServiceCategoryController::class, 'restore'])->name('restore');
        Route::post('/check-category', [App\Http\Controllers\ServiceCategoryController::class, 'checkCategoryExists'])->name('check-category');
        
        // TEMPORARY - Test CRUD cache functionality
        Route::get('/test-crud-cache', [App\Http\Controllers\ServiceCategoryController::class, 'testCrudCache'])->name('test-crud-cache');
    });

    // Invoice Management Resource Routes (CRUD) - NEW
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [App\Http\Controllers\InvoiceDemoController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\InvoiceDemoController::class, 'store'])->name('store');
        
        // Export Routes (MUST come before UUID routes to avoid conflicts)
        Route::get('/export/excel', [App\Http\Controllers\InvoiceDemoController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export/pdf', [App\Http\Controllers\InvoiceDemoController::class, 'exportPdf'])->name('export-pdf');
        
        // Utility Routes (non-UUID)
        Route::get('/check-invoice-number', [App\Http\Controllers\InvoiceDemoController::class, 'checkInvoiceNumberExists'])->name('check-invoice-number');
        Route::get('/generate-invoice-number', [App\Http\Controllers\InvoiceDemoController::class, 'generateInvoiceNumber'])->name('generate-invoice-number');
        Route::get('/form-data', [App\Http\Controllers\InvoiceDemoController::class, 'getFormData'])->name('form-data');
        
        // UUID-based Routes (MUST come after specific routes)
        Route::get('/{uuid}/edit', [App\Http\Controllers\InvoiceDemoController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\InvoiceDemoController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\InvoiceDemoController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\InvoiceDemoController::class, 'restore'])->name('restore');
        
        // PDF Routes (UUID-based)
        Route::get('/{uuid}/pdf', [App\Http\Controllers\InvoiceDemoController::class, 'viewPdf'])->name('view-pdf');
        Route::get('/{uuid}/download-pdf', [App\Http\Controllers\InvoiceDemoController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{uuid}/generate-pdf', [App\Http\Controllers\InvoiceDemoController::class, 'generatePdf'])->name('generate-pdf');
        Route::get('/{uuid}/verify-pdf-status', [App\Http\Controllers\InvoiceDemoController::class, 'verifyPdfStatus'])->name('verify-pdf-status');
    });


    // API Routes for Service Categories (needed by Portfolio CRUD)
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/service-categories', [App\Http\Controllers\ServiceCategoryController::class, 'getForApi'])->name('service-categories');
    });

    // Blog Categories Resource Routes (CRUD) - NEW
    Route::prefix('blog-categories')->name('blog-categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\BlogCategoryController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\BlogCategoryController::class, 'store'])->name('store');
        Route::get('/{uuid}/edit', [App\Http\Controllers\BlogCategoryController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\BlogCategoryController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\BlogCategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\BlogCategoryController::class, 'restore'])->name('restore');
        Route::post('/check-category-name', [App\Http\Controllers\BlogCategoryController::class, 'checkCategoryNameExists'])->name('check-category-name');
        
        // TEMPORARY - Test CRUD cache functionality
        Route::get('/test-crud-cache', [App\Http\Controllers\BlogCategoryController::class, 'testCrudCache'])->name('test-crud-cache');
    });

    Route::get('/admin/posts', function () {
        return view('posts');
    })->name('admin.posts');

    // Posts CRUD Resource Routes (NEW - Non-Livewire)
    Route::prefix('posts-crud')->name('posts-crud.')->group(function () {
        Route::get('/', [App\Http\Controllers\PostCrudController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PostCrudController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PostCrudController::class, 'store'])->name('store');
        Route::get('/{uuid}', [App\Http\Controllers\PostCrudController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [App\Http\Controllers\PostCrudController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\PostCrudController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\PostCrudController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\PostCrudController::class, 'restore'])->name('restore');
        Route::post('/check-title', [App\Http\Controllers\PostCrudController::class, 'checkTitleExists'])->name('check-title');
    });

    // Portfolios CRUD Resource Routes (NEW - No Livewire)
    Route::prefix('portfolios')->name('portfolios-crud.')->group(function () {
        Route::get('/', [App\Http\Controllers\PortfolioCrudController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PortfolioCrudController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PortfolioCrudController::class, 'store'])->name('store');
        Route::get('/{uuid}', [App\Http\Controllers\PortfolioCrudController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [App\Http\Controllers\PortfolioCrudController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\PortfolioCrudController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\PortfolioCrudController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\PortfolioCrudController::class, 'restore'])->name('restore');
        Route::post('/check-title', [App\Http\Controllers\PortfolioCrudController::class, 'checkTitleExists'])->name('check-title');
    });

    // Call Records Routes
    Route::get('/call-records', [App\Http\Controllers\CallRecordsController::class, 'index'])->name('call-records');
    Route::get('/api/call-records', [App\Http\Controllers\CallRecordsController::class, 'getCalls'])->name('api.call-records');
    Route::get('/api/call-records/{callId}', [App\Http\Controllers\CallRecordsController::class, 'getCallDetails'])->name('api.call-records.details');
    Route::post('/api/call-records/clear-cache', [App\Http\Controllers\CallRecordsController::class, 'clearCallRecordsCache'])->name('api.call-records.clear-cache');

    // Appointment Resource Routes (CRUD)
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');
        Route::get('/{uuid}', [AppointmentController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [AppointmentController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [AppointmentController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [AppointmentController::class, 'restore'])->name('restore');
        Route::post('/check-email', [AppointmentController::class, 'checkEmailExists'])->name('check-email');
        Route::post('/check-phone', [AppointmentController::class, 'checkPhoneExists'])->name('check-phone');
        Route::post('/send-rejection', [AppointmentController::class, 'sendRejection'])->name('send-rejection');
    });

    // Email Data Resource Routes (CRUD)
    Route::prefix('email-datas')->name('email-datas.')->group(function () {
        Route::get('/', [App\Http\Controllers\EmailDataController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\EmailDataController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\EmailDataController::class, 'store'])->name('store');
        Route::get('/{uuid}', [App\Http\Controllers\EmailDataController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [App\Http\Controllers\EmailDataController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\EmailDataController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\EmailDataController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\EmailDataController::class, 'restore'])->name('restore');
        Route::post('/check-email', [App\Http\Controllers\EmailDataController::class, 'checkEmailExists'])->name('check-email');
        Route::post('/check-phone', [App\Http\Controllers\EmailDataController::class, 'checkPhoneExists'])->name('check-phone');
        
        // TEMPORARY - Testing new CRUD cache functionality
        Route::get('/test-crud-cache', [App\Http\Controllers\EmailDataController::class, 'testCrudCache'])->name('test-crud-cache');
    });

    // Insurance Companies Resource Routes (CRUD) - NEW
    Route::prefix('insurance-companies')->name('insurance-companies.')->group(function () {
        Route::get('/', [App\Http\Controllers\InsuranceCompanyController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\InsuranceCompanyController::class, 'store'])->name('store');
        
        // Export Routes (MUST come before UUID routes to avoid conflicts)
        Route::get('/export/excel', [App\Http\Controllers\InsuranceCompanyController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export/pdf', [App\Http\Controllers\InsuranceCompanyController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/bulk-export', [App\Http\Controllers\InsuranceCompanyController::class, 'bulkExport'])->name('bulk-export');
        
        // UUID-based Routes
        Route::get('/{uuid}/edit', [App\Http\Controllers\InsuranceCompanyController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\InsuranceCompanyController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\InsuranceCompanyController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\InsuranceCompanyController::class, 'restore'])->name('restore');
        
        // Utility Routes
        Route::post('/check-name', [App\Http\Controllers\InsuranceCompanyController::class, 'checkNameExists'])->name('check-name');
        Route::post('/check-email', [App\Http\Controllers\InsuranceCompanyController::class, 'checkEmail'])->name('check-email');
        Route::post('/check-phone', [App\Http\Controllers\InsuranceCompanyController::class, 'checkPhoneExists'])->name('check-phone');
        Route::get('/form-data', [App\Http\Controllers\InsuranceCompanyController::class, 'getFormData'])->name('form-data');
        
        // Test route for date filter functionality
        Route::get('/test-filter', function () {
            return view('insurance-companies.test-filter');
        })->name('test-filter');
    });

    // Model AI Resource Routes (CRUD) - NEW
    Route::prefix('model-ais')->name('model-ais.')->group(function () {
        Route::get('/', [App\Http\Controllers\ModelAIController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ModelAIController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ModelAIController::class, 'store'])->name('store');
        Route::get('/{uuid}', [App\Http\Controllers\ModelAIController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [App\Http\Controllers\ModelAIController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [App\Http\Controllers\ModelAIController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\ModelAIController::class, 'destroy'])->name('destroy');
        Route::patch('/{uuid}/restore', [App\Http\Controllers\ModelAIController::class, 'restore'])->name('restore');
        Route::post('/check-name', [App\Http\Controllers\ModelAIController::class, 'checkNameExists'])->name('check-name');
    });

    // Appointment Calendar Routes
    Route::get('/appointment-calendar', [\App\Http\Controllers\AppointmentCalendarController::class, 'index'])->name('appointment-calendar');
    Route::get('/appointment-calendar/events', [\App\Http\Controllers\AppointmentCalendarController::class, 'events'])->name('appointment-calendar.events');
    Route::get('/appointment-calendar/clients', [\App\Http\Controllers\AppointmentCalendarController::class, 'getClients'])->name('appointment-calendar.clients');
    Route::patch('/appointment-calendar/events/{id}', [\App\Http\Controllers\AppointmentCalendarController::class, 'update'])->name('appointment-calendar.update');
    Route::patch('/appointment-calendar/status/{id}', [\App\Http\Controllers\AppointmentCalendarController::class, 'updateStatus'])->name('appointment-calendar.status');
    Route::post('/appointment-calendar/create', [\App\Http\Controllers\AppointmentCalendarController::class, 'create'])->name('appointment-calendar.create');
    Route::post('/appointment-calendar/create-appointment', [\App\Http\Controllers\AppointmentCalendarController::class, 'createAppointment'])->name('appointment-calendar.createAppointment');
    Route::post('/appointment-calendar/store', [\App\Http\Controllers\AppointmentCalendarController::class, 'store'])->name('appointment-calendar.store');
    Route::post('/appointment-calendar/check-email', [\App\Http\Controllers\AppointmentCalendarController::class, 'checkEmailExists'])->name('appointment-calendar.check-email');
    Route::post('/appointment-calendar/check-phone', [\App\Http\Controllers\AppointmentCalendarController::class, 'checkPhoneExists'])->name('appointment-calendar.check-phone');
    
    // Translation Demo Route
    Route::get('/translation-demo', function () {
        return view('translation-demo');
    })->name('translation-demo');
});

Route::get('/new-roof', function () {
    return view('services.new-roof');
})->name('new-roof');

Route::get('/roof-repair', function () {
    return view('services.roof-repair');
})->name('roof-repair');

Route::get('/storm-damage', function () {
    return view('services.storm-damage');
})->name('storm-damage');

Route::get('/hail-damage', function () {
    return view('services.hail-damage');
})->name('hail-damage');

Route::get('/warranties', function () {
    return view('warranties');
})->name('warranties');

Route::get('/products', function () {
    return view('products');
})->name('products');

Route::get('/financing', function () {
    return view('financing');
})->name('financing');

Route::get('/virtual-remodeler', function () {
    return view('virtual-remodeler');
})->name('virtual-remodeler');

Route::get('/insurance-claims', function () {
    return view('insurance-claims');
})->name('insurance-claims');

Route::get('/portfolio', function () {
    return view('portfolio');
})->name('portfolio');

Route::get('/faqs', function () {
    return view('faqs');
})->name('faqs');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/cookies-policy', function () {
    return view('cookies-policy');
})->name('cookies-policy');

Route::get('/terms-and-conditions', function () {
    return view('terms-and-conditions');
})->name('terms-and-conditions');

// Rutas públicas del blog
Route::get('/blog', [PostController::class, 'showLatestPosts'])->name('blog.index');
Route::get('/blog/search', [PostController::class, 'search'])->name('blog.search');
Route::get('/blog/category/{categorySlug}', [PostController::class, 'showPostsByCategory'])->name('blog.category');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
Route::get('/feed', [FeedController::class, 'rss'])->name('feeds.rss');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/contact', [App\Http\Controllers\ContactSupportController::class, 'showForm'])->name('contact');

// Standalone Facebook Lead Form Routes
Route::get('/facebook-lead-form', [FacebookLeadFormController::class, 'showForm'])->name('facebook.lead.form');
Route::post('/facebook-lead-form/submit', [FacebookLeadFormController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('facebook.lead.store');
Route::post('/facebook-lead-form/validate-field', [FacebookLeadFormController::class, 'validateField'])->name('facebook.lead.validate'); // Route for single field validation
Route::get('/facebook-lead-form/confirmation', [FacebookLeadFormController::class, 'showConfirmation'])->name('facebook.confirmation');

// Field validation routes - No throttling for real-time validation
Route::post('/contact-support/validate', [ContactSupportController::class, 'validateField'])
    ->name('contact-support.validate');
Route::post('/facebook-lead-form/validate-field', [FacebookLeadFormController::class, 'validateField'])
    ->name('facebook.lead.validate');

// Public API routes
Route::middleware(['throttle:api'])->group(function () {
    Route::get('/blog/search', [PostController::class, 'search'])->name('blog.search');
    Route::get('/feed', [FeedController::class, 'rss'])->name('feeds.rss');
});




