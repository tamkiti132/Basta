<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportGroup extends Component
{
    public $showModalReportGroup = false;
    public $group_id;
    public $reason;
    public $detail;



    protected $rules = [
        'reason' => ['required', 'string'],
        'detail' => ['required', 'string']
    ];


    protected $listeners = [
        'showModalReportGroup',
        'closeModalReportGroup'
    ];


    public function mount()
    {
        $this->group_id = session()->get('group_id');
    }



    public function showModalReportGroup()
    {
        $this->showModalReportGroup = true;
    }

    public function closeModalReportGroup()
    {
        $this->showModalReportGroup = false;
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
            'type' => 3,
            'reason' => $this->reason,
            'detail' => $this->detail,
        ]);

        //reportsテーブルとgroupsテーブルの紐付けをする
        $report->groups()->sync([$this->group_id]);

        $this->reset(['reason', 'detail']);
    }

    public function render()
    {
        return view('livewire.report-group');
    }
}
