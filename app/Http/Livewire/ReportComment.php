<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportComment extends Component
{
    public $showModalReportComment = false;
    public $comment_id;
    public $reason;
    public $detail;



    protected $rules = [
        'reason' => ['required', 'integer', 'between:1,4'],
        'detail' => ['required', 'string']
    ];


    protected $listeners = [
        'showModalReportComment',
        'closeModalReportComment'
    ];


    public function mount($comment_id)
    {
        $this->comment_id = $comment_id;
    }



    public function showModalReportComment($comment_id)
    {
        //複数のレポート（コメント）がいっぺんに表示されないようにするためのif文
        if ($this->comment_id == $comment_id) {
            $this->showModalReportComment = true;
        }
    }

    public function closeModalReportComment()
    {
        $this->showModalReportComment = false;
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

        //reportsテーブルとcommentsテーブルの紐付けをする
        $report->comments()->sync([$this->comment_id]);

        $this->reset(['reason', 'detail']);

        $this->dispatchBrowserEvent('flash-message', ['message' => '通報しました']);
    }

    public function render()
    {
        return view('livewire.report-comment');
    }
}
