import { useMemo } from 'react';

export interface Dataset {
    label?: string;
    data: number[];
    backgroundColor?: string | string[];
    borderColor?: string;
    fill?: boolean;
}

export interface ChartData {
    labels: string[];
    datasets: Dataset[];
}

export interface ChartWidgetProps {
    heading?: string;
    description?: string;
    chartType: 'line' | 'bar' | 'pie' | 'doughnut';
    data: ChartData;
    options?: Record<string, any>;
    height?: number;
    color?: string;
    polling?: {
        enabled: boolean;
        interval?: number;
    };
}

const EMPTY_OPTIONS: Record<string, any> = {};

const getColorPalette = (): string[] => {
    return [
        'rgb(59, 130, 246)', // blue
        'rgb(16, 185, 129)', // green
        'rgb(249, 115, 22)', // orange
        'rgb(239, 68, 68)', // red
        'rgb(168, 85, 247)', // purple
        'rgb(236, 72, 153)', // pink
        'rgb(234, 179, 8)', // yellow
        'rgb(14, 165, 233)', // cyan
    ];
};

const GRID_STEPS = [1, 2, 3, 4, 5];

export default function ChartWidget({ heading, description, chartType, data, options = EMPTY_OPTIONS, height = 300 }: ChartWidgetProps) {
    const chartHeight = `${height}px`;

    // Line Chart
    const renderLineChart = useMemo(() => {
        if (chartType !== 'line') {
            return null;
        }

        const { labels, datasets } = data;

        if (!labels.length || !datasets.length) {
            return null;
        }

        const width = 100;
        const chartBoxHeight = 100;
        const padding = 10;

        const allValues = datasets.flatMap((d) => d.data);
        const max = Math.max(...allValues, 0);
        const min = Math.min(...allValues, 0);
        const range = max - min || 1;

        const xStep = (width - 2 * padding) / (labels.length - 1 || 1);

        return datasets.map((dataset, datasetIndex) => {
            const points = dataset.data
                .map((value, index) => {
                    const x = padding + index * xStep;
                    const y = chartBoxHeight - padding - ((value - min) / range) * (chartBoxHeight - 2 * padding);

                    return `${x},${y}`;
                })
                .join(' ');

            const color = dataset.borderColor || getColorPalette()[datasetIndex % getColorPalette().length];
            const tension = options.tension ?? 0.4;

            return {
                points,
                color,
                label: dataset.label,
                fill: dataset.fill ?? options.fill ?? false,
                tension,
            };
        });
    }, [chartType, data, options]);

    // Bar Chart
    const renderBarChart = useMemo(() => {
        if (chartType !== 'bar') {
            return null;
        }

        const { labels, datasets } = data;

        if (!labels.length || !datasets.length) {
            return null;
        }

        const allValues = datasets.flatMap((d) => d.data);
        const max = Math.max(...allValues, 0);
        const range = max || 1;

        const barWidth = 100 / (labels.length * datasets.length + labels.length);
        const groupGap = barWidth;

        return {
            labels,
            datasets: datasets.map((dataset, datasetIndex) => {
                const background = dataset.backgroundColor;
                const color =
                    (Array.isArray(background) ? background[0] : background) ||
                    getColorPalette()[datasetIndex % getColorPalette().length];

                return {
                    label: dataset.label,
                    color,
                    bars: dataset.data.map((value, index) => {
                        const barHeight = (value / range) * 100;
                        const x = index * (barWidth * datasets.length + groupGap) + datasetIndex * barWidth;

                        return {
                            x,
                            width: barWidth - 1,
                            height: barHeight,
                            value,
                            // An array of colors assigns one color per bar (Chart.js convention).
                            color:
                                Array.isArray(background) && background.length > 0
                                    ? background[index % background.length]
                                    : color,
                        };
                    }),
                };
            }),
        };
    }, [chartType, data]);

    // Pie/Doughnut Chart
    const renderPieChart = useMemo(() => {
        if (chartType !== 'pie' && chartType !== 'doughnut') {
            return null;
        }

        const { labels, datasets } = data;

        if (!labels.length || !datasets.length || !datasets[0].data.length) {
            return null;
        }

        const dataset = datasets[0];
        const total = dataset.data.reduce((sum, val) => sum + val, 0);

        if (total === 0) {
            return null;
        }

        const colors = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor : getColorPalette();

        let currentAngle = -90; // Start from top

        return {
            slices: dataset.data.map((value, index) => {
                const percentage = (value / total) * 100;
                const angle = (value / total) * 360;
                const startAngle = currentAngle;
                const endAngle = currentAngle + angle;

                currentAngle += angle;

                // Convert to radians
                const startRad = (startAngle * Math.PI) / 180;
                const endRad = (endAngle * Math.PI) / 180;

                const x1 = 50 + 45 * Math.cos(startRad);
                const y1 = 50 + 45 * Math.sin(startRad);
                const x2 = 50 + 45 * Math.cos(endRad);
                const y2 = 50 + 45 * Math.sin(endRad);

                const largeArc = angle > 180 ? 1 : 0;

                const path =
                    chartType === 'doughnut'
                        ? `M ${50 + 25 * Math.cos(startRad)} ${50 + 25 * Math.sin(startRad)} L ${x1} ${y1} A 45 45 0 ${largeArc} 1 ${x2} ${y2} L ${50 + 25 * Math.cos(endRad)} ${50 + 25 * Math.sin(endRad)} A 25 25 0 ${largeArc} 0 ${50 + 25 * Math.cos(startRad)} ${50 + 25 * Math.sin(startRad)} Z`
                        : `M 50 50 L ${x1} ${y1} A 45 45 0 ${largeArc} 1 ${x2} ${y2} Z`;

                return {
                    path,
                    color: colors[index % colors.length],
                    label: labels[index],
                    value,
                    percentage: percentage.toFixed(1),
                };
            }),
        };
    }, [chartType, data]);

    let body;

    if (chartType === 'line' && renderLineChart) {
        /* Line Chart */
        body = (
            <div style={{ height: chartHeight }} className="relative">
                <svg className="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid meet">
                    {/* Grid lines */}
                    {options.showGrid !== false && (
                        <g className="opacity-10">
                            {GRID_STEPS.map((i) => (
                                <line
                                    key={`h-${i}`}
                                    x1="10"
                                    y1={10 + i * 18}
                                    x2="90"
                                    y2={10 + i * 18}
                                    stroke="currentColor"
                                    strokeWidth="0.2"
                                />
                            ))}
                            {GRID_STEPS.map((i) => (
                                <line
                                    key={`v-${i}`}
                                    x1={10 + i * 16}
                                    y1="10"
                                    x2={10 + i * 16}
                                    y2="90"
                                    stroke="currentColor"
                                    strokeWidth="0.2"
                                />
                            ))}
                        </g>
                    )}

                    {/* Lines */}
                    {renderLineChart.map((line, index) => (
                        <g key={index}>
                            <polyline
                                points={line.points}
                                fill={line.fill ? line.color : 'none'}
                                fillOpacity={line.fill ? '0.2' : '0'}
                                stroke={line.color}
                                strokeWidth="0.5"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                vectorEffect="non-scaling-stroke"
                            />
                            {options.showPoints !== false &&
                                line.points
                                    .split(' ')
                                    .map((point, pIndex) => (
                                        <circle
                                            key={pIndex}
                                            cx={point.split(',')[0]}
                                            cy={point.split(',')[1]}
                                            r="1"
                                            fill={line.color}
                                        />
                                    ))}
                        </g>
                    ))}
                </svg>
            </div>
        );
    } else if (chartType === 'bar' && renderBarChart) {
        /* Bar Chart */
        body = (
            <div style={{ height: chartHeight }} className="relative">
                <svg className="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid meet">
                    {renderBarChart.datasets.map((dataset, datasetIndex) => (
                        <g key={datasetIndex}>
                            {dataset.bars.map((bar, barIndex) => (
                                <rect
                                    key={barIndex}
                                    x={bar.x}
                                    y={100 - bar.height}
                                    width={bar.width}
                                    height={bar.height}
                                    fill={bar.color}
                                    rx="1"
                                    className="transition-opacity hover:opacity-80"
                                />
                            ))}
                        </g>
                    ))}
                </svg>
            </div>
        );
    } else if ((chartType === 'pie' || chartType === 'doughnut') && renderPieChart) {
        /* Pie/Doughnut Chart */
        body = (
            <div className="flex flex-col lg:flex-row items-center gap-6" style={{ minHeight: chartHeight }}>
                <div className="flex-shrink-0" style={{ width: chartHeight, height: chartHeight }}>
                    <svg className="w-full h-full" viewBox="0 0 100 100">
                        {renderPieChart.slices.map((slice, index) => (
                            <path key={index} d={slice.path} fill={slice.color} className="transition-opacity hover:opacity-80" />
                        ))}
                    </svg>
                </div>

                {/* Legend */}
                {options.showLegend !== false && (
                    <div className="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        {renderPieChart.slices.map((slice, index) => (
                            <div key={index} className="flex items-center gap-2 text-sm">
                                <div className="w-3 h-3 rounded-sm flex-shrink-0" style={{ backgroundColor: slice.color }}></div>
                                <span className="text-muted-foreground truncate">{slice.label}</span>
                                <span className="ml-auto font-medium whitespace-nowrap">
                                    {options.showPercentage ? `${slice.percentage}%` : slice.value}
                                </span>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        );
    } else {
        /* Empty State */
        body = (
            <div className="flex items-center justify-center text-muted-foreground" style={{ height: chartHeight }}>
                <p className="text-sm">No data available</p>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            <div className="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                {(heading || description) && (
                    <div className="px-6 py-4 border-b border-border bg-muted/30">
                        {heading && <h3 className="text-base font-semibold text-foreground">{heading}</h3>}
                        {description && <p className="text-sm text-muted-foreground mt-0.5">{description}</p>}
                    </div>
                )}

                <div className="p-6">{body}</div>
            </div>
        </div>
    );
}

export { ChartWidget };
