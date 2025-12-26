<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public string $name;
    public string $label;
    public string $type;
    public mixed $value;
    public ?string $placeholder;
    public bool $required;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label,
        string $type = 'text',
        mixed $value = null,
        ?string $placeholder = null,
        bool $required = false
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->value = $value ?? old($name);
        $this->placeholder = $placeholder ?? $label;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.input');
    }
}
