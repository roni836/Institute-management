<?php

namespace App\Livewire\Admin\Batches;

use App\Models\Batch;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public string $q = '';

    protected $queryString = ['q','page'];

    public function updating($name, $value)
    {
        if ($name === 'q') $this->resetPage();
    }

    public function delete(int $id)
    {
        Batch::findOrFail($id)->delete();
        session()->flash('ok', 'Batch deleted.');
    }

    public function render()
    {
        $batches = Batch::with('course')
            ->when($this->q, fn($q) => $q->where(function($qq){
                $term = "%{$this->q}%";
                $qq->where('batch_name','like',$term)
                   ->orWhereHas('course', fn($c) => $c->where('name','like',$term));
            }))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.batches.index', compact('batches'));
    }
}
