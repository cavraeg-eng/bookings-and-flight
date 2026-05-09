<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */
require __DIR__ . '/vendor/autoload.php';
use Travelpayouts\Vendor\Glook\YiiGrid\DataView\GridView;
use Travelpayouts\Vendor\Glook\YiiGrid\Data\ArrayDataProvider;

$dataSource = [];
for ($i = 0; $i < 10; $i++) {
    $dataSource[] = [
        'uniqid' => uniqid(),
        'loop_iterator' => $i . ' times',
        'date' => date('Y-m-d'),
        'total' => rand(1, 25)
    ];
}


$dataProvider = new ArrayDataProvider([
    'allModels' => $dataSource,
]);

$grid = new GridView([
    'emptyText' => 'Empty data',
    'dataProvider' => $dataProvider,
    'tableOptions' => [
        'class' => 'table',
        'data-item' => rand(),
    ],
    'headerRowOptions' => [
        'class' => 'header row',
        'data-item' => rand(),
    ],
    'rowOptions' => [
        'class' => 'row'
    ],
    'columns' => [
        'total',
        'date',
        'iterationNumber' => [
            'value' => function ($model) {
                return 'it counts '.$model['loop_iterator'];
            },
            'label' => 'Iteration Count',

            'headerOptions' => [
                'style' => 'background:aqua;'
            ],
            'contentOptions' => function ($model, $key, $index, $column) {
                return [
                    'style' => 'background:red;',
                    'data-id' => $key + 1,
                ];
            },
        ]
    ]

]);
echo($grid->renderItems());
