<?php

namespace Larawise\Support\Enums;

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
enum TTL: int
{
    /**
     * 1 minute (60 seconds) — for very short-lived cache entries.
     */
    case SHORT = 60;

    /**
     * 10 minutes (600 seconds) — suitable for temporary UI state or ephemeral data.
     */
    case MEDIUM = 600;

    /**
     * 1 hour (3600 seconds) — good for session-level or hourly refresh data.
     */
    case LONG = 3600;

    /**
     * 1 day (86400 seconds) — ideal for daily cache cycles.
     */
    case DAY = 86400;

    /**
     * 1 week (604800 seconds) — useful for weekly reports or batch jobs.
     */
    case WEEK = 604800;

    /**
     * 1 month (2592000 seconds) — for monthly summaries or stable data.
     */
    case MONTH = 2592000;

    /**
     * 1 year (31536000 seconds) — for long-term cache like config snapshots.
     */
    case YEAR = 31536000;

    /**
     * Forever (-1) — disables expiration entirely.
     */
    case FOREVER = -1;
}
