<?php
/**
 * Created by PhpStorm.
 * User: lenin
 * Date: 4/17/16
 * Time: 5:29 PM
 */

namespace app\components;

use app\models\BankReconciliation;
use app\models\ClientPaymentDetails;
use app\models\ClientPaymentHistory;
use app\models\CustomerWithdraw;
use app\models\Expense;
use app\models\ProductStock;
use app\models\ProductStockItems;
use app\models\ProductStockItemsOutlet;
use app\models\ProductStockOutlet;
use app\models\ReconciliationType;
use app\models\Sales;
use app\models\SalesDetails;
use Dompdf\Dompdf;
use kartik\mpdf\Pdf;
use Yii;

class PdfGen
{
    /**
     * generates the booking voucher for customer
     *
     * @param $hotelBookingHistory
     * @return string
     * @property yii\web\Controller $controller
     *
     */

    const watermarkAlphaPrint = 0.040;
    const watermarkAlphaEmail = 0.040;
    const watermarkEmail = 'Electronic Copy';

    public static function stockOutletInvoice($stockId, $isSave = false)
    {

        /* @var $stock ProductStockOutlet */
        /* @var $items ProductStockOutlet */


        $stock = ProductStockOutlet::findOne($stockId);
        $items = ProductStockItemsOutlet::find()->where(['product_stock_outlet_id'=>$stockId])->all();

        $content = Yii::$app->controller->renderPartial('/product-stock-outlet/invoice', [
            'model' => $stock,
            'items' => $items
        ]);

        $title = $stock->invoice;
        $filename = "invoice_" . $stock->invoice . '.pdf';
        $print = 'Print at: ';
        if ($isSave) {
            $print = 'Generated At: ';
            $watermark = $watermarkAlpha = self::watermarkEmail;
            $watermarkAlpha = self::watermarkAlphaEmail;
            $filename = Yii::getAlias('@webroot/temp/') . $filename;
        } else {
            $watermark = SystemSettings::getStoreName();
            $watermarkAlpha = self::watermarkAlphaPrint;
        }


        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            'defaultFont' => '@webroot/css/SourceSansPro-Regular.ttf',
            'format' => Pdf::FORMAT_A4,
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => $isSave ? Pdf::DEST_DOWNLOAD : Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => file_get_contents(Yii::getAlias('@webroot/css/invoice.css')),
            'options' => ['title' => $title],
            'methods' => [
                'SetFooter' => [$print . DateTimeUtility::getDate(null, SystemSettings::dateTimeFormat()) . '|Developed by: Axial Solution Ltd|Page: {PAGENO}|'],
            ]
        ]);


        $pdf->getApi()->SetWatermarkText($watermark);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'DejaVuSansCondensed';
        $pdf->getApi()->watermarkTextAlpha = $watermarkAlpha;
        $pdf->getApi()->SetDisplayMode('fullpage');
        $pdf->getApi()->allow_charset_conversion = true;
        $pdf->getApi()->charset_in = 'iso-8859-4';

        if (SystemSettings::invoiceSalesAutoPrint() && !$isSave) {
            $pdf->getApi()->SetJS('this.print(true);');
        }

        return $isSave ? $filename : $pdf->render();

    }


    public static function stockInvoice($stockId, $isSave = false)
    {

        /* @var $stock ProductStock */
        /* @var $items ProductStockItems */


        $stock = ProductStock::findOne($stockId);
        $items = ProductStockItems::find()->where(['product_stock_id'=>$stockId])->all();

        $content = Yii::$app->controller->renderPartial('/product-stock/invoice', [
            'model' => $stock,
            'items' => $items
        ]);

        $title = $stock->invoice_no;
        $filename = "invoice_" . $stock->invoice_no . '.pdf';
        $print = 'Print at: ';
        if ($isSave) {
            $print = 'Generated At: ';
            $watermark = $watermarkAlpha = self::watermarkEmail;
            $watermarkAlpha = self::watermarkAlphaEmail;
            $filename = Yii::getAlias('@webroot/temp/') . $filename;
        } else {
            $watermark = SystemSettings::getStoreName();
            $watermarkAlpha = self::watermarkAlphaPrint;
        }


        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            'defaultFont' => '@webroot/css/SourceSansPro-Regular.ttf',
            'format' => Pdf::FORMAT_A4,
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => $isSave ? Pdf::DEST_DOWNLOAD : Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => file_get_contents(Yii::getAlias('@webroot/css/invoice.css')),
            'options' => ['title' => $title],
            'methods' => [
                'SetFooter' => [$print . DateTimeUtility::getDate(null, SystemSettings::dateTimeFormat()) . '|Developed by: Axial Solution Ltd|Page: {PAGENO}|'],
            ]
        ]);


        $pdf->getApi()->SetWatermarkText($watermark);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'DejaVuSansCondensed';
        $pdf->getApi()->watermarkTextAlpha = $watermarkAlpha;
        $pdf->getApi()->SetDisplayMode('fullpage');
        $pdf->getApi()->allow_charset_conversion = true;
        $pdf->getApi()->charset_in = 'iso-8859-4';

        if (SystemSettings::invoiceSalesAutoPrint() && !$isSave) {
            $pdf->getApi()->SetJS('this.print(true);');
        }

        return $isSave ? $filename : $pdf->render();

    }

    public static function expenseInvoice($id, $isSave)
    {
        $model = Expense::findOne(Utility::decrypt($id));


        $content = Yii::$app->controller->renderPartial('/expense/invoice', ['model' => $model]);

        //$title = $sales->client_name . " # Invoice: " . $sales->sales_id;
        $filename = "invoice_" . $model->expenseType->expense_type_name . '.pdf';

        if ($isSave) {
            $watermark = $watermarkAlpha = self::watermarkEmail;
            $watermarkAlpha = self::watermarkAlphaEmail;
            $destination = Pdf::DEST_FILE;
            $filename = Yii::getAlias('@webroot/temp/') . $filename;
        } else {
            $watermark = SystemSettings::getStoreName();
            $destination = Pdf::DEST_BROWSER;
            $watermarkAlpha = self::watermarkAlphaPrint;
        }

        $print = $isSave ? 'Generated At:' : 'Print at: ';

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            //'mode' => Pdf::MODE_BLANK,
            'defaultFont' => '@web/css/SourceSansPro-Regular.ttf',
            'format' => [190, 236],
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            //'mode' => Pdf::MODE_CORE,
            // A4 paper format
            // portrait orientation
            // stream to browser inline
            'destination' => $destination,
            'content' => $content,
            //'cssInline' => file_get_contents(Yii::getAlias('@webroot/css/invoice.css')),
            'options' => ['title' => "Tes"],
            'methods' => [
                //'SetHeader'=>['Sales Invoice'],
                'SetFooter' => [$print . DateTimeUtility::getDate(null, SystemSettings::dateTimeFormat()) . '|Developed by: Axial Solution Ltd|Page: {PAGENO}|'],
            ]
        ]);
        $pdf->getApi()->allow_charset_conversion = true;
        $pdf->getApi()->charset_in = 'UTF-8';;
        $pdf->getApi()->autoLangToFont = true;
        $pdf->getApi()->WriteHTML($content);


        $pdf->getApi()->SetWatermarkText($watermark);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'DejaVuSansCondensed';
        $pdf->getApi()->watermarkTextAlpha = $watermarkAlpha;
        $pdf->getApi()->SetDisplayMode('fullpage');

        if (SystemSettings::invoiceExpenseAutoPrint()) {
            $pdf->getApi()->SetJS('this.print(true);');
        }

        return $isSave?$filename:$pdf->Output('', 'I');
    }

    public static function salesInvoice($salesId, $filename)
    {


        $sales = Sales::findOne($salesId);
        $salesDetails = SalesDetails::find()->where(['sales_id' => $sales->sales_id])->orderBy('sales_details_id')->all();

        $visibleReconciliationAmount = 0;
        $invisibleReconciliationAmount = 0;
        $reconciliationType = [];

        $reconciliations = BankReconciliation::find()->where(['invoice_id' => $salesId])->all();
        foreach ($reconciliations as $reconciliation) {
            if ($reconciliation->reconciliation->show_invoice == ReconciliationType::VISIBLE_ON_INVOICE_YES) {
                $visibleReconciliationAmount += $reconciliation->amount;
                $reconciliationType[] = $reconciliation->reconciliation->name;
            } else {
                $invisibleReconciliationAmount += $reconciliation->amount;
            }
        }

        $content = Yii::$app->controller->renderPartial('/sales/invoice', [
            'model' => $sales,
            'salesDetails' => $salesDetails,
            'visibleReconciliationAmount' => $visibleReconciliationAmount,
            'invisibleReconciliationAmount' => $invisibleReconciliationAmount,
            'reconciliationType' => implode(',', $reconciliationType)
        ]);

        $title = $sales->client_name . " # Invoice: " . $sales->sales_id;
        $print = 'Generated At: ';
        $watermark = $watermarkAlpha = self::watermarkEmail;
        $watermarkAlpha = self::watermarkAlphaEmail;

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            'defaultFont' => '@webroot/css/SourceSansPro-Regular.ttf',
            'format' => Pdf::FORMAT_A4,
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'content' => mb_convert_encoding($content, 'UTF-8', 'windows-1252'),
            'cssInline' => file_get_contents(Yii::getAlias('@webroot/css/invoice.css')),
            'options' => ['title' => $title],
            'methods' => [
                //'SetHeader'=>[AppConfig::getStoreName()],
                'SetFooter' => [$print . DateTimeUtility::getDate(null, SystemSettings::dateTimeFormat()) . '|Developed by: Axial Solution Ltd|Page: {PAGENO}|'],
            ]
        ]);
        //Utility::debug($pdf);

        $pdf->getApi()->SetWatermarkText($watermark);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'DejaVuSansCondensed';
        $pdf->getApi()->watermarkTextAlpha = $watermarkAlpha;
        $pdf->getApi()->SetDisplayMode('fullpage');
        $pdf->getApi()->allow_charset_conversion = true;
        $pdf->getApi()->autoScriptToLang = true;
        $pdf->getApi()->cleanup();
        $pdf->getApi()->charset_in = 'iso-8859-4';

        if (SystemSettings::invoiceSalesAutoPrint()) {
            $pdf->getApi()->SetJS('this.print();');
        }

        $pdf->render();
    }

    public static function paymentReceipt($receiptId, $isSave)
    {


        $model = ClientPaymentHistory::findOne($receiptId);
        $details = ClientPaymentDetails::find()->where(['payment_history_id' => $receiptId])->all();
        $withdraw = CustomerWithdraw::find()->where(['payment_history_id' => $receiptId])->all();
        $content = Yii::$app->controller->renderPartial('/client-payment-history/invoice', ['model' => $model, 'details' => $details, 'withdraw' => $withdraw]);
        $title = $model->customer->client_name . " # Invoice: " . $model->client_payment_history_id;
        $filename = "payment_receiptId_" . $model->client_payment_history_id . '.pdf';

        if ($isSave) {
            $watermark = $watermarkAlpha = self::watermarkEmail;
            $watermarkAlpha = self::watermarkAlphaEmail;
            $filename = Yii::getAlias('@webroot/temp/') . $filename;
        } else {
            $watermark = SystemSettings::getStoreName();
            $watermarkAlpha = self::watermarkAlphaPrint;
        }

        $print = $isSave ? 'Generated At:' : 'Print AT: ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => $isSave ? Pdf::DEST_DOWNLOAD : Pdf::DEST_BROWSER,
            'content' => mb_convert_encoding($content, 'UTF-8', 'auto'), // Convert content to UTF-8            'cssInline' => file_get_contents(Yii::getAlias('@webroot/css/invoice.css')),
            'options' => ['title' => $title],
            'methods' => [
                'SetFooter' => [$print . DateTimeUtility::getDate(null, SystemSettings::dateTimeFormat()) . '|Developed by: Axial Solution Ltd|Page: {PAGENO}|'],
            ]
        ]);

        $pdf->getApi()->SetWatermarkText($watermark);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'SolaimanLipi';
        $pdf->getApi()->watermarkTextAlpha = $watermarkAlpha;
        $pdf->getApi()->SetDisplayMode('fullpage');
        $pdf->getApi()->allow_charset_conversion = true;
        $pdf->getApi()->charset_in = 'iso-8859-4';
        if (SystemSettings::invoiceSalesAutoPrint() && !$isSave) {
            $pdf->getApi()->SetJS('this.print(true);');
        }
        return $isSave ? $filename : $pdf->render();
    }

    public static function refundReceipt($receiptId, $isSave)
    {
        $print = $isSave ? 'Generated At:' : 'Print at: ';

        $withdraw = CustomerWithdraw::findOne($receiptId);
        $model = ClientPaymentHistory::findOne($withdraw->payment_history_id);

        $content = Yii::$app->controller->renderPartial('/customer-withdraw/invoice', ['model' => $model, 'withdraw' => $withdraw]);
        $title = $model->customer->client_name . " # Invoice: " . $model->client_payment_history_id;
        $filename = "payment_refund_receiptId_" . $model->client_payment_history_id . '.pdf';

        if ($isSave) {
            $watermark = $watermarkAlpha = self::watermarkEmail;
            $watermarkAlpha = self::watermarkAlphaEmail;
            $destination = Pdf::DEST_FILE;
            $filename = Yii::getAlias('@webroot/temp/') . $filename;
        } else {
            $watermark = SystemSettings::getStoreName();
            $destination = Pdf::DEST_BROWSER;
            $watermarkAlpha = self::watermarkAlphaPrint;
        }

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            'defaultFont' => '@web/css/SourceSansPro-Regular.ttf',
            'format' => Pdf::FORMAT_A4,
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => $destination,
            'content' => $content,
            'cssInline' => file_get_contents(Yii::getAlias('@webroot/css/invoice.css')),
            'options' => ['title' => $title],
            'methods' => [
                //'SetHeader'=>['Sales Invoice'],
                'SetFooter' => [$print . DateTimeUtility::getDate(null, SystemSettings::dateTimeFormat()) . '|Developed by: Axial Solution Ltd|Page: {PAGENO}|'],
            ]
        ]);

        $pdf->getApi()->SetWatermarkText($watermark);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'DejaVuSansCondensed';
        $pdf->getApi()->watermarkTextAlpha = $watermarkAlpha;
        $pdf->getApi()->SetDisplayMode('fullpage');

        if (SystemSettings::invoiceAutoPrintWindow() && !$isSave) {
            $pdf->getApi()->SetJS('this.print(true);');
        }

        return $isSave ? $filename : $pdf->render();

    }

} 
