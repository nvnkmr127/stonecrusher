<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public string $name;
    public string $label;
    public mixed $value;
    public ?string $placeholder;
    public bool $required;
    public int $rows;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label,
        mixed $value = null,
        ?string $placeholder = null,
        bool $required = false,
        int $rows = 3
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value ?? old($name);
        $this->placeholder = $placeholder ?? $label;
        $this->required = $required;
        $this->rows = $rows;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.textarea');
    }
}
