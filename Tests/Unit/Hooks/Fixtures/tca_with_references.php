<?php
/**
 * Test TCA setup for a table with references to other tables.
 */
return [
    'columns' => [
        'cust_fe_user' => [
            'label' => 'aLabel',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'cust_stuff' => [
            'label' => 'aLabel',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'fe_users, tt_content',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ],
];
