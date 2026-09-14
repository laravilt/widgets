import { registerComponents } from '@laravilt/support/composables/registry';
import ChartWidget from './components/ChartWidget';
import StatsOverviewWidget from './components/StatsOverviewWidget';
import WidgetRenderer from './components/WidgetRenderer';

/**
 * Registers the same names as the Vue plugin (`app.component('StatsOverviewWidget', …)` etc. — no `laravilt-` prefix).
 */
export default {
    register(): void {
        registerComponents({
            StatsOverviewWidget,
            ChartWidget,
            WidgetRenderer,
        });
    },
};

export { StatsOverviewWidget, ChartWidget, WidgetRenderer };
