<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventLog;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use JOOservices\LaravelEvents\EventLog\Contracts\HasLogAction;
use JOOservices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;
use JOOservices\LaravelEvents\EventPersisterInterface;
use JOOservices\LaravelEvents\Support\DiffHelper;

final class EventLogSubscriber
{
    public function __construct(
        private readonly EventPersisterInterface $eventService,
        private readonly DiffHelper $diffHelper,
    ) {
    }

    public function subscribe(Dispatcher $events): void
    {
        if (! $this->featureEnabled('events.event_log.enabled', true)) {
            return;
        }
        $events->listen(LoggableModelInterface::class, [$this, 'logModelChange']);
    }

    public function logModelChange(LoggableModelInterface $event): void
    {
        $prev = $event->getPrev();
        $changed = $event->getChanged();
        $action = $event instanceof HasLogAction ? $event->getAction() : EventLogAction::UPDATED->value;

        // Deleted with empty changed: treat as full removal so every prev key appears in diff.
        // Otherwise merge partial dirty attributes onto prev (documented Event Log contract).
        $current = ($action === EventLogAction::DELETED->value && $changed === [])
            ? []
            : array_merge($prev, $changed);
        $diff = $this->diffHelper->diff($prev, $current);

        $meta = [];
        $userId = Auth::id();
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

    private function featureEnabled(string $key, bool $default): bool
    {
        $enabled = config($key, $default);

        return is_bool($enabled) ? $enabled : filter_var($enabled, FILTER_VALIDATE_BOOLEAN);
    }
}
