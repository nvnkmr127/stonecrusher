<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    public string $name;
    public string $label;
    public bool $checked;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label,
        bool $checked = false
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->checked = $checked || old($name);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.checkbox');
    }
}
