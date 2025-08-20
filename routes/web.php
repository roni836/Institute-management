<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Admissions\Create as AdmissionsCreate;
use App\Livewire\Admin\Admissions\Index as AdmissionsIndex;
use App\Livewire\Admin\Admissions\Edit as AdmissionEdit;
use App\Livewire\Admin\Payments\Index as PaymentsIndex;
use App\Livewire\Admin\Students\Form as StudentsForm;
use App\Livewire\Admin\Students\Index as StudentsIndex;
use App\Livewire\Admin\Batches\Index as BatchesIndex;
use App\Livewire\Admin\Courses\Index as CoursesIndex;

Route::get('/', Dashboard::class)->name('admin.dashboard');

// Students
Route::get('/students', StudentsIndex::class)->name('admin.students.index');
Route::get('/students/create', StudentsForm::class)->name('students.create');
Route::get('/students/{studentId}/edit', StudentsForm::class)->name('students.edit');

// Admissions
Route::get('/admissions', AdmissionsIndex::class)->name('admin.admissions.index');
Route::get('/admissions/create', AdmissionsCreate::class)->name('admin.admissions.create');
Route::get('/admissions/{admission}/edit', AdmissionEdit::class)->name('admin.admissions.edit');

// Payments
Route::get('/payments', PaymentsIndex::class)->name('admin.payments.index');

// Courses & Batches
Route::get('/courses', CoursesIndex::class)->name('admin.courses.index');
Route::get('/batches', BatchesIndex::class)->name('admin.batches.index');