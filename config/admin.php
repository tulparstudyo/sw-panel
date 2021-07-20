<?php

return [
    'jqadm' => [
        'navbar' => [
            'swordbros'=>[
                ''=>'swordbros',
                '10'=>'swordbros/frigian',
                '20'=>'swordbros/slider'
            ]
        ],
        'resource' =>[
            'swordbros' => [
                'groups' => ['admin', 'editor', 'super'],
                'frigian' =>[
                    'groups' => ['admin', 'editor', 'super'],
                    'key' => 'SF',
                ],
                'slider' =>[
                    'groups' => ['admin', 'editor', 'super'],
                    'key' => 'SS',
                ],
                /*'blog' =>[
                    'groups' => ['admin', 'editor', 'super'],
                    'key' => 'SB',
                ],*/
            ],
        ],
    ],
    'jsonadm' => [
    ],
];