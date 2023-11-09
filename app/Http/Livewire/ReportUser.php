<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportUser extends Component
{
    public $showModalReportUser = false;
    public $user_id;
    public $reason;
    public $detail;



    protected $rules = [
        'reason' => ['required', 'string'],
        'detail' => ['required', 'string']
    ];


    protected $listeners = [
        'showModalReportUser',
        'closeModalReportUser'
    ];


    public function mount($user_id)
    {
        $this->user_id = $user_id;
    }



    public function showModalReportUser()
    {
        $this->showModalReportUser = true;
    }

    public function closeModalReportUser()
    {
        $this->showModalReportUser = false;
        $this->reason = '';
        $this->detail = '';
        $this->resetErrorBag();
    }


    public function createReport()
    {
        $this->validate();

        //レポートを保存
        $report = Report::create([
            'contribute_user_id' => Auth::id(),
            'type' => 1,
            'reason' => $this->reason,
            'detail' => $this->detail,
        ]);

        //reportsテーブルとusersテーブルの紐付けをする
        $report->users()->sync([$this->user_id]);

        $this->reset(['reason', 'detail']);

        $this->dispatchBrowserEvent('flash-message', ['message' => '通報しました']);
    }

    public function render()
    {
        return view('livewire.report-user');
    }
}
