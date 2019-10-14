<?php

namespace App\Providers;

use App\Models\Task;
use App\Schemas\TaskSchema;
use Illuminate\Support\ServiceProvider;
use Neomerx\JsonApi\Encoder\Encoder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Encoder::class, static function () {
            return Encoder::instance([
                Task::class => TaskSchema::class,
            ]);
        });
    }
}
