import { router } from '@inertiajs/react';
import { useEffect, type ComponentType } from 'react';
import ChartWidget from './ChartWidget';
import StatsOverviewWidget from './StatsOverviewWidget';

export interface WidgetRendererProps {
    widgets: any[];
    queryRoute?: string;
}

const componentMap: Record<string, ComponentType<any>> = {
    StatsOverviewWidget: StatsOverviewWidget,
    ChartWidget: ChartWidget,
};

export default function WidgetRenderer({ widgets, queryRoute }: WidgetRendererProps) {
    // Each tick reloads every widget, so widgets sharing an interval share one timer.
    // A string key keeps the effect stable across reloads that don't change the polling configuration.
    const pollingIntervals = queryRoute
        ? Array.from(
              new Set(
                  widgets
                      .filter((widget) => widget.polling?.enabled)
                      .map((widget) => (widget.polling.interval || 10) * 1000),
              ),
          )
              .sort((a, b) => a - b)
              .join(',')
        : '';

    useEffect(() => {
        if (!pollingIntervals) {
            return;
        }

        const timerIds = pollingIntervals.split(',').map((interval) =>
            setInterval(() => {
                // Inertia v3 reloads always preserve state and scroll (Vue passed preserveState/preserveScroll: true)
                router.reload({
                    only: ['widgets'],
                });
            }, Number(interval)),
        );

        return () => {
            timerIds.forEach((timerId) => {
                clearInterval(timerId);
            });
        };
    }, [pollingIntervals]);

    return (
        <div className="space-y-6">
            {widgets.map((widget, index) => {
                const Component = componentMap[widget.component];

                return Component ? <Component key={index} {...widget} /> : null;
            })}
        </div>
    );
}

export { WidgetRenderer };
