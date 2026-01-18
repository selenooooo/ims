<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('leaveMenuClass')) {
    /**
     * Returns CSS class for leave menu items based on current filter.
     */
    function leaveMenuClass($filter)
    {
        return request('filter') === $filter || ($filter === 'all' && !request('filter'))
            ? 'bg-gray-200 font-semibold'
            : 'text-gray-700 hover:bg-gray-100';
    }
}
