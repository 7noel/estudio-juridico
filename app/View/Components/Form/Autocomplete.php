<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Autocomplete extends Component
{
    public $name;
    public $label;
    public $value;
    public $text;
    public $id;

    public function __construct(
        $name,
        $label,
        $value = null,
        $text = null,
        $id = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->text = $text;
        $this->id = $id ?? $name;
    }

    public function render()
    {
        return view('components.form.autocomplete');
    }
}