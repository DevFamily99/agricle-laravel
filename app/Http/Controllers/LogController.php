<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function create($user_id, $action_type)
        {
            $log = Log::create([
                'user_id' => $user_id,
                'action_type' => $action_type,
            ]);

            return $log;
        }
}
