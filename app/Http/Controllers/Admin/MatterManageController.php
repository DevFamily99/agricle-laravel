<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\RecruitmentController;
use App\Models\Recruitment;
use App\Models\User;
use Illuminate\Http\Request;

class MatterManageController extends Controller
{
    public $page_count = 8;

    public function view_matter_list(Request $request)
    {
        $farms = User::where('role', 'producer')
            ->paginate($this->page_count);
        foreach ($farms as $farm) {
            $farm['collecting'] = Recruitment::where('producer_id', $farm['id'])->where('status', 'collecting')->count();
            $farm['working'] = Recruitment::where('producer_id', $farm['id'])->where('status', 'working')->count();
            $farm['completed'] = Recruitment::where('producer_id', $farm['id'])->where('status', 'completed')->count();
        }

        return view('admin.matters.list', compact('farms'))
            ->with('i', (request()->input('page', 1) - 1) * $this->page_count);
    }

    public function search_matter_admin(Request $request)
    {
        $data = $request->all();

        $matters = Recruitment::join('users', 'users.id', '=', '_recruitments.producer_id')
            ->select('_recruitments.*', 'users.avatar', 'users.family_name')
            ->where('status', '<>', 'draft')
            ->when(isset($data['producer_name']), function($query) use($data) {
                $query->where('users.family_name', 'like', '%'.$data['producer_name'].'%');
            })
            ->when($data['recruitment_status'] != 'all', function($query) use($data) {
                $query->where('status', $data['recruitment_status']);
            })
            ->when(isset($data['work_date_start']), function($query) use($data) {
                $query->where('work_date_start', $data['work_date_start']);
            })
            ->when(isset($data['work_date_end']), function($query) use($data) {
                $query->where('work_date_end', $data['work_date_end']);
            })
            ->paginate($this->page_count);

        return view('admin.matters.listContent', compact('matters'))
            ->with('i', (request()->input('page', 1) - 1) * $this->page_count);
    }


    public function view_matter_list_by_producer(Request $request, $id)
    {
        $producer = User::find($id);
        $producer['review'] = ProducerController::calculate_review($producer['id']);

        $matters = Recruitment::join('users', 'users.id', '=', '_recruitments.producer_id')
            ->select('_recruitments.*', 'users.avatar', 'users.family_name')
            ->where('producer_id', $id)
            ->where('status', '<>', 'draft')
            ->paginate($this->page_count);

        return view('admin.matters.list_by_producer', compact('matters'), ['producer' => $producer])
            ->with('i', (request()->input('page', 1) - 1) * $this->page_count);
    }

    public function view_matter_detail(Request $request, $id)
    {
        $matter = RecruitmentController::get_recruitment_info($id);

        return view('admin.matters.detail', ['matter' => $matter]);
    }

    public function set_matter_approve(Request $request)
    {
        return Recruitment::find($request->input('id'))
            ->update(['approved' => $request->input('approved')]);
    }
}
