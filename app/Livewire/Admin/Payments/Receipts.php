<?php

namespace App\Livewire\Admin\Payments;

use App\Mail\PaymentReceiptMail;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Receipts extends Component
{
     public Transaction $transaction;
    public $consolidatedTransactions = [];
    public $allPaymentSchedules = [];
    public bool $isConsolidatedReceipt = false;

    // Email modal state
    public bool $showEmailModal = false;
    public string $email = '';

    public function mount(Transaction $transaction)
    {
        // Eager load related data for the view
        $transaction->load(['admission.student', 'admission.batch.course', 'schedule']);
        $this->transaction = $transaction;
        
        // Get all transactions with the same receipt number for this admission
        $this->consolidatedTransactions = Transaction::where('receipt_number', $transaction->receipt_number)
            ->where('admission_id', $transaction->admission_id)
            ->with(['schedule'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        $this->isConsolidatedReceipt = $this->consolidatedTransactions->count() > 1;
        
        // If this is a consolidated receipt, get all payment schedules for this admission
        // to show both paid and due installments
        if ($this->isConsolidatedReceipt) {
            $this->allPaymentSchedules = \App\Models\PaymentSchedule::where('admission_id', $transaction->admission_id)
                ->with(['transactions'])
                ->orderBy('installment_no')
                ->get();
        }
    }

    public function openEmailModal()
    {
        $this->resetErrorBag();
        $this->email = '';
        $this->showEmailModal = true;
    }

    public function closeEmailModal()
    {
        $this->showEmailModal = false;
    }


    protected function renderPdfHtml(): string
    {
        // Use the dedicated PDF view (inline CSS, dompdf-friendly)
        return view('pdf.receipt', [
            'tx' => $this->transaction,
            'consolidatedTransactions' => $this->consolidatedTransactions,
            'allPaymentSchedules' => $this->allPaymentSchedules,
            'isConsolidatedReceipt' => $this->isConsolidatedReceipt,
            'org' => [
                'name'    => 'Antra Institutions',
                'gst'     => '5451515121',
                'contact' => '615112123',
                'address' => 'abcd',
            ],
        ])->render();
    }

    public function downloadPdf()
    {
        $html = $this->renderPdfHtml();
        $pdf  = Pdf::loadHTML($html)->setPaper('A4');

        $filename = sprintf('Receipt-TX-%d.pdf', $this->transaction->id);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function sendEmail()
    {
        $this->validate([
            'email' => ['required','email'],
        ]);

        $html = $this->renderPdfHtml();
        $pdf  = Pdf::loadHTML($html)->setPaper('A4');
        $pdfContent = $pdf->output();

        Mail::to($this->email)->send(
            new PaymentReceiptMail($this->transaction, $pdfContent)
        );

        $this->showEmailModal = false;
        session()->flash('success', 'Receipt emailed successfully.');
    }

    public function render()
    {
        // If the request sets ?minimal=1 we render using a minimal print layout
        $data = [
            'tx' => $this->transaction,
            'consolidatedTransactions' => $this->consolidatedTransactions,
            'allPaymentSchedules' => $this->allPaymentSchedules,
            'isConsolidatedReceipt' => $this->isConsolidatedReceipt,
            'org' => [
                'name'    => 'Antra Institutions',
                'gst'     => '5451515121',
                'contact' => '615112123',
                'address' => 'abcd',
            ],
        ];

        if (request()->query('minimal') == '1') {
            // Render the receipt into the minimal print layout by injecting the rendered view as the slot content.
            $html = view('livewire.admin.payments.receipts', $data)->render();
            return view('components.layouts.print', array_merge($data, ['slot' => $html]));
        }

        // Default admin layout (interactive)
        return view('livewire.admin.payments.receipts', $data);
    }

    // Helper actions bound to UI buttons
   

 
}
