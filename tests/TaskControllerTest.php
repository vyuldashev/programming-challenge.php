<?php

namespace Tests;

use App\Models\Task;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TaskControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex(): void
    {
        factory(Task::class, 30)->create();

        $this->get('/tasks');

        $this->assertResponseOk();

        $this->seeJsonStructure([
            'meta' => [
                'current_page',
                'per_page',
            ],
            'links' => [
                'first',
                'next',
            ],
            'data' => [
                [
                    'type',
                    'id',
                    'attributes' => [
                        'id',
                        'title',
                        'remind_at',
                        'created_at',
                        'updated_at',
                    ],
                    'links' => [
                        'self',
                    ],
                ],
            ],
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

        $this->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'id',
                    'title',
                    'remind_at',
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

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

        $this->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'id',
                    'title',
                    'remind_at',
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        $this->seeJson([
            'title' => $title,
            'remind_at' => $remindAt,
        ]);

        $this->seeInDatabase('tasks', [
            'title' => $title,
            'remind_at' => $remindAt,
        ]);
    }

    public function testUpdateTaskNotFound(): void
    {
        $this
            ->put('/tasks/1', ['title' => 'foo'])
            ->assertResponseStatus(404);
    }

    public function testUpdate(): void
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $title = 'foo';

        $this->put('/tasks/' . $task->id, ['title' => $title]);

        $this->assertResponseOk();

        $this->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'id',
                    'title',
                    'remind_at',
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        $this->seeJson([
            'id' => $task->id,
            'title' => $title,
            'remind_at' => null,
            'created_at' => (string)$task->created_at,
            'updated_at' => (string)$task->updated_at,
        ]);

        $task->refresh();

        $this->assertSame($title, $task->title);
        $this->assertNull($task->remind_at);
    }

    public function testUpdateWithRemindAt(): void
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $title = 'Pay bills';
        $remindAt = Carbon::now()->addSecond();

        $this->put('/tasks/' . $task->id, ['title' => $title, 'remind_at' => (string)$remindAt]);

        $this->assertResponseOk();

        $this->seeJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'id',
                    'title',
                    'remind_at',
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);

        $this->seeJson([
            'id' => $task->id,
            'title' => $title,
            'remind_at' => (string)$remindAt,
            'created_at' => (string)$task->created_at,
            'updated_at' => (string)$task->updated_at,
        ]);

        $task->refresh();

        $this->assertSame($title, $task->title);
        $this->assertSame((string)$remindAt, (string)$task->remind_at);
    }

    public function testDestroyNotFound(): void
    {
        $this->delete('/tasks/1')->assertResponseStatus(404);
    }

    public function testDestroy(): void
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->delete('/tasks/' . $task->id)->assertResponseOk();

        $this->missingFromDatabase('tasks', ['id' => $task->id]);
    }
}
