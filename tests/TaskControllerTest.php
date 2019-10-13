<?php

namespace Tests;

use App\Models\Task;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testStore(): void
    {
        $title = 'Buy a milk';

        $this->post('/tasks', [
            'title' => $title,
        ]);

        $this->assertResponseOk();

        $this->seeJson([
            'title' => $title,
            'remind_at' => null,
        ]);

        $task = Task::first();

        $this->assertNotNull($task);
        $this->assertSame($title, $task->title);
        $this->assertNull($task->remind_at);
    }

    public function testStoreWithReminder(): void
    {
        $title = 'Book a hotel';
        $remindAt = (string)Carbon::now()->addSecond();

        $this->post('/tasks', [
            'title' => $title,
            'remind_at' => $remindAt,
        ]);

        $this->assertResponseOk();

        $this->seeJson([
            'title' => $title,
            'remind_at' => $remindAt,
        ]);

        $task = Task::first();

        $this->assertNotNull($task);
        $this->assertSame($title, $task->title);
        $this->assertSame($remindAt, (string)$task->remind_at);
    }
}
