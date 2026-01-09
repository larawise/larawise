<?php

namespace Larawise\Concerns;

use Larawise\Support\Enums\Framework;

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
trait InteractsWithFramework
{
    /**
     * Inertia.js front-end framework status.
     *
     * @var bool
     */
    protected $useNative = true;

    /**
     * Inertia.js front-end framework status.
     *
     * @var bool
     */
    protected $useInertia = false;

    /**
     * React.js front-end framework status.
     *
     * @var bool
     */
    protected $useReact = false;

    /**
     * Vue.js front-end framework status.
     *
     * @var bool
     */
    protected $useVue = false;

    /**
     * Livewire front-end framework status.
     *
     * @var bool
     */
    protected $useLivewire = false;

    /**
     * Check if `Inertia` is the active front-end framework.
     *
     * @return bool
     */
    public function isInertia()
    {
        return $this->isFramework(Framework::INERTIA);
    }

    /**
     * Check if `React.js` is the active front-end framework.
     *
     * @return bool
     */
    public function isReact()
    {
        return $this->isFramework(Framework::REACT);
    }

    /**
     * Check if `Vue.js` is the active front-end framework.
     *
     * @return bool
     */
    public function isVue()
    {
        return $this->isFramework(Framework::VUE);
    }

    /**
     * Check if `Livewire` is the active front-end framework.
     *
     * @return bool
     */
    public function isLivewire()
    {
        return $this->isFramework(Framework::LIVEWIRE);
    }

    /**
     * Check if a specific framework is active, or return the current one.
     *
     * @param Framework $framework
     *
     * @return bool
     */
    public function isFramework($framework = Framework::NATIVE)
    {
        return match ($framework) {
            Framework::INERTIA  => $this->useInertia,
            Framework::REACT    => $this->useReact,
            Framework::VUE      => $this->useVue,
            Framework::LIVEWIRE => $this->useLivewire,
            Framework::NATIVE   => !($this->useInertia || $this->useReact || $this->useVue || $this->useLivewire),
        };
    }

    /**
     * Check if `Native` is the active front-end framework.
     *
     * @return bool
     */
    public function isNative()
    {
        return $this->currentFramework() === Framework::NATIVE;
    }

    /**
     * Use front-end framework as `Inertia`.
     *
     * @return void
     */
    public function useInertia()
    {
        $this->useFramework(Framework::INERTIA);
    }

    /**
     * Use front-end framework as `React.js`.
     *
     * @return void
     */
    public function useReact()
    {
        $this->useFramework(Framework::REACT);
    }

    /**
     * Use front-end framework as `Vue.js`.
     *
     * @return void
     */
    public function useVue()
    {
        $this->useFramework(Framework::VUE);
    }

    /**
     * Use front-end framework as `Livewire`.
     *
     * @return void
     */
    public function useLivewire()
    {
        $this->useFramework(Framework::LIVEWIRE);
    }

    /**
     * Set the active front-end framework.
     *
     * @param Framework $framework
     *
     * @return bool
     */
    public function useFramework($framework)
    {
        // Reset all flags
        $this->useNative = false;
        $this->useInertia = false;
        $this->useReact = false;
        $this->useVue = false;
        $this->useLivewire = false;

        // Activate the selected framework
        return match ($framework) {
            Framework::INERTIA => $this->useInertia = true,
            Framework::REACT => $this->useReact = true,
            Framework::VUE => $this->useVue = true,
            Framework::LIVEWIRE => $this->useLivewire = true,
            Framework::NATIVE => $this->useNative = true,
        };
    }

    /**
     * Get the currently active front-end framework.
     *
     * @return Framework
     */
    public function currentFramework()
    {
        return match (true) {
            $this->useInertia => Framework::INERTIA,
            $this->useReact => Framework::REACT,
            $this->useVue => Framework::VUE,
            $this->useLivewire => Framework::LIVEWIRE,
            $this->useNative => Framework::NATIVE,
        };
    }
}
