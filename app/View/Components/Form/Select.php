<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $name;
    public string $label;
    public array $options;
    public mixed $selected;
    public bool $required;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label,
        array $options = [],
        mixed $selected = null,
        bool $required = false
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = $selected ?? old($name);
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
