<?php

namespace Laravilt\Widgets\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GenerateWidgetTool extends Tool
{
    protected string $description = 'Generate a new widget class for dashboard statistics or charts';

    public function handle(Request $request): Response
    {
        $name = $request->string('name');

        $command = 'php '.base_path('artisan').' make:widget "'.$name.'" --no-interaction';

        if ($request->boolean('stats', false)) {
            $command .= ' --stats';
        }

        if ($request->string('chart')) {
            $command .= ' --chart='.$request->string('chart');
        }

        if ($request->boolean('force', false)) {
            $command .= ' --force';
        }

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $response = "✅ Widget '{$name}' created successfully!\n\n";
            $response .= "📖 Location: app/Widgets/{$name}.php\n\n";
            $response .= "📦 Widget types: Stats Overview, Line Chart, Bar Chart, Pie/Doughnut Chart\n";

            return Response::text($response);
        } else {
            return Response::text('❌ Failed to create widget: '.implode("\n", $output));
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Widget class name in StudlyCase (e.g., "SalesChartWidget")')
                ->required(),
            'stats' => $schema->boolean()
                ->description('Generate a stats overview widget')
                ->default(false),
            'chart' => $schema->string()
                ->description('Generate a chart widget: line, bar, or pie')
                ->enum(['line', 'bar', 'pie']),
            'force' => $schema->boolean()
                ->description('Overwrite existing file')
                ->default(false),
        ];
    }
}
