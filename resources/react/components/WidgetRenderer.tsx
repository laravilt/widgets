import { router } from '@inertiajs/react';
import { useEffect, useRef, type ComponentType } from 'react';
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
    const pollingIntervals = useRef<Map<number, ReturnType<typeof setInterval>>>(new Map());

    // onMounted / onUnmounted — polling is set up once, from the widgets present at mount (like Vue)
    useEffect(() => {
        const intervals = pollingIntervals.current;

        const setupPolling = (widget: any, index: number) => {
            if (!widget.polling?.enabled || !queryRoute) {
                return;
            }

            const interval = (widget.polling.interval || 10) * 1000;

            const timerId = setInterval(() => {
                // Inertia v3 reloads always preserve state and scroll (Vue passed preserveState/preserveScroll: true)
                router.reload({
                    only: ['widgets'],
                });
            }, interval);

            intervals.set(index, timerId);
        };

        widgets.forEach((widget, index) => {
            setupPolling(widget, index);
        });

        return () => {
            intervals.forEach((timerId) => {
                clearInterval(timerId);
            });
            intervals.clear();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

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
