<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchableDropdown extends Component
{
    public $name;
    public $label;
    public $options;
    public $valueField;
    public $labelField;
    public $groupField;
    public $selected;
    public $placeholder;
    public $emptyOption;
    public $required;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $name,
        $options,
        $label = null,
        $valueField = 'id',
        $labelField = 'name',
        $groupField = null,
        $selected = null,
        $placeholder = 'Cari...',
        $emptyOption = null,
        $required = false
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->valueField = $valueField;
        $this->labelField = $labelField;
        $this->groupField = $groupField;
        $this->selected = $selected;
        $this->placeholder = $placeholder;
        $this->emptyOption = $emptyOption;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.searchable-dropdown');
    }
}
