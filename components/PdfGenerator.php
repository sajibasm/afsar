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
use app\models\CustomerAccount;
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
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use kartik\mpdf\Pdf;

use Yii;
use yii\helpers\Url;

class PdfGenerator
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

    const WATERMARK_EMAIL = 'AFSAR TRADERS';
    const WATERMARK_ALPHA_PRINT = 0.035;

    private static function createPdf($content, $title, $filename, $isSave, $customFormat = Pdf::FORMAT_A4)
    {

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'filename' => $filename,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'content' => mb_convert_encoding($content, 'UTF-8', 'windows-1252'),
//            'cssInline' => file_get_contents(Yii::getAlias('@app/library/invoice/css/invoice.css')),
            'options' => ['title' => $title],
            'methods' => [
                'SetFooter' => [ 'Developed by: '.SystemSettings::DevelopBy().'||Page: {PAGENO}|'],
            ],
        ]);

        $pdf->getApi()->SetWatermarkText(self::WATERMARK_EMAIL);
        $pdf->getApi()->showWatermarkText = true;
        $pdf->getApi()->watermark_font = 'DejaVuSansCondensed';
        $pdf->getApi()->watermarkTextAlpha = self::WATERMARK_ALPHA_PRINT;
        $pdf->getApi()->SetDisplayMode('fullpage');
        $pdf->getApi()->allow_charset_conversion = true;
        $pdf->getApi()->autoScriptToLang = true;
        $pdf->getApi()->cleanup();
        $pdf->getApi()->charset_in = 'UTF-8';


        return $isSave?$filename:$pdf->render();
    }

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
            $watermark = SystemSettings::Company();
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
            $watermark = SystemSettings::Company();
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
            $watermark = SystemSettings::Company();
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

        return $isSave?$filename:$pdf->Output('', 'I');
    }

    public static function numberToTakaWords($amount)
    {
        $amount = number_format((float)$amount, 2, '.', '');
        list($taka, $paisa) = explode('.', $amount);

        $taka = (int)$taka;
        $paisa = (int)$paisa;

        $words = [];

        if ($taka > 0) {
            $words[] = self::convertNumberToWords($taka) . ' Taka';
        }

        if ($paisa > 0) {
            $words[] = self::convertNumberToWords($paisa) . ' Paisa';
        }

        if (empty($words)) {
            return 'Zero Taka only';
        }

        return implode(' and ', $words) . ' only';
    }

    private static function convertNumberToWords($number)
    {
        $words = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six',
            'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
            'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen',
            'Eighteen', 'Nineteen'
        ];

        $tens = [
            '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty',
            'Sixty', 'Seventy', 'Eighty', 'Ninety'
        ];

        $digits = ['', 'Thousand', 'Lakh', 'Crore'];

        if ($number == 0) {
            return 'Zero';
        }

        $result = '';
        $digitIndex = 0;

        while ($number > 0) {
            $divider = ($digitIndex == 0) ? 1000 : 100;
            $chunk = $number % $divider;
            $number = floor($number / $divider);

            if ($chunk > 0) {
                $chunkWords = '';

                // Handle hundreds
                if ($chunk >= 100) {
                    $chunkWords .= $words[floor($chunk / 100)] . ' Hundred';
                    $remainder = $chunk % 100;
                    if ($remainder > 0) {
                        $chunkWords .= ' ' . self::convertNumberToWords($remainder);
                    }
                } elseif ($chunk < 20) {
                    $chunkWords = $words[$chunk];
                } else {
                    $chunkWords = $tens[floor($chunk / 10)];
                    if ($chunk % 10 > 0) {
                        $chunkWords .= ' ' . $words[$chunk % 10];
                    }
                }

                if ($digitIndex > 0 && $chunkWords !== '') {
                    $chunkWords .= ' ' . $digits[$digitIndex];
                }

                $result = $chunkWords . ' ' . $result;
            }

            $digitIndex++;
        }

        return trim($result);
    }

    public static function SalesInvoice($salesId, $filename)
    {
        $sales = Sales::findOne($salesId);
        $totalDues = CustomerAccount::getCustomerDues($sales->client_id);
        $salesDetails = SalesDetails::find()->where(['sales_id' => $sales->sales_id])->orderBy('sales_details_id')->all();

        $visibleReconciliationAmount = 0;
        $invisibleReconciliationAmount = 0;
        $reconciliationType = [];

        $reconciliations = BankReconciliation::find()->where(['invoice_id' => $salesId])->all();
        $reconciliationAmount = 0;
        foreach ($reconciliations as $reconciliation) {
            $reconciliationAmount+= $reconciliation->amount;
        }

        // ✅ Generate QR Code (Base64) for Invoice Lookup
        $salesId = $sales->sales_id;
        $clientId = $sales->client_id;
        $expiryTimestamp = time() + (3 * 24 * 60 * 60);  // 7 days validity

        $tokenString = $salesId . '|' . $clientId . '|' . $expiryTimestamp;
        $secureToken = Utility::encrypt($tokenString);

        $publicUrl = Url::to(['sales/invoice-lookup', 'token' => $secureToken], true);

        $qrCode = QrCode::create($publicUrl)->setSize(150)->setMargin(0);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

        $content = Yii::$app->controller->renderPartial('/sales/invoice', [
            'model' => $sales,
            'salesDetails' => $salesDetails,
            'visibleReconciliationAmount' => $visibleReconciliationAmount,
            'reconciliationAmount' => $reconciliationAmount,
            'previousDues'  => ($totalDues - $sales->due_amount),
            'qrCode' => $qrBase64,   // ✅ Pass QR Code to view
        ]);


        $title = $sales->client_name . " # Invoice: " . $sales->sales_id;
        self::createPdf($content, $title, $filename, false);
    }

    public static function paymentReceipt($receiptId, $filename)
    {


        $model = ClientPaymentHistory::findOne($receiptId);
        $details = ClientPaymentDetails::find()->where(['payment_history_id' => $receiptId])->all();
        $withdraw = CustomerWithdraw::find()->where(['payment_history_id' => $receiptId])->all();
        $content = Yii::$app->controller->renderPartial('/client-payment-history/invoice', ['model' => $model, 'details' => $details, 'withdraw' => $withdraw]);
        $title = $model->customer->client_name . " # Payment: " . $model->client_payment_history_id;
        self::createPdf($content, $title, $filename, false);

    }

    public static function CustomerRefundReceipt($receiptId, $filename)
    {
        $withdraw = CustomerWithdraw::findOne($receiptId);
        $model = ClientPaymentHistory::findOne($withdraw->payment_history_id);
        $content = Yii::$app->controller->renderPartial('/customer-withdraw/invoice', ['model' => $model, 'withdraw' => $withdraw]);
        $title = $model->customer->client_name . " # Invoice: " . $model->client_payment_history_id;
        self::createPdf($content, $title, $filename, false);
    }

}
