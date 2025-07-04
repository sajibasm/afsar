<?php

namespace app\components;

use yii\helpers\Html;

class ButtonGroupHelper
{
    /**
     * Render a flexible, responsive button group.
     * @param array $buttons Array of rendered buttons (use ButtonHelper::button)
     * @param array $options Optional layout config:
     *        - 'class' => wrapper class (default: 'row')
     *        - 'inline' => true for side-by-side, false for stacked (default: false)
     *        - 'spacingClass' => spacing class for each button (default: 'mb-2')
     * @return string
     */
    public static function buttonGroup(array $buttons, $options = [])
    {
        $defaults = [
            'class' => 'row',
            'inline' => false,                          // false = block, true = inline
            'spacingClass' => 'mb-2',                   // bottom margin by default
        ];

        $config = array_merge($defaults, $options);

        $html = Html::beginTag('div', ['class' => $config['class']]);

        $buttonCount = count($buttons);
        $colSize = 12;  // Bootstrap grid is always /12

        // Auto calculate column width based on button count (max 4 cols)
        if ($buttonCount > 0) {
            $colWidth = max(12 / min($buttonCount, 4), 3); // Prevent col-md-0
        } else {
            $colWidth = 12;
        }

        foreach ($buttons as $btnHtml) {
            $colClass = $config['inline'] ? '' : "col-md-{$colWidth} col-sm-6 col-xs-12";
            $spacing = $config['spacingClass'];

            $html .= Html::tag('div', $btnHtml, [
                'class' => trim("$colClass $spacing")
            ]);
        }

        $html .= Html::endTag('div');

        return $html;
    }
}
