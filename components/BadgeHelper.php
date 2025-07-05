<?php

namespace app\components;

class BadgeHelper
{
    // Centralized color mappings
    protected static $colorMap = [
        // Statuses
        'active' => ['label' => 'Active', 'color' => '#28a745'],         // Green
        'inactive' => ['label' => 'Inactive', 'color' => '#6c757d'],     // Gray
        'pending' => ['label' => 'Pending', 'color' => '#ffc107'],       // Yellow
        'hold' => ['label' => 'Pending', 'color' => '#ffc107'],          // Yellow
        'reject' => ['label' => 'Rejected', 'color' => '#dc3545'],       // Red
        'approved' => ['label' => 'Approved', 'color' => '#007bff'],     // Blue
        'declined' => ['label' => 'Declined', 'color' => '#dc3545'],     // Red
        'delete' => ['label' => 'Deleted', 'color' => '#343a40'],        // Dark Gray
        'regular' => ['label' => 'Regular', 'color' => '#28a745'],       // Green
        'irregular' => ['label' => 'Irregular', 'color' => '#e83e8c'],   // Pink/Red

        // Types
        'transfer' => ['label' => 'Transfer', 'color' => '#17a2b8'],
        'received' => ['label' => 'Received', 'color' => '#28a745'],
        'movement' => ['label' => 'Movement', 'color' => '#ffc107'],
        'sales' => ['label' => 'Sales', 'color' => '#007bff'],

        'sales-update' => ['label' => 'Sales Update', 'color' => '#6f42c1'],
        'sales update' => ['label' => 'Sales Update', 'color' => '#6f42c1'],

        'sales-return' => ['label' => 'Sales Return', 'color' => '#fd7e14'],
        'sales return' => ['label' => 'Sales Return', 'color' => '#fd7e14'],

        'sales delete' => ['label' => 'Sales Delete', 'color' => '#dc3545'],
        'sales-delete' => ['label' => 'Sales Delete', 'color' => '#dc3545'],

        'due-received' => ['label' => 'Due Received', 'color' => '#28a745'],      // Green
        'advanced' => ['label' => 'Advanced', 'color' => '#6610f2'],

        // ProductStock Types
        'local'     => ['label' => 'Local', 'color' => '#007bff'],      // Blue
        'import'    => ['label' => 'Import', 'color' => '#6610f2'],     // Purple

        '2fa-enabled' => [
            'label' => '<i class="fas fa-lock"></i>',
            'color' => '#28a745', // Green
        ],

        '2fa-disabled' => [
            'label' => '<i class="fas fa-unlock-alt"></i>',
            'color' => '#ffc107', // Yellow
        ],

        // User Statuses
        '10' => ['label' => 'Active', 'color' => '#28a745'],      // Green
        '1'  => ['label' => 'Inactive', 'color' => '#6c757d'],    // Gray
        '2'  => ['label' => 'Suspended', 'color' => '#dc3545'],   // Red


    ];


    /**
     * Generate a colored badge for a value.
     *
     * @param string|int $value The raw status/type/key
     * @param string|null $customLabel Optional label override
     * @param string|null $customColor Optional color override
     * @param string $defaultLabel Label if value not found
     * @param string $defaultColor Fallback color
     * @return string
     */
    public static function render($value, $customLabel = null, $customColor = null, $defaultLabel = 'Unknown', $defaultColor = '#adb5bd')
    {
        $key = strtolower($value);

        if (isset(self::$colorMap[$key])) {
            $label = $customLabel ?: self::$colorMap[$key]['label'];
            $color = $customColor ?: self::$colorMap[$key]['color'];
        } else {
            $label = $customLabel ?: ucfirst($value);
            $color = $customColor ?: $defaultColor;
        }

        return '<span class="badge" style="background-color: ' . $color . '; color: #fff;">' . $label . '</span>';
    }
}
