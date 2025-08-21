<?php
namespace App\Livewire\Admin\Payments;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    #[Url( as : 'q')]
    public string $search = '';

    #[Url]
    public string $status = ''; // success|pending|failed or blank

    #[Url]
    public string $mode = ''; // cash|cheque|online or blank

    public int $perPage = 15;

    public function updatingSearch()
    {$this->resetPage();}
    public function updatingStatus()
    {$this->resetPage();}
    public function updatingMode()
    {$this->resetPage();}

    public function render()
    {
        $tx = Transaction::query()
            ->with(['admission.student', 'admission.batch', 'schedule'])
            ->when($this->search, function ($q) {
                $q->whereHas('admission.student', function ($qq) {
                    $qq->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                })->orWhereHas('admission.batch', function ($qq) {
                    $qq->where('batch_name', 'like', "%{$this->search}%");
                })->orWhere('reference_no', 'like', "%{$this->search}%");
            })
            ->when($this->status !== '', fn($q) => $q->where('status', $this->status))
            ->when($this->mode !== '', fn($q) => $q->where('mode', $this->mode))
            ->latest('date')
            ->paginate($this->perPage);

        return view('livewire.admin.payments.index', [
            'transactions' => $tx,
        ]);
    }
}
