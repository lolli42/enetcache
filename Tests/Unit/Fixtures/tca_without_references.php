<?php
/**
 * This is a TCA column definition for a standalone table that has
 * no references to other tables.
 */
return [
    'columns' => [
        'aField' => [
            'label' => 'aLabel',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['anItem', 0],
                    ['anotherItem', 1]
                ]
            ],
        ],
    ],
];
