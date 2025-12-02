<?php

namespace Laravilt\Widgets\Mcp;

use Laravel\Mcp\Server;
use Laravilt\Widgets\Mcp\Tools\GenerateWidgetTool;
use Laravilt\Widgets\Mcp\Tools\SearchDocsTool;

class LaraviltWidgetsServer extends Server
{
    protected string $name = 'Laravilt Widgets';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This server provides dashboard widget capabilities for Laravilt projects.

        You can:
        - Generate stats overview widgets
        - Generate chart widgets (line, bar, pie/doughnut)
        - Search widgets documentation
        - Access information about widget types and configuration

        Widgets display dashboard statistics and data visualizations.
    MARKDOWN;

    protected array $tools = [
        GenerateWidgetTool::class,
        SearchDocsTool::class,
    ];
}
