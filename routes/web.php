<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Teachers\Create as TeachersCreate;
use App\Livewire\Admin\Teachers\Index as TeachersIndex;
use App\Livewire\Admin\Admissions\Create as AdmissionsCreate;
use App\Livewire\Admin\Admissions\Index as AdmissionsIndex;
use App\Livewire\Admin\Admissions\Edit as AdmissionEdit;
use App\Livewire\Admin\Admissions\Show as AdmissionShow;
use App\Livewire\Admin\Payments\Index as PaymentsIndex;
use App\Livewire\Admin\Payments\DuePayments;
use App\Livewire\Admin\Payments\Receipts as PaymentReceipt;
use App\Livewire\Admin\Payments\Create as PaymentsCreate;
use App\Livewire\Admin\Students\Form as StudentsForm;
use App\Livewire\Admin\Students\Index as StudentsIndex;
use App\Livewire\Admin\Batches\Index as BatchesIndex;
use App\Livewire\Admin\Courses\Index as CoursesIndex;
use App\Livewire\Admin\Exams\Create as ExamsCreate;
use App\Livewire\Admin\Exams\Index;
use App\Livewire\Admin\Exams\Show;
use App\Livewire\Admin\Exams\Student\Create;
use App\Livewire\Admin\Students\StudentProfile;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Public\LandingPage;

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
Route::get('/payments/create', PaymentsCreate::class)->name('admin.payments.create');
Route::get('/due-payments', DuePayments::class)->name('admin.due-payments.index');
Route::get('/payments/{transaction}/receipt', PaymentReceipt::class)->name('admin.payments.receipt');

// Courses & Batches
Route::get('/courses', CoursesIndex::class)->name('admin.courses.index');
Route::get('/courses/create', \App\Livewire\Admin\Courses\Create::class)->name('admin.courses.create');
Route::get('/courses/{id}/edit', \App\Livewire\Admin\Courses\Edit::class)->name('admin.courses.edit');
Route::get('/courses/{id}/view', \App\Livewire\Admin\Courses\View::class)->name('admin.courses.view');
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
// Auth
Route::get('/login', Login::class)->name('login');
Route::get('/login', Login::class)->name('logout');
Route::get('/register', Register::class)->name('register');
