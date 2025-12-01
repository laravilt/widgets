import type { App } from 'vue'
import StatsOverviewWidget from './components/StatsOverviewWidget.vue'
import ChartWidget from './components/ChartWidget.vue'
import WidgetRenderer from './components/WidgetRenderer.vue'

export default {
  install(app: App) {
    app.component('StatsOverviewWidget', StatsOverviewWidget)
    app.component('ChartWidget', ChartWidget)
    app.component('WidgetRenderer', WidgetRenderer)
  },
}

export { StatsOverviewWidget, ChartWidget, WidgetRenderer }
