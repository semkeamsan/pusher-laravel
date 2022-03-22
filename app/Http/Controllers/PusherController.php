<?php

namespace App\Http\Controllers;

use App\Events\MyEvent;
use Illuminate\Http\Request;

class PusherController extends Controller
{
    public function index()
    {
        $event = new MyEvent;
        return view('pusher',compact('event'));
    }

    public function store(Request $request)
    {
        try {
            event(new MyEvent($request->all()));
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
                'data'    => $request->all(),
            ];
        }
        return [
            'status' => true,
            'message' => __('Successfully'),
            'data'    => $request->all(),
        ];
    }

}
