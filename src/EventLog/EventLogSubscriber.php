<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventLog;

use Illuminate\Contracts\Events\Dispatcher;
use JOOservices\LaravelEvents\EventLog\Contracts\HasLogAction;
use JOOservices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;
use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\Support\DiffHelper;

class EventLogSubscriber
{
    public function __construct(
        protected EventService $eventService,
        protected DiffHelper $diffHelper,
    ) {}

    public function subscribe(Dispatcher $events): void
    {
        if (! config('events.event_log.enabled', true)) {
            return;
        }
        $events->listen(LoggableModelInterface::class, [$this, 'logModelChange']);
    }

    public function logModelChange(LoggableModelInterface $event): void
    {
        $prev = $event->getPrev();
        $changed = $event->getChanged();
        $current = array_merge($prev, $changed);
        $diff = $this->diffHelper->diff($prev, $current);
        $action = $event instanceof HasLogAction ? $event->getAction() : 'updated';

        $this->eventService->logChange(
            $event->getLoggableType(),
            $event->getLoggableId(),
            $action,
            $prev,
            $changed,
            $diff,
            ['user_id' => auth()->id()],
        );
    }
}
