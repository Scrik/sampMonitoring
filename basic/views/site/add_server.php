<?php

$this->title = 'Добавление сервера';

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal'],
]);

echo $form->field($model, 'ip')->textInput(['placeholder' => 'Введите IP'])->label(false);
echo $form->field($model, 'port')->textInput(['placeholder' => 'Введите порт'])->label(false);
?>
    <div class="form-group">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>