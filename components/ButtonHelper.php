<?php
namespace app\components;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class ButtonHelper
{

    /**
     * @param $type
     * @param $url
     * @param $options
     * @return string
     */
    public static function actionButton($type, $url = '#', $options = []){
        $defaults = [
            'class' => '',               // Additional custom classes
            'data-pjax' => 0,
            'title' => '',
            'target' => false,
            'disabled' => false,
            'icon' => '',                // Optional override icon
            'value' => null,             // For modal buttons
            'confirm' => false,          // Use SweetAlert2 confirmation
            'confirmTitle' => 'Are you sure?',
            'confirmText' => 'This action cannot be undone.',
            'confirmButton' => 'Yes, proceed!',
            'cancelButton' => 'Cancel',
        ];

        $config = array_merge($defaults, $options);

        // Predefined button types
        $buttonTypes = [
            'approve' => [
                'icon' => '<span class="fas fa-check"></span>',
                'class' => 'btn btn-success btn-xs',
                'title' => Yii::t('app', 'Approve'),
            ],
            'update' => [
                'icon' => '<span class="fas fa-pen"></span>',
                'class' => 'btn btn-warning btn-xs',
                'title' => Yii::t('app', 'Update'),
            ],
            'view' => [
                'icon' => '<span class="fas fa-eye"></span>',
                'class' => 'btn btn-default btn-xs',
                'title' => Yii::t('app', 'View'),
            ],
            'store' => [
                'icon' => '<span class="fas fa-store"></span>',
                'class' => 'btn btn-info btn-xs',
                'title' => Yii::t('app', 'Assign To Store'),
            ],
            'print' => [
                'icon' => '<span class="fas fa-print"></span>',
                'class' => 'btn btn-success btn-xs',
                'title' => Yii::t('app', 'Print Invoice'),
                'target' => '_blank',
            ],
            'delete' => [
                'icon' => '<span class="fas fa-trash"></span>',
                'class' => 'btn btn-danger btn-xs',
                'title' => Yii::t('app', 'Delete'),
            ],
            'restore' => [  // ✅ Your new button type
                'icon' => '<span class="fas fa-undo"></span>',
                'class' => 'btn btn-primary btn-xs',
                'title' => Yii::t('app', 'Restore'),
            ],
            'details' => [
                'icon' => '<span class="fas fa-box-open"></span>',
                'class' => 'btn btn-primary btn-xs modalUpdateBtn',
                'title' => Yii::t('app', 'Item Details'),
            ],
            'pay' => [
                'icon' => '<span class="glyphicon glyphicon-import"></span>',
                'class' => 'btn btn-success btn-xs',
                'title' => Yii::t('app', 'Pay Invoice'),
            ],
            'payment' => [
                'icon' => '<span class="fas fa-credit-card"></span>',
                'class' => 'btn btn-success btn-xs modalUpdateBtn',
                'title' => Yii::t('app', 'Payment Details'),
            ],
            'notification' => [
                'icon' => '<span class="fas fa-paper-plane"></span>',
                'class' => 'btn btn-primary btn-xs modalUpdateBtn',
                'title' => Yii::t('app', 'Notification'),
            ],
            'withdraw' => [
                'icon' => '<span class="glyphicon glyphicon-export"></span>',
                'class' => 'btn btn-danger btn-xs',
                'title' => Yii::t('app', 'Withdraw'),
            ],
            'transfer' => [
                'icon' => '<span class="fas fa-truck-loading"></span>',
                'class' => 'btn btn-default btn-xs',
                'title' => Yii::t('app', 'Transfer to Store'),
            ],
            'reject' => [
                'icon' => '<span class="fas fa-times-circle"></span>',
                'class' => 'btn btn-danger btn-xs',
                'title' => Yii::t('app', 'Transfer to Store'),
            ],
        ];

        // Use button type or empty fallback
        $btn = $buttonTypes[$type] ?? [];

        // Always ensure icon exists (fallback to question icon)
        $btn['icon'] = $config['icon'] ?: ($btn['icon'] ?? '<span class="fas fa-question"></span>');

        // Title fallback
        $btn['title'] = $config['title'] ?: ($btn['title'] ?? '');

        // Merge classes
        $btn['class'] = trim(($btn['class'] ?? 'btn btn-default btn-xs') . ' ' . $config['class']);

        // Disabled?
        if (!empty($config['disabled'])) {
            $btn['class'] .= ' disabled';
        }

        // Add SweetAlert2 confirmation if requested
        if (!empty($config['confirm'])) {
            $btn['class'] .= ' btn-confirm';
            $btn['data-confirm-title'] = $config['confirmTitle'];
            $btn['data-confirm-text'] = $config['confirmText'];
            $btn['data-confirm-button'] = $config['confirmButton'];
            $btn['data-cancel-button'] = $config['cancelButton'];
        }

        // Build common options for Html::a or Html::button
        $commonOptions = [
            'class' => $btn['class'],
            'title' => $btn['title'],
            'data-pjax' => $config['data-pjax'],
        ];

        // Add confirmation attributes
        if (!empty($btn['data-confirm-title'])) {
            $commonOptions['data-confirm-title'] = $btn['data-confirm-title'];
            $commonOptions['data-confirm-text'] = $btn['data-confirm-text'];
            $commonOptions['data-confirm-button'] = $btn['data-confirm-button'];
            $commonOptions['data-cancel-button'] = $btn['data-cancel-button'];
        }

        // Add target if external
        if (!empty($btn['target'])) {
            $commonOptions['target'] = $btn['target'];
        }


        if (!empty($config['url'])) {
            $commonOptions['data-url'] = $config['url'];
        }

        // Add data-method if provided (e.g., 'post' for delete)
        if (!empty($config['data-method'])) {
            $commonOptions['data-method'] = $config['data-method'];
        }

        // Add confirmation AJAX flag
        if (isset($config['confirmAjax'])) {
            $commonOptions['data-confirm-ajax'] = $config['confirmAjax'];
        }

        // Add PJAX container ID
        if (!empty($config['pjaxId'])) {
            $commonOptions['data-pjax-id'] = $config['pjaxId'];
        }

        // Modal button case
        if ($config['value'] !== null) {
            $commonOptions['value'] = $config['value'];
            return Html::button($btn['icon'], $commonOptions);
        }

        // Normal anchor button
        return Html::a($btn['icon'], $url, $commonOptions);
    }

    /**
     * @param $label
     * @param $options
     * @return string
     */
    public static function button($label, $options = [])
    {
        $defaults = [
            'type' => 'submit',          // submit, reset, link, button
            'url' => '#',
            'id' => null,
            'block' => false,
            'flat' => false,
            'confirm' => false,
            'confirmTitle' => 'Are you sure?',
            'confirmText' => 'Do you want to proceed?',
            'confirmButton' => 'Yes',
            'cancelButton' => 'Cancel',
            'pjax' => null,              // ⚠️ Set to null by default → not rendered unless provided
            'icon' => '',
            'class' => '',
        ];

        $config = array_merge($defaults, $options);

        // Set default icon & class
        switch (strtolower($config['type'])) {
            case 'submit':
                $defaultIcon = '<i class="fas fa-save"></i>';
                $defaultClass = 'btn btn-primary btn-block btn-flat';
                break;
            case 'reset':
                $defaultIcon = '<i class="fas fa-sync-alt"></i>';
                $defaultClass = 'btn btn-default';
                break;
            case 'link':
                $defaultIcon = '<i class="fas fa-times-circle"></i>';
                $defaultClass = 'btn btn-default btn-block btn-flat';
                break;
            case 'button':
            default:
                $defaultIcon = '<i class="fas fa-circle"></i>';
                $defaultClass = 'btn btn-secondary';
                break;
        }

        $icon = $config['icon'] ?: $defaultIcon;
        $class = $config['class'] ?: $defaultClass;

        if (!empty($config['block']) && strpos($class, 'btn-block') === false) {
            $class .= ' btn-block';
        }
        if (!empty($config['flat']) && strpos($class, 'btn-flat') === false) {
            $class .= ' btn-flat';
        }

        $finalLabel = $icon . ' ' . $label;

        $htmlOptions = [
            'class' => $class,
            'id' => $config['id'],
        ];

        // Only add data-pjax if explicitly set
        if ($config['pjax'] !== null) {
            $htmlOptions['data-pjax'] = $config['pjax'];
        }

        // Confirm block
        if (!empty($config['confirm'])) {
            $htmlOptions['class'] .= ' btn-confirm';
            $htmlOptions['data-url'] = Url::to($config['url']);
            $htmlOptions['data-confirm-title'] = $config['confirmTitle'];
            $htmlOptions['data-confirm-text'] = $config['confirmText'];
            $htmlOptions['data-confirm-button'] = $config['confirmButton'];
            $htmlOptions['data-cancel-button'] = $config['cancelButton'];
            $config['url'] = '#';
        } elseif ($config['type'] === 'link' && !empty($config['confirmText'])) {
            $htmlOptions['onclick'] = "return confirm('{$config['confirmText']}');";
        }

        switch (strtolower($config['type'])) {
            case 'submit':
                return Html::submitButton($finalLabel, $htmlOptions);
            case 'reset':
                return Html::resetButton($finalLabel, $htmlOptions);
            case 'link':
                return Html::a($finalLabel, $config['url'], $htmlOptions);
            case 'button':
            default:
                return Html::button($finalLabel, $htmlOptions);
        }
    }
}
