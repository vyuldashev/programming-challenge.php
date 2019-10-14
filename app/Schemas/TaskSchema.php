<?php

namespace App\Schemas;

use Neomerx\JsonApi\Schema\BaseSchema;

class TaskSchema extends BaseSchema
{
    /**
     * Get resource type.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'tasks';
    }

    /**
     * Get resource identity. Newly created objects without ID may return `null` to exclude it from encoder output.
     *
     * @param object $resource
     *
     * @return string|null
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * Get resource attributes.
     *
     * @param mixed $resource
     *
     * @return iterable
     */
    public function getAttributes($resource): iterable
    {
        return [
            'id' => $resource->id,
            'title' => $resource->title,
            'remind_at' => $resource->remind_at ? (string)$resource->remind_at : null,
            'created_at' => (string)$resource->created_at,
            'updated_at' => (string)$resource->updated_at,
        ];
    }

    /**
     * Get resource relationship descriptions.
     *
     * @param mixed $resource
     *
     * @return iterable
     */
    public function getRelationships($resource): iterable
    {
        return [];
    }
}
