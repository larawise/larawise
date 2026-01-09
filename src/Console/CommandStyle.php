<?php

namespace Larawise\Console;

use Illuminate\Console\Command;

class CommandStyle
{
    public function __construct(
        protected Command $command
    ) {}

    /**
     * Displays a styled CLI heading with optional icon and color.
     *
     * @param string $text
     * @param string|null $icon
     * @param string $color
     * @param bool $bold
     *
     * @return void
     */
    public function heading($text, $icon = '📦', $color = 'bright-white', $bold = true)
    {
        $this->command->newLine();

        $style = $bold ? 'options=bold,' : '';
        $prefix = $icon ? "$icon " : '';

        $this->command->line("<$style fg=$color>{$prefix}{$text}</>");
    }

    /**
     * Displays a bullet-point list in CLI output.
     *
     * Each item is prefixed with a styled bullet (•) and optional color.
     *
     * @param array $items
     * @param string $color
     *
     * @return void
     */
    public function list(array $items, string $color = 'bright-white')
    {
        if (empty($items)) {
            $this->command->line($this->dim('No items to display.'));
            return;
        }

        foreach ($items as $item) {
            $this->command->line("<fg=gray>•</> <fg=$color>$item</>");
        }
    }

    /**
     * Returns a styled status indicator for CLI output.
     *
     * Depending on the selected type, this method returns:
     * - a colored icon (✓ or ❌),
     * - a colored text label (e.g. "OK", "FAIL"),
     * - or both combined.
     *
     * @param bool $status
     * @param string $type
     * @param string $success
     * @param string $error
     *
     * @return string
     */
    public function status($status, $type = 'icon', $success = 'OK', $error = 'FAIL')
    {
        $icon = $status ? '<fg=green>✓</>' : '<fg=red>❌</>';
        $text = $status ? "<fg=green>{$success}</>" : "<fg=red>{$error}</>";

        return match ($type) {
            'icon' => $icon,
            'text' => $text,
            'both' => "$icon $text",
            default => $icon,
        };
    }

    /**
     * Returns a styled CLI label with the given foreground color.
     *
     * This method wraps the provided text in Laravel's console color tags,
     * allowing you to display colored output in terminal commands.
     *
     * Common colors include: blue, green, red, yellow, cyan, magenta, gray, bright-white.
     *
     * @param string $text
     * @param string $color
     *
     * @return string
     */
    public function label($text, $color = 'blue', $bold = false)
    {
        $style = $bold ? 'options=bold,' : '';
        return "<$style fg=$color>$text</>";
    }

    /**
     * Returns the given text styled in a dim gray color for CLI output.
     *
     * Useful for displaying secondary or less prominent information,
     * such as file paths, inferred values, or debug notes.
     *
     * @param string $text
     *
     * @return string
     */
    public function dim($text)
    {
        return "<fg=gray>$text</>";
    }
}
