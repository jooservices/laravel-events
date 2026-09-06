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
    ) {
    }

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
        $action = $event instanceof HasLogAction ? $event->getAction() : EventLogAction::UPDATED;

        // Deleted with empty changed: treat as full removal so every prev key appears in diff.
        // Otherwise merge partial dirty attributes onto prev (documented Event Log contract).
        $current = ($action === EventLogAction::DELETED && $changed === [])
            ? []
            : array_merge($prev, $changed);
        $diff = $this->diffHelper->diff($prev, $current);

        $meta = [];
        $userId = auth()->id();
        if ($userId !== null) {
            $meta['user_id'] = $userId;
        }

        $this->eventService->logChange(
            $event->getLoggableType(),
            $event->getLoggableId(),
            $action,
            $prev,
            $changed,
            $diff,
            $meta,
        );
    }
}
