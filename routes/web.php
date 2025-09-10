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
use App\Livewire\Admin\Exams\Student\MarksDetail;
use App\Livewire\Admin\Exams\Student\MarksForm;
use App\Livewire\Admin\Payments\Create as PaymentsCreate;
use App\Livewire\Admin\Payments\DuePayments;
use App\Livewire\Admin\Payments\Index as PaymentsIndex;
use App\Livewire\Admin\Payments\Receipts as PaymentReceipt;
use App\Livewire\Admin\Students\Index as StudentsIndex;
use App\Livewire\Admin\Students\StudentProfile;
use App\Livewire\Admin\Students\Edit as StudentEdit;
use App\Livewire\Admin\Teachers\Create as TeachersCreate;
use App\Livewire\Admin\Teachers\Index as TeachersIndex;
use App\Livewire\Admin\Profile\Edit as ProfileEdit;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Public\LandingPage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login');

Route::middleware(['auth','admin'])->group(function () {
        // Admin Dashboard
        Route::get('/', Dashboard::class)->name('admin.dashboard');
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

        // Profile
        Route::get('/profile', ProfileEdit::class)->name('admin.profile.edit');

        //students
        Route::get('/students/{id}', StudentProfile::class)->name('student.profile');
        Route::get('/students/{id}/edit', StudentEdit::class)->name('student.edit');

        //Exams
        Route::get('/exams', Index::class)->name('admin.exams.index');
        Route::get('exams/show/{examid}', Show::class)->name('admin.exams.show');
        Route::get('/exams/{exam_id}/students/create', Create::class)->name('admin.students.create');
        Route::get('/exams/create', ExamsCreate::class)->name('admin.exams.create');
        Route::get('/exams/{exam}/edit', Edit::class)->name('admin.exams.edit');
        Route::get('/exams/marking/{exam_id}/{student_id}', MarksForm::class)->name('admin.exams.marking');
        Route::get('/exams/{exam_id}/student/{student_id}/details', MarksDetail::class)->name('admin.exams.student.details');

        Route::post('/logout', function () {
            \Illuminate\Support\Facades\Auth::logout();
            return redirect()->route('login');
        })->name('admin.logout');

});

// Teacher Routes - Removed since teachers now use admin routes
// Route::middleware(['auth', 'teacher'])->group(function () {
//     Route::get('/exams', \App\Livewire\Teacher\Exams\Index::class)->name('teacher.exams.index');
//     Route::get('/attendance', \App\Livewire\Teacher\Attendance\Index::class)->name('teacher.attendance.index');
    
//     Route::post('/logout', function () {
//         \Illuminate\Support\Facades\Auth::logout();
//         return redirect()->route('login');
//     })->name('teacher.logout');
// });

Route::get('/test-email', function () {
    $admission = App\Models\Admission::with(['student', 'batch.course'])->first();
    if ($admission) {
        try {
            \Illuminate\Support\Facades\Mail::to('test@example.com')->send(new App\Mail\AdmissionConfirmationMail($admission));
            return 'Test admission email sent successfully!';
        } catch (\Exception $e) {
            return 'Failed to send email: ' . $e->getMessage();
        }
    }
    return 'No admission found for testing.';
})->name('test.email');

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');

    return "All Caches are cleared by @Roni";
});

Route::get('/run-migations', function () {
    Artisan::call('migrate');
    return "Migrations are run successfully.";
});