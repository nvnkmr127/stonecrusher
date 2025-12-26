<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $type;
    public bool $dismissible;

    /**
     * Create a new component instance.
     */
    public function __construct(string $type = 'info', bool $dismissible = false)
    {
        $this->type = $type;
        $this->dismissible = $dismissible;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alert');
    }

    public function alertClass(): string
    {
        return match ($this->type) {
            'success' => 'alert-success',
            'danger', 'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-info',
        };
    }
}
