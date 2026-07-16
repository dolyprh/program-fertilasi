<?php

return [
    'training_age_range' => [
        'min' => 26,
        'max' => 59,
    ],

    'model' => [
        'algorithm' => 'Decision Tree C4.5',
        'criterion' => 'Gain Ratio',
        'training_count' => 101,
        'testing_count' => 25,
        'root_node' => 'Promil Saat Ini',
        'maximal_depth' => 4,
        'minimal_gain' => 0.001,
        'minimal_leaf_size' => 1,
        'pruning' => true,
        'prepruning' => true,
        'accuracy' => 36.00,
        'macro_precision' => 17.11,
        'macro_recall' => 22.92,
        'macro_f1' => 18.97,
    ],
];
