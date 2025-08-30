<?php

use App\Livewire\Admin\Admissions\Create as AdmissionsCreate;
use App\Livewire\Admin\Admissions\Edit as AdmissionEdit;
use App\Livewire\Admin\Admissions\Index as AdmissionsIndex;
use App\Livewire\Admin\Admissions\Show as AdmissionShow;
use App\Livewire\Admin\Batches\Index as BatchesIndex;
use App\Livewire\Admin\Courses\Index as CoursesIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Exams\Create as ExamsCreate;
use App\Livewire\Admin\Exams\Edit;
use App\Livewire\Admin\Exams\Index;
use App\Livewire\Admin\Exams\Show;
use App\Livewire\Admin\Exams\Student\Create;
use App\Livewire\Admin\Exams\Student\MarksForm;
use App\Livewire\Admin\Payments\Create as PaymentsCreate;
use App\Livewire\Admin\Payments\DuePayments;
use App\Livewire\Admin\Payments\Index as PaymentsIndex;
use App\Livewire\Admin\Payments\Receipts as PaymentReceipt;
use App\Livewire\Admin\Students\Index as StudentsIndex;
use App\Livewire\Admin\Students\StudentProfile;
use App\Livewire\Admin\Teachers\Create as TeachersCreate;
use App\Livewire\Admin\Teachers\Index as TeachersIndex;
use App\Livewire\Auth\AdminPinLogin;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\SetPinForDevice;
use App\Livewire\Public\LandingPage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('admin.dashboard');
Route::get('/home', LandingPage::class)->name('public.home');
// Students
Route::get('/students', StudentsIndex::class)->name('admin.students.index');

// Admissions
Route::get('/admissions', AdmissionsIndex::class)->name('admin.admissions.index');
Route::get('/admissions/create', AdmissionsCreate::class)->name('admin.admissions.create');
Route::get('/admissions/{admission}/edit', AdmissionEdit::class)->name('admin.admissions.edit');
Route::get('/admin/show-admissions/{admission}', AdmissionShow::class)->name('admin.admissions.show');

// Payments
Route::get('/payments', PaymentsIndex::class)->name('admin.payments.index');
Route::get('/payments/create/{id?}', PaymentsCreate::class)->name('admin.payments.create');
Route::get('/due-payments', DuePayments::class)->name('admin.due-payments.index');
Route::get('/payments/{transaction}/receipt', PaymentReceipt::class)->name('admin.payments.receipt');

// Courses & Batches
Route::get('/courses', CoursesIndex::class)->name('admin.courses.index');
Route::get('/courses/create', \App\Livewire\Admin\Courses\Create::class)->name('admin.courses.create');
Route::get('/courses/{id}/edit', \App\Livewire\Admin\Courses\Edit::class)->name('admin.courses.edit');
Route::get('/courses/{id}/view', \App\Livewire\Admin\Courses\View::class)->name('admin.courses.view');

Route::get('/attendance', \App\Livewire\Admin\Attendance\Index::class)->name('admin.attendance.index');
Route::get('/attendance/create', \App\Livewire\Admin\Attendance\Create::class)->name('admin.attendance.create');
Route::get('/attendance/{date}/view', \App\Livewire\Admin\Attendance\View::class)->name('admin.attendance.view');
// Route::get('/attendance/{id}/edit', \App\Livewire\Admin\Attendance\Edit::class)->name('admin.attendance.edit');

Route::get('/subjects', \App\Livewire\Admin\Subjects\Index::class)->name('admin.subjects.index');
Route::get('/subjects/create', \App\Livewire\Admin\Subjects\Create::class)->name('admin.subjects.create');
Route::get('/subjects/{id}/edit', \App\Livewire\Admin\Subjects\Edit::class)->name('admin.subjects.edit');
Route::get('/subjects/{id}/view', \App\Livewire\Admin\Subjects\View::class)->name('admin.subjects.view');

Route::get('/batches/create', \App\Livewire\Admin\Batches\Create::class)->name('admin.batches.create');
Route::get('/batches/{batch}/edit', \App\Livewire\Admin\Batches\Edit::class)->name('admin.batches.edit');
Route::get('/batches', BatchesIndex::class)->name('admin.batches.index');

// teachers
Route::get('/teachers', TeachersIndex::class)->name('admin.teachers.index');
Route::get('/teachers/create', TeachersCreate::class)->name('admin.teachers.create');

//students
Route::get('/students/{id}', StudentProfile::class)->name('student.profile');

//Exams
Route::get('/exams', Index::class)->name('admin.exams.index');
Route::get('exams/show/{examid}', Show::class)->name('admin.exams.show');
Route::get('/admin/exams/{exam_id}/students/create', Create::class)->name('admin.students.create');
Route::get('/admin/exams/create', ExamsCreate::class)->name('admin.exams.create');
Route::get('/admin/exams/{exam}/edit', Edit::class)->name('admin.exams.edit');
Route::get('/admin/exams/marking/{exam_id}/{student_id}', MarksForm::class)->name('admin.exams.marking');
// Auth
Route::get('/login', Login::class)->name('login');
// Route::get('/login', Login::class)->name('logout');
Route::get('/register', Register::class)->name('register');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', Login::class)->name('admin.login');
    Route::get('/admin/pin', AdminPinLogin::class)->name('admin.pin');          // shown if device is recognized
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/set-pin', SetPinForDevice::class)->name('admin.setpin'); // after first password login
    Route::post('/admin/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        return redirect()->route('admin.login');
    })->name('admin.logout');
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');

    return "All Caches are cleared by @Roni";
});
