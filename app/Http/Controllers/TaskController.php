<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Schema\Link;

class TaskController extends Controller
{
    private $encoder;

    public function __construct(Encoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function index(): string
    {
        /** @var Paginator $paginator */
        $paginator = Task::latest()->simplePaginate();

        $isSubUrl = false;
        $hasMeta = false;
        $links = [
            Link::FIRST => new Link($isSubUrl, $paginator->url(1), $hasMeta),
        ];

        if ($previousPageUrl = $paginator->previousPageUrl()) {
            $links[Link::PREV] = new Link($isSubUrl, $paginator->previousPageUrl(), $hasMeta);
        }

        if ($nextPageUrl = $paginator->nextPageUrl()) {
            $links[Link::NEXT] = new Link($isSubUrl, $paginator->nextPageUrl(), $hasMeta);
        }

        $meta = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
        ];

        return $this->encoder
            ->withUrlPrefix(url())
            ->withLinks($links)
            ->withMeta($meta)
            ->encodeData($paginator);
    }

    public function store(Request $request): string
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

        return $this->encoder->encodeData($task);
    }

    public function update(Request $request, int $taskId): string
    {
        /** @var Task $task */
        $task = Task::findOrFail($taskId);

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

        $task->title = $request->input('title');
        $task->remind_at = $request->input('remind_at');
        $task->save();

        return $this->encoder->encodeData($task);
    }

    public function destroy(int $taskId): void
    {
        /** @var Task $task */
        $task = Task::findOrFail($taskId);

        $task->delete();
    }
}
