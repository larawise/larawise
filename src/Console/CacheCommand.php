<?php

namespace Larawise\Console;

use Illuminate\Console\Command;
use Larawise\Facades\Service\CacheService as Cache;
use Larawise\Service\CacheService;
use Larawise\Support\Enums\TTL;
use Symfony\Component\Console\Command\Command as CommandAlias;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Core
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 */
class CacheCommand extends Command
{
    protected $signature = 'larawise:cache
        {action : flush|get|put|forget|remember|inspect|macro}
        {key?}
        {--group=}
        {--value=}
        {--ttl=}
        {--match=}
        {--exclude=}
        {--sliding}
        {--track}
        {--encrypt}
        {--compress}
        {--json}
        {--dry-run}
        {--macro=}';

    protected $description = 'Unified cache command for Larawise CacheService';

    protected CacheService $cache;
    protected array $inspect = [];

    public function handle(): int
    {
        $this->prepareService();

        return match ($this->action()) {
            'flush'    => $this->handleFlush(),
            'get'      => $this->handleGet(),
            'put'      => $this->handlePut(),
            'forget'   => $this->handleForget(),
            'remember' => $this->handleRemember(),
            'inspect'  => $this->handleInspect(),
            'macro'    => $this->handleMacro(),
            default    => $this->error("Unknown action: {$this->action()}") & CommandAlias::FAILURE,
        };
    }

    protected function prepareService(): void
    {
        $this->cache = Cache::group($this->group());

        if ($this->option('match'))    $this->cache->match($this->option('match'));
        if ($this->option('exclude'))  $this->cache->exclude($this->option('exclude'));
        if ($this->option('ttl'))      $this->cache->ttl($this->resolveTTL($this->option('ttl')));
        if ($this->option('sliding'))  $this->cache->sliding();
        if ($this->option('track'))    $this->cache->track();
        if ($this->option('encrypt'))  $this->cache->encrypt();
        if ($this->option('compress')) $this->cache->compress();

        $this->inspect = $this->cache->inspect($this->option('verbose'));
    }

    protected function resolveTTL(string $ttl): mixed
    {
        return defined(TTL::class . "::{$ttl}")
            ? constant(TTL::class . "::{$ttl}")
            : (is_numeric($ttl) ? (int) $ttl : null);
    }

    protected function handleFlush(): int
    {
        if ($this->option('dry-run')) {
            $this->warn("[Dry Run] Would flush group [{$this->group()}]");
            return CommandAlias::SUCCESS;
        }

        $count = $this->cache->flush();
        $this->info("✅ Flushed {$count} keys.");
        return CommandAlias::SUCCESS;
    }

    protected function handleGet(): int
    {
        $result = $this->cache->get($this->key());

        if ($this->option('json')) {
            return $this->json($result);
        }

        return $this->detail([
            'Key'   => $this->key(),
            'Value' => $this->stringify($result),
        ]);
    }

    protected function handlePut(): int
    {
        $success = $this->cache->put($this->key(), $this->option('value'));
        $this->info($success ? "✅ Stored [{$this->key()}]" : "❌ Failed to store [{$this->key()}]");
        return CommandAlias::SUCCESS;
    }

    protected function handleForget(): int
    {
        $success = $this->cache->forget($this->key());
        $this->info($success ? "🗑️ Forgot [{$this->key()}]" : "⚠️ Key not found or excluded");
        return CommandAlias::SUCCESS;
    }

    protected function handleRemember(): int
    {
        $ttl = $this->inspect[$this->key()]['ttl'] ?? null;

        $result = $this->cache->remember($this->key(), $ttl, fn() => $this->option('value'));

        $this->info("🧠 Remembered [{$this->key()}] → " . $this->stringify($result));
        return CommandAlias::SUCCESS;
    }

    protected function handleInspect(): int
    {
        if (empty($this->inspect)) {
            $this->warn("⚠️ No tracked keys.");
            return CommandAlias::SUCCESS;
        }

        return $this->option('json')
            ? $this->json($this->inspect)
            : $this->table(['Key', 'TTL', 'Size', 'Last Accessed'], $this->formatInspect($this->inspect));
    }

    protected function handleMacro(): int
    {
        $macro = $this->option('macro');

        if (! Cache::hasMacro($macro)) {
            $this->error("❌ Macro [{$macro}] not found.");
            return CommandAlias::FAILURE;
        }

        Cache::$macro();
        $this->info("✅ Macro [{$macro}] executed.");
        return CommandAlias::SUCCESS;
    }

    protected function key(): ?string     { return $this->argument('key'); }
    protected function group(): string    { return $this->option('group') ?? 'default'; }
    protected function action(): string   { return $this->argument('action'); }

    protected function json(mixed $data): int
    {
        $this->line(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return CommandAlias::SUCCESS;
    }

    protected function detail(array $data): int
    {
        foreach ($data as $label => $value) {
            $this->components->twoColumnDetail($label, $this->stringify($value));
        }

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }

    protected function formatInspect(array $keys): array
    {
        return collect($keys)->map(fn($meta, $key) => [
            $key,
            $meta['ttl_label'] ?? '—',
            $meta['size'] ?? '—',
            $meta['last_accessed'] ?? '—',
        ])->toArray();
    }

    protected function stringify(mixed $value): string
    {
        return match (true) {
            is_null($value)      => 'null',
            is_bool($value)      => $value ? 'true' : 'false',
            is_scalar($value)    => (string) $value,
            default              => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        };
    }
}
