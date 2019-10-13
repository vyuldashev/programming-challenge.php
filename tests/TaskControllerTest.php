<?php

namespace Tests;

use App\Models\Task;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndex(): void
    {
        factory(Task::class, 30)->create();

        $this->get('/tasks');

        $this->assertResponseOk();

        $this->seeJsonStructure([
            'current_page',
            'data' => [
                [
                    'id',
                    'title',
                    'remind_at',
                    'created_at',
                    'updated_at',
                ],
            ],
            'first_page_url',
            'from',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
        ]);

        $actual = json_decode($this->response->getContent(), true);

        $this->assertCount(15, $actual['data']);
    }

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

        $this->seeInDatabase('tasks', [
            'title' => $title,
            'remind_at' => null,
        ]);
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

        $this->seeInDatabase('tasks', [
            'title' => $title,
            'remind_at' => $remindAt,
        ]);
    }
}
