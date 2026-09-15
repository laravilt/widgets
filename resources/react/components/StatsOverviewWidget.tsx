import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

export interface Stat {
    label: string;
    value: string | number;
    description?: string;
    icon?: string;
    color?: string;
    chart?: string;
    chartData?: number[];
    chartColor?: string;
    url?: string;
    descriptionIcon?: boolean;
    descriptionColor?: string;
}

export interface StatsOverviewWidgetProps {
    heading?: string;
    description?: string;
    stats: Stat[];
    columns?: number;
    polling?: {
        enabled: boolean;
        interval?: number;
    };
}

type MiniChart =
    | { type: 'line'; points: string; color: string }
    | { type: 'bar'; data: { height: number; value: number }[]; color: string };

const colsMap: Record<number, string> = {
    1: 'grid-cols-1',
    2: 'grid-cols-1 md:grid-cols-2',
    3: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    5: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-5',
    6: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-6',
};

const getColorClasses = (color?: string): string => {
    if (!color) {
        return 'text-muted-foreground';
    }

    const colorMap: Record<string, string> = {
        primary: 'text-primary',
        success: 'text-green-600 dark:text-green-500',
        danger: 'text-red-600 dark:text-red-500',
        warning: 'text-yellow-600 dark:text-yellow-500',
        info: 'text-blue-600 dark:text-blue-500',
        gray: 'text-gray-600 dark:text-gray-500',
    };

    return colorMap[color] || 'text-muted-foreground';
};

const getDescriptionColorClasses = (color?: string): string => {
    if (!color) {
        return 'text-muted-foreground';
    }

    const colorMap: Record<string, string> = {
        success: 'text-green-600 dark:text-green-500',
        danger: 'text-red-600 dark:text-red-500',
        warning: 'text-yellow-600 dark:text-yellow-500',
        info: 'text-blue-600 dark:text-blue-500',
        gray: 'text-gray-600 dark:text-gray-500',
    };

    return colorMap[color] || 'text-muted-foreground';
};

const renderMiniChart = (stat: Stat): MiniChart | null => {
    if (!stat.chart || !stat.chartData || stat.chartData.length === 0) {
        return null;
    }

    const max = Math.max(...stat.chartData);
    const min = Math.min(...stat.chartData);
    const range = max - min || 1;

    if (stat.chart === 'line') {
        // A single value has no segment to draw (and would divide by zero), so render it as a flat line.
        const values = stat.chartData.length === 1 ? [stat.chartData[0], stat.chartData[0]] : stat.chartData;

        const points = values
            .map((value, index) => {
                const x = (index / (values.length - 1)) * 100;
                const y = 100 - ((value - min) / range) * 100;

                return `${x},${y}`;
            })
            .join(' ');

        return {
            type: 'line',
            points,
            color: stat.chartColor || 'primary',
        };
    }

    if (stat.chart === 'bar') {
        return {
            type: 'bar',
            data: stat.chartData.map((value) => ({
                height: ((value - min) / range) * 100,
                value,
            })),
            color: stat.chartColor || 'primary',
        };
    }

    return null;
};

const getChartColorClasses = (color?: string): { stroke: string; fill: string } => {
    const colorMap: Record<string, { stroke: string; fill: string }> = {
        primary: { stroke: 'stroke-primary', fill: 'fill-primary' },
        success: { stroke: 'stroke-green-600 dark:stroke-green-500', fill: 'fill-green-600 dark:fill-green-500' },
        danger: { stroke: 'stroke-red-600 dark:stroke-red-500', fill: 'fill-red-600 dark:fill-red-500' },
        warning: { stroke: 'stroke-yellow-600 dark:stroke-yellow-500', fill: 'fill-yellow-600 dark:fill-yellow-500' },
        info: { stroke: 'stroke-blue-600 dark:stroke-blue-500', fill: 'fill-blue-600 dark:fill-blue-500' },
        gray: { stroke: 'stroke-gray-600 dark:stroke-gray-500', fill: 'fill-gray-600 dark:fill-gray-500' },
    };

    return colorMap[color || 'primary'] ?? colorMap.primary;
};

export default function StatsOverviewWidget({ heading, description, stats, columns = 3 }: StatsOverviewWidgetProps) {
    const gridCols = colsMap[columns] || colsMap[3];

    return (
        <div className="space-y-4">
            {(heading || description) && (
                <div className="space-y-1">
                    {heading && <h3 className="text-lg font-semibold text-foreground">{heading}</h3>}
                    {description && <p className="text-sm text-muted-foreground">{description}</p>}
                </div>
            )}

            <div className={cn('grid gap-6', gridCols)}>
                {stats.map((stat, index) => {
                    const miniChart = renderMiniChart(stat);

                    const content = (
                        <div className="p-6">
                            {/* Header with Icon and Label */}
                            <div className="flex items-start justify-between gap-3 mb-4">
                                <div className="space-y-1 flex-1 min-w-0">
                                    <p className="text-sm font-medium text-muted-foreground">{stat.label}</p>
                                </div>
                                {stat.icon && !stat.descriptionIcon && (
                                    <div className={cn('flex-shrink-0 p-2 rounded-lg bg-primary/10', getColorClasses(stat.color))}>
                                        <i className={cn(stat.icon, 'text-lg')}></i>
                                    </div>
                                )}
                            </div>

                            {/* Value */}
                            <div className="mb-3">
                                <div className={cn('text-2xl font-semibold tracking-tight', getColorClasses(stat.color))}>
                                    {stat.value}
                                </div>
                            </div>

                            {/* Description or Chart */}
                            {(stat.description || miniChart) && (
                                <div>
                                    {stat.description ? (
                                        <div
                                            className={cn(
                                                'flex items-center gap-1.5 text-xs font-medium',
                                                getDescriptionColorClasses(stat.descriptionColor),
                                            )}
                                        >
                                            {stat.descriptionIcon && stat.icon && <i className={cn(stat.icon, 'text-sm')}></i>}
                                            <span>{stat.description}</span>
                                        </div>
                                    ) : miniChart ? (
                                        /* Mini Chart */
                                        <div className="h-10 -mx-1">
                                            {miniChart.type === 'line' ? (
                                                <svg className="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                                    <polyline
                                                        points={miniChart.points}
                                                        fill="none"
                                                        className={getChartColorClasses(miniChart.color).stroke}
                                                        strokeWidth="3"
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        opacity="0.8"
                                                    />
                                                </svg>
                                            ) : miniChart.type === 'bar' ? (
                                                <div className="flex items-end justify-between h-full gap-1">
                                                    {miniChart.data.map((bar, barIndex) => (
                                                        <div
                                                            key={barIndex}
                                                            className={cn(
                                                                'flex-1 rounded-t transition-all',
                                                                getChartColorClasses(miniChart.color).fill,
                                                            )}
                                                            style={{ height: `${bar.height}%`, opacity: 0.8 }}
                                                        ></div>
                                                    ))}
                                                </div>
                                            ) : null}
                                        </div>
                                    ) : null}
                                </div>
                            )}
                        </div>
                    );

                    const cardClassName =
                        'relative overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all';

                    // Linked cards are real links, so keyboard users can focus and activate them.
                    return stat.url ? (
                        <Link
                            key={index}
                            href={stat.url}
                            className={cn(cardClassName, 'cursor-pointer hover:shadow-md hover:border-primary/50')}
                        >
                            {content}
                        </Link>
                    ) : (
                        <div key={index} className={cardClassName}>
                            {content}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}

export { StatsOverviewWidget };
