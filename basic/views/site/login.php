<?php
$this->title = 'Авторизация';

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin(['options' => ['class' => 'form-horizontal']]);

echo $form->field($model, 'login')->textInput(['placeholder' => 'Введите имя пользователя'])->label(false);
echo $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите пароль'])->label(false);
?>
<div class="form-group">
    <?= Html::submitButton('Войти', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>