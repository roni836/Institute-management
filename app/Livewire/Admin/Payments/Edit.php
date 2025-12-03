<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Admission;
use App\Models\PaymentSchedule;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Edit extends Component
{
    public Admission $admission;
    public $schedules = [];
    public $transactions = [];
    public $editMode = false;
    public $selectedScheduleId = null;
    
    // Form fields for editing
    public $editingSchedule = [
        'id' => null,
        'installment_no' => null,
        'amount' => null,
        'due_date' => null,
        'status' => null,
        'paid_amount' => null,
        'payment_mode' => null,
        'transaction_reference' => null,
        'receipt_no' => null,
        'remarks' => null,
    ];
    
    // Transaction editing
    public $editingTransaction = [
        'id' => null,
        'amount' => null,
        'date' => null,
        'mode' => null,
        'reference_no' => null,
        'status' => null,
    ];
    
    public $showTransactionForm = false;
    public $editingTransactionMode = false;

    public function mount(Admission $admission)
    {
        $this->admission = $admission->load(['student', 'batch.course']);
        $this->loadPaymentData();
    }

    public function loadPaymentData()
    {
        // Load payment schedules with transactions
        $this->schedules = $this->admission->schedules()
            ->with(['transactions' => function($query) {
                $query->orderBy('date', 'desc');
            }])
            ->orderBy('installment_no')
            ->get()
            ->toArray();
            
        // Load all transactions for this admission
        $this->transactions = $this->admission->transactions()
            ->with('schedule')
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
        $this->resetEditingForms();
    }

    public function editSchedule($scheduleId)
    {
        $schedule = collect($this->schedules)->firstWhere('id', $scheduleId);
        if ($schedule) {
            $this->editingSchedule = [
                'id' => $schedule['id'],
                'installment_no' => $schedule['installment_no'],
                'amount' => $schedule['amount'],
                'due_date' => Carbon::parse($schedule['due_date'])->format('Y-m-d'),
                'status' => $schedule['status'],
                'paid_amount' => $schedule['paid_amount'],
                'payment_mode' => $schedule['payment_mode'] ?? null,
                'transaction_reference' => $schedule['transaction_reference'] ?? null,
                'receipt_no' => $schedule['receipt_no'] ?? null,
                'remarks' => $schedule['remarks'] ?? null,
            ];
            $this->selectedScheduleId = $scheduleId;
        }
    }

    public function updateSchedule()
    {
        $this->validate([
            'editingSchedule.amount' => 'required|numeric|min:0',
            'editingSchedule.due_date' => 'required|date',
            'editingSchedule.installment_no' => 'required|integer|min:1',
            'editingSchedule.payment_mode' => 'nullable|in:cash,card,upi,bank_transfer,cheque,head_office',
            'editingSchedule.transaction_reference' => 'nullable|string|max:191',
            'editingSchedule.receipt_no' => 'nullable|string|max:191',
            'editingSchedule.remarks' => 'nullable|string',
        ]);

        try {
            $schedule = PaymentSchedule::find($this->editingSchedule['id']);
            
            if ($schedule && $schedule->admission_id === $this->admission->id) {
                // Check if there are transactions - be careful with amount changes
                if ($schedule->transactions->count() > 0 && $this->editingSchedule['amount'] < $schedule->paid_amount) {
                    session()->flash('error', 'Cannot set amount less than already paid amount (₹' . number_format($schedule->paid_amount, 2) . ')');
                    return;
                }
                
                $schedule->update([
                    'installment_no' => $this->editingSchedule['installment_no'],
                    'amount' => $this->editingSchedule['amount'],
                    'due_date' => $this->editingSchedule['due_date'],
                    'payment_mode' => $this->editingSchedule['payment_mode'] ?? null,
                    'transaction_reference' => $this->editingSchedule['transaction_reference'] ?? null,
                    'receipt_no' => $this->editingSchedule['receipt_no'] ?? null,
                    'remarks' => $this->editingSchedule['remarks'] ?? null,
                ]);
                
                // Recalculate admission due amount
                $this->admission->refreshDue();
                
                session()->flash('success', 'Payment schedule updated successfully.');
                $this->loadPaymentData();
                $this->resetEditingForms();
            }
        } catch (\Exception $e) {
            Log::error('Failed to update payment schedule: ' . $e->getMessage());
            session()->flash('error', 'Failed to update payment schedule.');
        }
    }

    public function deleteSchedule($scheduleId)
    {
        try {
            $schedule = PaymentSchedule::find($scheduleId);
            
            if ($schedule && $schedule->admission_id === $this->admission->id) {
                // Check if there are transactions
                if ($schedule->transactions->count() > 0) {
                    session()->flash('error', 'Cannot delete payment schedule with existing transactions.');
                    return;
                }
                
                $schedule->delete();
                
                // Recalculate admission due amount
                $this->admission->refreshDue();
                
                session()->flash('success', 'Payment schedule deleted successfully.');
                $this->loadPaymentData();
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete payment schedule: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete payment schedule.');
        }
    }

    public function addNewSchedule()
    {
        $maxInstallmentNo = collect($this->schedules)->max('installment_no') ?? 0;
        
        $this->editingSchedule = [
            'id' => null,
            'installment_no' => $maxInstallmentNo + 1,
            'amount' => 0,
            'due_date' => now()->addMonth()->format('Y-m-d'),
            'status' => 'pending',
            'paid_amount' => 0,
            'payment_mode' => null,
            'transaction_reference' => null,
            'receipt_no' => null,
            'remarks' => null,
        ];
        $this->selectedScheduleId = 'new';
    }

    public function createSchedule()
    {
        $this->validate([
            'editingSchedule.amount' => 'required|numeric|min:0.01',
            'editingSchedule.due_date' => 'required|date',
            'editingSchedule.installment_no' => 'required|integer|min:1',
            'editingSchedule.payment_mode' => 'nullable|in:cash,card,upi,bank_transfer,cheque,head_office',
            'editingSchedule.transaction_reference' => 'nullable|string|max:191',
            'editingSchedule.receipt_no' => 'nullable|string|max:191',
            'editingSchedule.remarks' => 'nullable|string',
        ]);

        try {
            $this->admission->schedules()->create([
                'installment_no' => $this->editingSchedule['installment_no'],
                'amount' => $this->editingSchedule['amount'],
                'due_date' => $this->editingSchedule['due_date'],
                'status' => 'pending',
                'paid_amount' => 0,
                'payment_mode' => $this->editingSchedule['payment_mode'] ?? null,
                'transaction_reference' => $this->editingSchedule['transaction_reference'] ?? null,
                'receipt_no' => $this->editingSchedule['receipt_no'] ?? null,
                'remarks' => $this->editingSchedule['remarks'] ?? null,
            ]);
            
            // Recalculate admission due amount
            $this->admission->refreshDue();
            
            session()->flash('success', 'New payment schedule created successfully.');
            $this->loadPaymentData();
            $this->resetEditingForms();
        } catch (\Exception $e) {
            Log::error('Failed to create payment schedule: ' . $e->getMessage());
            session()->flash('error', 'Failed to create payment schedule.');
        }
    }

    public function editTransaction($transactionId)
    {
        $transaction = collect($this->transactions)->firstWhere('id', $transactionId);
        if ($transaction) {
            $this->editingTransaction = [
                'id' => $transaction['id'],
                'amount' => $transaction['amount'],
                'date' => Carbon::parse($transaction['date'])->format('Y-m-d'),
                'mode' => $transaction['mode'],
                'reference_no' => $transaction['reference_no'],
                'status' => $transaction['status'],
            ];
            $this->editingTransactionMode = true;
            $this->showTransactionForm = true;
        }
    }

    public function updateTransaction()
    {
        $this->validate([
            'editingTransaction.amount' => 'required|numeric|min:0.01',
            'editingTransaction.date' => 'required|date',
            'editingTransaction.mode' => 'required|string',
            'editingTransaction.status' => 'required|in:success,pending,failed',
        ]);

        try {
            $transaction = Transaction::find($this->editingTransaction['id']);
            
            if ($transaction && $transaction->admission_id === $this->admission->id) {
                $oldAmount = $transaction->amount;
                
                $transaction->update([
                    'amount' => $this->editingTransaction['amount'],
                    'date' => $this->editingTransaction['date'],
                    'mode' => $this->editingTransaction['mode'],
                    'reference_no' => $this->editingTransaction['reference_no'],
                    'status' => $this->editingTransaction['status'],
                ]);
                
                // Update the payment schedule's paid amount if transaction amount changed
                if ($transaction->schedule && $oldAmount != $this->editingTransaction['amount']) {
                    $schedule = $transaction->schedule;
                    $totalPaid = $schedule->transactions()->where('status', 'success')->sum('amount');
                    $schedule->update([
                        'paid_amount' => $totalPaid,
                        'status' => $totalPaid >= $schedule->amount ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending')
                    ]);
                }
                
                // Recalculate admission due amount
                $this->admission->refreshDue();
                
                session()->flash('success', 'Transaction updated successfully.');
                $this->loadPaymentData();
                $this->resetEditingForms();
            }
        } catch (\Exception $e) {
            Log::error('Failed to update transaction: ' . $e->getMessage());
            session()->flash('error', 'Failed to update transaction.');
        }
    }

    public function deleteTransaction($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);
            
            if ($transaction && $transaction->admission_id === $this->admission->id) {
                $schedule = $transaction->schedule;
                $transaction->delete();
                
                // Recalculate the payment schedule's paid amount
                if ($schedule) {
                    $totalPaid = $schedule->transactions()->where('status', 'success')->sum('amount');
                    $schedule->update([
                        'paid_amount' => $totalPaid,
                        'status' => $totalPaid >= $schedule->amount ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending')
                    ]);
                }
                
                // Recalculate admission due amount
                $this->admission->refreshDue();
                
                session()->flash('success', 'Transaction deleted successfully.');
                $this->loadPaymentData();
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete transaction: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete transaction.');
        }
    }

    public function resetEditingForms()
    {
        $this->selectedScheduleId = null;
        $this->editingSchedule = [
            'id' => null,
            'installment_no' => null,
            'amount' => null,
            'due_date' => null,
            'status' => null,
            'paid_amount' => null,
        ];
        
        $this->editingTransaction = [
            'id' => null,
            'amount' => null,
            'date' => null,
            'mode' => null,
            'reference_no' => null,
            'status' => null,
        ];
        
        $this->showTransactionForm = false;
        $this->editingTransactionMode = false;
    }

    public function render()
    {
        return view(view: 'livewire.admin.payments.edit');
    }
}
