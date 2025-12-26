<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;
    public string $size;
    public string $type;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $variant = 'primary',
        string $size = 'md',
        string $type = 'button'
    ) {
        $this->variant = $variant;
        $this->size = $size;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }

    public function buttonClass(): string
    {
        $classes = ['btn', "btn-{$this->variant}"];

        if ($this->size !== 'md') {
            $classes[] = "btn-{$this->size}";
        }

        return implode(' ', $classes);
    }
}
