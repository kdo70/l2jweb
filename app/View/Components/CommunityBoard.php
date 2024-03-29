<?php

namespace App\View\Components;

use Illuminate\Support\Arr;
use Illuminate\View\Component;

class CommunityBoard extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.community-board.community-board', [
            'actionName' => $this->getActionName()
        ]);
    }

    public function getActionName()
    {
        $route = request()->route();
        if (!empty($route)) {
            return Arr::get(request()->route()->getAction(), 'name');
        }
    }
}
