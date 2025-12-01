<script setup lang="ts">
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

interface Stat {
    label: string
    value: string | number
    description?: string
    icon?: string
    color?: string
    chart?: string
    chartData?: number[]
    chartColor?: string
    url?: string
    descriptionIcon?: boolean
    descriptionColor?: string
}

interface StatsOverviewWidgetProps {
    heading?: string
    description?: string
    stats: Stat[]
    columns?: number
    polling?: {
        enabled: boolean
        interval?: number
    }
}

const props = withDefaults(defineProps<StatsOverviewWidgetProps>(), {
    columns: 3,
    polling: () => ({ enabled: false, interval: 10 })
})

const gridCols = computed(() => {
    const colsMap: Record<number, string> = {
        1: 'grid-cols-1',
        2: 'grid-cols-1 md:grid-cols-2',
        3: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        5: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-5',
        6: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-6',
    }
    return colsMap[props.columns] || colsMap[3]
})

const getColorClasses = (color?: string) => {
    if (!color) return 'text-muted-foreground'

    const colorMap: Record<string, string> = {
        primary: 'text-primary',
        success: 'text-green-600 dark:text-green-500',
        danger: 'text-red-600 dark:text-red-500',
        warning: 'text-yellow-600 dark:text-yellow-500',
        info: 'text-blue-600 dark:text-blue-500',
        gray: 'text-gray-600 dark:text-gray-500',
    }

    return colorMap[color] || 'text-muted-foreground'
}

const getDescriptionColorClasses = (color?: string) => {
    if (!color) return 'text-muted-foreground'

    const colorMap: Record<string, string> = {
        success: 'text-green-600 dark:text-green-500',
        danger: 'text-red-600 dark:text-red-500',
        warning: 'text-yellow-600 dark:text-yellow-500',
        info: 'text-blue-600 dark:text-blue-500',
        gray: 'text-gray-600 dark:text-gray-500',
    }

    return colorMap[color] || 'text-muted-foreground'
}

const handleStatClick = (url?: string) => {
    if (url) {
        router.visit(url)
    }
}

const renderMiniChart = (stat: Stat) => {
    if (!stat.chart || !stat.chartData || stat.chartData.length === 0) {
        return null
    }

    const max = Math.max(...stat.chartData)
    const min = Math.min(...stat.chartData)
    const range = max - min || 1

    if (stat.chart === 'line') {
        const points = stat.chartData.map((value, index) => {
            const x = (index / (stat.chartData!.length - 1)) * 100
            const y = 100 - ((value - min) / range) * 100
            return `${x},${y}`
        }).join(' ')

        return {
            type: 'line',
            points,
            color: stat.chartColor || 'primary'
        }
    }

    if (stat.chart === 'bar') {
        return {
            type: 'bar',
            data: stat.chartData.map(value => ({
                height: ((value - min) / range) * 100,
                value
            })),
            color: stat.chartColor || 'primary'
        }
    }

    return null
}

const getChartColorClasses = (color?: string) => {
    const colorMap: Record<string, { stroke: string, fill: string }> = {
        primary: { stroke: 'stroke-primary', fill: 'fill-primary' },
        success: { stroke: 'stroke-green-600 dark:stroke-green-500', fill: 'fill-green-600 dark:fill-green-500' },
        danger: { stroke: 'stroke-red-600 dark:stroke-red-500', fill: 'fill-red-600 dark:fill-red-500' },
        warning: { stroke: 'stroke-yellow-600 dark:stroke-yellow-500', fill: 'fill-yellow-600 dark:fill-yellow-500' },
        info: { stroke: 'stroke-blue-600 dark:stroke-blue-500', fill: 'fill-blue-600 dark:fill-blue-500' },
        gray: { stroke: 'stroke-gray-600 dark:stroke-gray-500', fill: 'fill-gray-600 dark:fill-gray-500' },
    }

    return colorMap[color || 'primary']
}
</script>

<template>
    <div class="space-y-4">
        <div v-if="heading || description" class="space-y-1">
            <h3 v-if="heading" class="text-lg font-semibold text-foreground">{{ heading }}</h3>
            <p v-if="description" class="text-sm text-muted-foreground">{{ description }}</p>
        </div>

        <div :class="['grid gap-6', gridCols]">
            <div
                v-for="(stat, index) in stats"
                :key="index"
                class="relative overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all"
                :class="{
                    'cursor-pointer hover:shadow-md hover:border-primary/50': stat.url
                }"
                @click="handleStatClick(stat.url)"
            >
                <div class="p-6">
                    <!-- Header with Icon and Label -->
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="space-y-1 flex-1 min-w-0">
                            <p class="text-sm font-medium text-muted-foreground">
                                {{ stat.label }}
                            </p>
                        </div>
                        <div v-if="stat.icon && !stat.descriptionIcon" :class="['flex-shrink-0 p-2 rounded-lg bg-primary/10', getColorClasses(stat.color)]">
                            <i :class="stat.icon" class="text-lg"></i>
                        </div>
                    </div>

                    <!-- Value -->
                    <div class="mb-3">
                        <div :class="['text-2xl font-semibold tracking-tight', getColorClasses(stat.color)]">
                            {{ stat.value }}
                        </div>
                    </div>

                    <!-- Description or Chart -->
                    <div v-if="stat.description || renderMiniChart(stat)">
                        <div v-if="stat.description" class="flex items-center gap-1.5 text-xs font-medium" :class="getDescriptionColorClasses(stat.descriptionColor)">
                            <i v-if="stat.descriptionIcon && stat.icon" :class="stat.icon" class="text-sm"></i>
                            <span>{{ stat.description }}</span>
                        </div>

                        <!-- Mini Chart -->
                        <div v-else-if="renderMiniChart(stat)" class="h-10 -mx-1">
                            <svg
                                v-if="renderMiniChart(stat)?.type === 'line'"
                                class="w-full h-full"
                                viewBox="0 0 100 100"
                                preserveAspectRatio="none"
                            >
                                <polyline
                                    :points="renderMiniChart(stat)?.points"
                                    fill="none"
                                    :class="getChartColorClasses(renderMiniChart(stat)?.color).stroke"
                                    stroke-width="3"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    opacity="0.8"
                                />
                            </svg>

                            <div
                                v-else-if="renderMiniChart(stat)?.type === 'bar'"
                                class="flex items-end justify-between h-full gap-1"
                            >
                                <div
                                    v-for="(bar, barIndex) in renderMiniChart(stat)?.data"
                                    :key="barIndex"
                                    class="flex-1 rounded-t transition-all"
                                    :class="getChartColorClasses(renderMiniChart(stat)?.color).fill"
                                    :style="{ height: `${bar.height}%`, opacity: 0.8 }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
