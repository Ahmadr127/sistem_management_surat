<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TableFilter extends Component
{
    public $searchPlaceholder;
    public $label;

    /**
     * Create a new component instance.
     */
    public function __construct($searchPlaceholder = 'Cari...', $label = 'Cari')
    {
        $this->searchPlaceholder = $searchPlaceholder;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.table-filter');
    }
}
