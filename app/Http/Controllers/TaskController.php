<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Task::latest()->simplePaginate();
    }

    public function store(Request $request): Task
    {
        $this->validate($request, [
            'title' => [
                'required',
                'string',
            ],
            'remind_at' => [
                'nullable',
                'date',
                'after:now',
            ],
        ]);

        $task = new Task();
        $task->title = $request->input('title');
        $task->remind_at = $request->input('remind_at');
        $task->save();

        return $task;
    }
}
