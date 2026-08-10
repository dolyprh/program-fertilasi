<?php

return [
    'training_age_range' => [
        'min' => 24,
        'max' => 47,
    ],

    'model' => [
        'algorithm' => 'Decision Tree C4.5',
        'criterion' => 'Gain Ratio',
        'training_count' => 478,
        'testing_count' => 119,
        'root_node' => 'Promil Saat Ini',
        'maximal_depth' => 4,
        'minimal_gain' => 0.001,
        'minimal_leaf_size' => 1,
        'pruning' => true,
        'prepruning' => true,
        'accuracy' => 50.42,
        'macro_precision' => 36.40,
        'macro_recall' => 28.91,
        'macro_f1' => 27.92,
    ],
];
