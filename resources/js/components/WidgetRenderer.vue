<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import StatsOverviewWidget from './StatsOverviewWidget.vue'
import ChartWidget from './ChartWidget.vue'

interface WidgetRendererProps {
    widgets: any[]
    queryRoute?: string
}

const props = defineProps<WidgetRendererProps>()

const pollingIntervals = ref<Map<number, NodeJS.Timeout>>(new Map())

const componentMap: Record<string, any> = {
    'StatsOverviewWidget': StatsOverviewWidget,
    'ChartWidget': ChartWidget,
}

const setupPolling = (widget: any, index: number) => {
    if (!widget.polling?.enabled || !props.queryRoute) return

    const interval = (widget.polling.interval || 10) * 1000

    const timerId = setInterval(() => {
        router.reload({
            only: ['widgets'],
            preserveState: true,
            preserveScroll: true,
        })
    }, interval)

    pollingIntervals.value.set(index, timerId)
}

const clearPolling = (index: number) => {
    const timerId = pollingIntervals.value.get(index)
    if (timerId) {
        clearInterval(timerId)
        pollingIntervals.value.delete(index)
    }
}

onMounted(() => {
    props.widgets.forEach((widget, index) => {
        setupPolling(widget, index)
    })
})

onUnmounted(() => {
    pollingIntervals.value.forEach((timerId) => {
        clearInterval(timerId)
    })
    pollingIntervals.value.clear()
})
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
