<?php

$this->title = 'Админ панель';

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\models\Servers;
use yii\grid\SerialColumn;

$dataProvider = new ActiveDataProvider([
    'query' => Servers::find()->where(['status' => 1])->orderBy('rating DESC'),
    'pagination' => [
        'pageSize' => 25,
    ],
        ]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'tableOptions' => [
        'class' => 'table table-hover',
    ],
    'columns' => [
        ['class' => SerialColumn::className()],
        [
            'attribute' => 'hostname',
            'format' => 'html',
            'label' => 'Название',
        ],
        [
            'format' => 'text',
            'label' => 'Адрес',
            'value' => function($data) {
                return $data->ip . ':' . $data->port;
            }
        ],
        [
            'format' => 'text',
            'label' => 'Игроки',
            'value' => function($data) {
                return $data->players . '/' . $data->maxplayers;
            }
        ],
        [
            'format' => 'html',
            'label' => 'Управление',
            'value' => function($data) {
                return Html::a("Удалить", ['admin/delete', 'id' => $data->id]);
            }
                ],
            ]
        ]);
        