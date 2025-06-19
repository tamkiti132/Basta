<?php

namespace App\Http\Livewire;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportMemo extends Component
{
    public $showModalReportMemo = false;
    public $memo_id;
    public $reason;
    public $detail;

    protected $rules = [
        'reason' => ['required', 'integer', 'between:1,4'],
        'detail' => ['required', 'string'],
    ];

    protected $listeners = [
        'showModalReportMemo',
        'closeModalReportMemo',
    ];

    public function mount($memo_id)
    {
        $this->memo_id = $memo_id;
    }

    public function showModalReportMemo()
    {
        $this->showModalReportMemo = true;
    }

    public function closeModalReportMemo()
    {
        $this->showModalReportMemo = false;
        $this->reason = '';
        $this->detail = '';
        $this->resetErrorBag();
    }

    public function createReport()
    {
        $this->validate();

        // レポートを保存
        $report = Report::create([
            'contribute_user_id' => Auth::id(),
            'type' => 2,
            'reason' => $this->reason,
            'detail' => $this->detail,
        ]);

        // reportsテーブルとmemosテーブルの紐付けをする
        $report->memos()->sync([$this->memo_id]);

        $this->reset(['reason', 'detail']);

        $this->dispatchBrowserEvent('flash-message', ['message' => '通報しました']);
    }

    public function render()
    {
        return view('livewire.report-memo');
    }
}
