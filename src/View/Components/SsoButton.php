<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\View\Components;

use Packages\LaravelSSO\Models\Partner;
use Illuminate\View\Component;

/**
 * SSO Button Component
 *
 * Blade component for SSO login buttons.
 */
class SsoButton extends Component
{
    public Partner $partner;
    public string $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $partner,
        string $class = ''
    ) {
        if (is_string($partner)) {
            $partner = Partner::findByIdentifier($partner);
        }

        if (!$partner || !$partner->enabled) {
            throw new \InvalidArgumentException('Invalid or disabled partner specified.');
        }

        $this->partner = $partner;
        $this->class = $class ?: 'inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('laravel-sso::components.sso-button');
    }
}
