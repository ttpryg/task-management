<?php

if (!function_exists('getCategoryColor')) {
    /**
     * Get color scheme for a category based on its ID
     * 
     * @param int $categoryId The category ID
     * @return array Array containing bg (background), text, and icon color classes
     */
    function getCategoryColor($categoryId) {
        // Define a set of color combinations
        $colors = [
            [
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-800',
                'icon' => 'text-blue-500'
            ],
            [
                'bg' => 'bg-green-100',
                'text' => 'text-green-800',
                'icon' => 'text-green-500'
            ],
            [
                'bg' => 'bg-purple-100',
                'text' => 'text-purple-800',
                'icon' => 'text-purple-500'
            ],
            [
                'bg' => 'bg-yellow-100',
                'text' => 'text-yellow-800',
                'icon' => 'text-yellow-500'
            ],
            [
                'bg' => 'bg-pink-100',
                'text' => 'text-pink-800',
                'icon' => 'text-pink-500'
            ],
            [
                'bg' => 'bg-indigo-100',
                'text' => 'text-indigo-800',
                'icon' => 'text-indigo-500'
            ],
            [
                'bg' => 'bg-red-100',
                'text' => 'text-red-800',
                'icon' => 'text-red-500'
            ],
            [
                'bg' => 'bg-teal-100',
                'text' => 'text-teal-800',
                'icon' => 'text-teal-500'
            ]
        ];

        // Use modulo to ensure we always get a valid index, even if categoryId is large
        $colorIndex = ($categoryId - 1) % count($colors);
        return $colors[$colorIndex];
    }
} 