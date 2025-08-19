<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard as AdminDashboard;

// Students
use App\Livewire\Admin\Students\Index as StudentsIndex;
use App\Livewire\Admin\Students\Form as StudentsForm;

// Admissions
use App\Livewire\Admin\Admissions\Index as AdmissionsIndex;
use App\Livewire\Admin\Admissions\Form as AdmissionsForm;

// Payments
use App\Livewire\Admin\Payments\Index as PaymentsIndex;

// Courses & Batches
use App\Livewire\Admin\Courses\Index as CoursesIndex;
use App\Livewire\Admin\Batches\Index as BatchesIndex;

// Route::get('/', function () {
//     return view('app');
// });

// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
// });

// Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', AdminDashboard::class)->name('dashboard');

    // Students
    Route::get('/students', StudentsIndex::class)->name('students.index');
    Route::get('/students/create', StudentsForm::class)->name('students.create');
    Route::get('/students/{studentId}/edit', StudentsForm::class)->name('students.edit');

    // Admissions
    Route::get('/admissions', AdmissionsIndex::class)->name('admissions.index');
    Route::get('/admissions/create', AdmissionsForm::class)->name('admissions.create');
    Route::get('/admissions/{admissionId}/edit', AdmissionsForm::class)->name('admissions.edit');

    // Payments
    Route::get('/payments', PaymentsIndex::class)->name('payments.index');

    // Courses
    Route::get('/courses', CoursesIndex::class)->name('courses.index');

    // Batches
    Route::get('/batches', BatchesIndex::class)->name('batches.index');
// });