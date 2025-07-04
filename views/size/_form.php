<?php
    use app\components\ProductUtility;
use kartik\editors\Summernote;
use kartik\widgets\DepDrop;
    use kartik\widgets\Select2;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    /* @var $this yii\web\View */
    /* @var $model app\models\Size */
    /* @var $form yii\widgets\ActiveForm */

$this->registerCssFile('https://cdn.quilljs.com/1.3.6/quill.snow.css');
$this->registerJsFile('https://cdn.quilljs.com/1.3.6/quill.min.js');
?>

<?php
$js = <<<JS
var quill = new Quill('#editor', {
    theme: 'snow',
    placeholder: 'Write product details...',
    modules: {
        toolbar: [
            [{ 'font': [] }, { 'size': [] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'header': '1' }, { 'header': '2' }, 'blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }, { 'align': [] }],
            ['link', 'image', 'video'],
            ['clean']
        ]
    }
});

// Update hidden input on change
quill.on('text-change', function() {
    document.getElementById('size-description').value = quill.root.innerHTML;
});
JS;

$this->registerJs($js);
?>



    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'item_id')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(ProductUtility::getItemList(), 'item_id', 'item_name'),
                'options' => ['placeholder' => 'Select a Items'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'brand_id')->widget(DepDrop::classname(), [
                'type'=>DepDrop::TYPE_SELECT2,
                'data'=>$model->isNewRecord==false?ArrayHelper::map(ProductUtility::getBrandListByItem($model->item_id), 'brand_id', 'brand_name'):[],
                'select2Options'=>['pluginOptions'=>['allowClear'=>true],   'theme'=>Select2::THEME_DEFAULT,],
                'options' => ['id'=>'size-brand_id'],
                'pluginOptions'=>[
                    'depends'=>['size-item_id'],
                    'placeholder' => 'Select Brand',
                    'url' => Url::to(['/size/get-brand-list-by-item'])
                ]
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'size_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'unit')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ArrayHelper::map(ProductUtility::getProductUnit(), 'id', 'name'),
                'options' => [
                    'placeholder' => 'Select Unit'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'unit_quantity')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'lowest_price')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'size_status')->widget(Select2::classname(), [
                'theme'=>Select2::THEME_DEFAULT,
                'data' => ['Active'=> 'Active', 'Inactive'=>'Inactive'],
                'options' => ['placeholder' => 'Select Status'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>


        <div class="col-md-6">
            <?php
            if(!$model->getIsNewRecord()){
                echo '<div class="form-group field-size-size_image">
                            <label class="control-label" for="size-size_image">Current Image</label>
                            <img src="'.$model->getImageUrl(false).'">
                        </div>';
            }
            ?>
            <?= $form->field($model, 'imageFile')->fileInput() ?>
        </div>

    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::label('Product Details', 'editor', ['class' => 'control-label']) ?>
                <div id="editor" style="min-height: 150px;"><?= Html::encode($model->size_description) ?></div>
                <?= Html::hiddenInput('Size[size_description]', $model->size_description, ['id' => 'size-description']) ?>
            </div>
        </div>
    </div>

    <div class="panel-footer">
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end align-items-center">
                    <?= \app\components\ButtonHelper::button($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>