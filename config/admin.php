<?php

return [
    'jqadm' => [
        'navbar' => [
            'swordbros'=>['swordbros/frigian', 'swordbros/slider'/*, 'swordbros/blog'*/]
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