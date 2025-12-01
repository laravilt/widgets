<script setup lang="ts">
import { computed } from 'vue'

interface Dataset {
    label?: string
    data: number[]
    backgroundColor?: string | string[]
    borderColor?: string
    fill?: boolean
}

interface ChartData {
    labels: string[]
    datasets: Dataset[]
}

interface ChartWidgetProps {
    heading?: string
    description?: string
    chartType: 'line' | 'bar' | 'pie' | 'doughnut'
    data: ChartData
    options?: Record<string, any>
    height?: number
    color?: string
    polling?: {
        enabled: boolean
        interval?: number
    }
}

const props = withDefaults(defineProps<ChartWidgetProps>(), {
    height: 300,
    options: () => ({}),
    polling: () => ({ enabled: false, interval: 10 })
})

const chartHeight = computed(() => `${props.height}px`)

const getColorPalette = () => {
    return [
        'rgb(59, 130, 246)',   // blue
        'rgb(16, 185, 129)',   // green
        'rgb(249, 115, 22)',   // orange
        'rgb(239, 68, 68)',    // red
        'rgb(168, 85, 247)',   // purple
        'rgb(236, 72, 153)',   // pink
        'rgb(234, 179, 8)',    // yellow
        'rgb(14, 165, 233)',   // cyan
    ]
}

// Line Chart
const renderLineChart = computed(() => {
    if (props.chartType !== 'line') return null

    const { labels, datasets } = props.data
    if (!labels.length || !datasets.length) return null

    const width = 100
    const height = 100
    const padding = 10

    const allValues = datasets.flatMap(d => d.data)
    const max = Math.max(...allValues, 0)
    const min = Math.min(...allValues, 0)
    const range = max - min || 1

    const xStep = (width - 2 * padding) / (labels.length - 1 || 1)

    return datasets.map((dataset, datasetIndex) => {
        const points = dataset.data.map((value, index) => {
            const x = padding + index * xStep
            const y = height - padding - ((value - min) / range) * (height - 2 * padding)
            return `${x},${y}`
        }).join(' ')

        const color = dataset.borderColor || getColorPalette()[datasetIndex % getColorPalette().length]
        const tension = props.options.tension ?? 0.4

        return {
            points,
            color,
            label: dataset.label,
            fill: dataset.fill ?? props.options.fill ?? false,
            tension
        }
    })
})

// Bar Chart
const renderBarChart = computed(() => {
    if (props.chartType !== 'bar') return null

    const { labels, datasets } = props.data
    if (!labels.length || !datasets.length) return null

    const allValues = datasets.flatMap(d => d.data)
    const max = Math.max(...allValues, 0)
    const range = max || 1

    const barWidth = 100 / (labels.length * datasets.length + labels.length)
    const groupGap = barWidth

    return {
        labels,
        datasets: datasets.map((dataset, datasetIndex) => ({
            label: dataset.label,
            color: dataset.backgroundColor || getColorPalette()[datasetIndex % getColorPalette().length],
            bars: dataset.data.map((value, index) => {
                const height = (value / range) * 100
                const x = index * (barWidth * datasets.length + groupGap) + datasetIndex * barWidth
                return {
                    x,
                    width: barWidth - 1,
                    height,
                    value
                }
            })
        }))
    }
})

// Pie/Doughnut Chart
const renderPieChart = computed(() => {
    if (props.chartType !== 'pie' && props.chartType !== 'doughnut') return null

    const { labels, datasets } = props.data
    if (!labels.length || !datasets.length || !datasets[0].data.length) return null

    const dataset = datasets[0]
    const total = dataset.data.reduce((sum, val) => sum + val, 0)

    if (total === 0) return null

    const colors = Array.isArray(dataset.backgroundColor)
        ? dataset.backgroundColor
        : getColorPalette()

    let currentAngle = -90 // Start from top

    return {
        slices: dataset.data.map((value, index) => {
            const percentage = (value / total) * 100
            const angle = (value / total) * 360
            const startAngle = currentAngle
            const endAngle = currentAngle + angle

            currentAngle += angle

            // Convert to radians
            const startRad = (startAngle * Math.PI) / 180
            const endRad = (endAngle * Math.PI) / 180

            const x1 = 50 + 45 * Math.cos(startRad)
            const y1 = 50 + 45 * Math.sin(startRad)
            const x2 = 50 + 45 * Math.cos(endRad)
            const y2 = 50 + 45 * Math.sin(endRad)

            const largeArc = angle > 180 ? 1 : 0

            const path = props.chartType === 'doughnut'
                ? `M ${50 + 25 * Math.cos(startRad)} ${50 + 25 * Math.sin(startRad)} L ${x1} ${y1} A 45 45 0 ${largeArc} 1 ${x2} ${y2} L ${50 + 25 * Math.cos(endRad)} ${50 + 25 * Math.sin(endRad)} A 25 25 0 ${largeArc} 0 ${50 + 25 * Math.cos(startRad)} ${50 + 25 * Math.sin(startRad)} Z`
                : `M 50 50 L ${x1} ${y1} A 45 45 0 ${largeArc} 1 ${x2} ${y2} Z`

            return {
                path,
                color: colors[index % colors.length],
                label: labels[index],
                value,
                percentage: percentage.toFixed(1)
            }
        })
    }
})
</script>

<template>
    <div class="space-y-4">
        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
            <div v-if="heading || description" class="px-6 py-4 border-b border-border bg-muted/30">
                <h3 v-if="heading" class="text-base font-semibold text-foreground">{{ heading }}</h3>
                <p v-if="description" class="text-sm text-muted-foreground mt-0.5">{{ description }}</p>
            </div>

            <div class="p-6">
            <!-- Line Chart -->
            <div v-if="chartType === 'line' && renderLineChart" :style="{ height: chartHeight }" class="relative">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid meet">
                    <!-- Grid lines -->
                    <g v-if="options.showGrid !== false" class="opacity-10">
                        <line v-for="i in 5" :key="`h-${i}`" x1="10" :y1="10 + i * 18" x2="90" :y2="10 + i * 18" stroke="currentColor" stroke-width="0.2" />
                        <line v-for="i in 5" :key="`v-${i}`" :x1="10 + i * 16" y1="10" :x2="10 + i * 16" y2="90" stroke="currentColor" stroke-width="0.2" />
                    </g>

                    <!-- Lines -->
                    <g v-for="(line, index) in renderLineChart" :key="index">
                        <polyline
                            :points="line.points"
                            :fill="line.fill ? line.color : 'none'"
                            :fill-opacity="line.fill ? '0.2' : '0'"
                            :stroke="line.color"
                            stroke-width="0.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            vector-effect="non-scaling-stroke"
                        />
                        <circle
                            v-if="options.showPoints !== false"
                            v-for="(point, pIndex) in line.points.split(' ')"
                            :key="pIndex"
                            :cx="point.split(',')[0]"
                            :cy="point.split(',')[1]"
                            r="1"
                            :fill="line.color"
                        />
                    </g>
                </svg>
            </div>

            <!-- Bar Chart -->
            <div v-else-if="chartType === 'bar' && renderBarChart" :style="{ height: chartHeight }" class="relative">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid meet">
                    <g v-for="(dataset, datasetIndex) in renderBarChart.datasets" :key="datasetIndex">
                        <rect
                            v-for="(bar, barIndex) in dataset.bars"
                            :key="barIndex"
                            :x="bar.x"
                            :y="100 - bar.height"
                            :width="bar.width"
                            :height="bar.height"
                            :fill="dataset.color"
                            rx="1"
                            class="transition-opacity hover:opacity-80"
                        />
                    </g>
                </svg>
            </div>

            <!-- Pie/Doughnut Chart -->
            <div v-else-if="(chartType === 'pie' || chartType === 'doughnut') && renderPieChart" class="flex flex-col lg:flex-row items-center gap-6" :style="{ minHeight: chartHeight }">
                <div class="flex-shrink-0" :style="{ width: chartHeight, height: chartHeight }">
                    <svg class="w-full h-full" viewBox="0 0 100 100">
                        <path
                            v-for="(slice, index) in renderPieChart.slices"
                            :key="index"
                            :d="slice.path"
                            :fill="slice.color"
                            class="transition-opacity hover:opacity-80"
                        />
                    </svg>
                </div>

                <!-- Legend -->
                <div v-if="options.showLegend !== false" class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div
                        v-for="(slice, index) in renderPieChart.slices"
                        :key="index"
                        class="flex items-center gap-2 text-sm"
                    >
                        <div class="w-3 h-3 rounded-sm flex-shrink-0" :style="{ backgroundColor: slice.color }"></div>
                        <span class="text-muted-foreground truncate">{{ slice.label }}</span>
                        <span class="ml-auto font-medium whitespace-nowrap">
                            {{ options.showPercentage ? `${slice.percentage}%` : slice.value }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex items-center justify-center text-muted-foreground" :style="{ height: chartHeight }">
                <p class="text-sm">No data available</p>
            </div>
            </div>
        </div>
    </div>
</template>
