<script setup lang="ts">
import { computed, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import StatsOverviewWidget from './StatsOverviewWidget.vue'
import ChartWidget from './ChartWidget.vue'

interface WidgetRendererProps {
    widgets: any[]
    queryRoute?: string
}

const props = defineProps<WidgetRendererProps>()

const componentMap: Record<string, any> = {
    'StatsOverviewWidget': StatsOverviewWidget,
    'ChartWidget': ChartWidget,
}

// Each tick reloads every widget, so widgets sharing an interval share one timer.
// A string key keeps the watcher stable across reloads that don't change the polling configuration.
const pollingIntervals = computed(() => {
    if (!props.queryRoute) return ''

    return Array.from(
        new Set(
            props.widgets
                .filter((widget) => widget.polling?.enabled)
                .map((widget) => (widget.polling.interval || 10) * 1000),
        ),
    )
        .sort((a, b) => a - b)
        .join(',')
})

let timerIds: ReturnType<typeof setInterval>[] = []

const clearPolling = () => {
    timerIds.forEach((timerId) => clearInterval(timerId))
    timerIds = []
}

watch(
    pollingIntervals,
    (intervals) => {
        clearPolling()

        if (!intervals) return

        timerIds = intervals.split(',').map((interval) =>
            setInterval(() => {
                router.reload({
                    only: ['widgets'],
                    preserveState: true,
                    preserveScroll: true,
                })
            }, Number(interval)),
        )
    },
    { immediate: true },
)

onUnmounted(clearPolling)
</script>

<template>
    <div class="space-y-6">
        <component
            v-for="(widget, index) in widgets"
            :key="index"
            :is="componentMap[widget.component]"
            v-bind="widget"
        />
    </div>
</template>
