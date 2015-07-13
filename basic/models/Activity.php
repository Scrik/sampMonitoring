<?php

namespace app\models;

use Yii;

class Activity extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'activity';
    }

    public function rating_change($id) {
        if (Activity::find()->where(['user' => Yii::$app->user->id, 'server' => $id, 'date' => Yii::$app->formatter->asDate('now', 'php:Y-m-d')])->count() == 0) {
            $model = new Activity();
            $model->user = Yii::$app->user->id;
            $model->date = Yii::$app->formatter->asDate('now', 'php:Y-m-d');
            $model->server = $id;
            return $model->save();
        }
    }

}
