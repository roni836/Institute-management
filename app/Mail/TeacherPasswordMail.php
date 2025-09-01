<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $teacher;
    public string $password;

    public function __construct(User $teacher, string $password)
    {
        $this->teacher = $teacher;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject("Welcome to Antra Institute - Your Account Credentials")
            ->view('emails.teacher_password');
    }
}
