<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\WorkerController;
use App\Models\Applicant;
use App\Models\Contact;
use App\Models\Favourite;
use App\Models\Log;
use App\Models\Message;
use App\Models\News;
use App\Models\Recruitment;
use App\Models\RecruitmentFavourite;
use App\Models\Review_template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class UserManageController extends Controller
{
    public $page_count = 10;

    public function view_user_list(Request $request, $role = '*', $approved = '*')
    {
        $users = User::where('role', '<>', 'admin')
            ->when($role != '*', function($query) use($role) {
                $query->where('role', $role);
            })
            ->when($approved != '*', function($query) use($approved) {
                $query->where('approved', $approved);
            })
            ->paginate($this->page_count);

        return view('admin.users.list', compact('users'), ['role' => $role, 'approved' => $approved])
            ->with('i', (request()->input('page', 1) - 1) * $this->page_count);
    }

    public function set_user_approve(Request $request)
    {
        return User::find($request->input('id'))
            ->update(['approved' => $request->input('approved')]);
    }

    public function view_user_detail(Request $request, $id)
    {
        $user = User::find($id);
        $login_history = Log::where('user_id', $id)
            ->where('action_type', 'login')
            ->paginate($this->page_count);

        if($user['role'] == 'producer') {
            $user['review'] = ProducerController::calculate_review($user['id']);

            $recruitments = Recruitment::where('producer_id', $user['id'])
                ->whereIn('status', ['completed', 'canceled'])
                ->get()
                ->toArray();

            $result = [];
            foreach ($recruitments as $recruitment) {
                if(Applicant::where('_applicants.recruitment_id', $recruitment['id'])->where('recruitment_review', '<>', 0)->count() == 0) continue;
                $recruitment['review'] = RecruitmentController::calculate_review($recruitment['id']);
                $recruitment['workplace'] = format_address($recruitment['post_number'], $recruitment['prefectures'], $recruitment['city'], $recruitment['workplace']);
                $recruitment['applicants'] = Applicant::join('users', 'users.id', '=', '_applicants.worker_id')
                    ->where('_applicants.recruitment_id', $recruitment['id'])
                    ->get();
                array_push($result, $recruitment);
            }
            $recruitments = $this->paginate($result)->setPath(route('view_user_detail', ['id' => $user['id']]));

            return view('admin.users.detail', compact('recruitments'), ['user' => $user, 'login_history' => $login_history])
                ->with('i', (request()->input('page', 1) - 1) * $this->page_count);
        }
        elseif($user['role'] == 'worker') {
            $user['review'] = WorkerController::calculate_review($user['id']);

            $recruitments = Applicant::join('_recruitments', '_recruitments.id', '=', '_applicants.recruitment_id')
                ->join('users', 'users.id', '=', '_recruitments.producer_id')
                ->select('_recruitments.id as recruitment_id', 'users.id as user_id', 'users.*', '_recruitments.*', '_applicants.*')
                ->where('_recruitments.status', 'completed')
                ->where('_applicants.worker_id', $user['id'])
                ->paginate($this->page_count);

            return view('admin.users.detail', compact('recruitments'), ['user' => $user, 'login_history' => $login_history])
                ->with('i', (request()->input('page', 1) - 1) * $this->page_count);
        }

        return redirect()->intended('dashboard');
    }

    public function delete_user($id)
    {
        $user = User::find($id);

        if($user['role'] == 'producer') {
            $recruitments = Recruitment::where('producer_id', $id)
                ->get();
            foreach ($recruitments as $recruitment) {
                Applicant::where('recruitment_id', $recruitment['id'])
                    ->delete();
                Recruitment::find($recruitment['id'])
                    ->delete();
            }
            Contact::where('user_id', $id)
                ->delete();
            Favourite::where('user_id', $id)
                ->delete();
            Message::where('owner_id', $id)
                ->delete();
            News::where('user_id', $id)
                ->delete();
            RecruitmentFavourite::where('user_id', $id)
                ->delete();
            Review_template::where('user_id', $id)
                ->delete();
            Log::where('user_id', $id)
                ->delete();
        }
        else {
            Contact::where('user_id', $id)
                ->delete();
            Favourite::where('user_id', $id)
                ->delete();
            Message::where('owner_id', $id)
                ->delete();
            News::where('user_id', $id)
                ->delete();
            RecruitmentFavourite::where('user_id', $id)
                ->delete();
            Review_template::where('user_id', $id)
                ->delete();
            Log::where('user_id', $id)
                ->delete();
        }

        User::find($id)->delete();

        return redirect()->route('view_user_list');
    }

    public function paginate($items, $perPage = null, $page = null, $options = [])
    {
        $perPage = $perPage == null ? $this->page_count : $perPage;
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
