<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;
    public string $pdfContent;

    public function __construct(Transaction $transaction, string $pdfContent)
    {
        $this->transaction = $transaction;
        $this->pdfContent  = $pdfContent;
    }

    public function build()
    {
        $filename = sprintf('Receipt-TX-%d.pdf', $this->transaction->id);

        return $this->subject("Payment Receipt #TX-{$this->transaction->id} | Antra Institutions")
            ->view('emails.payment_receipt')
            ->attachData($this->pdfContent, $filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
