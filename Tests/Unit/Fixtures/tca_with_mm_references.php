<?php
/**
 * Test TCA for a table with external mm references to other table
 */
return [
    'columns' => [
        'relatedproducts' => [
            'label' => 'aLabel',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_commerce_products',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 20,
                'MM' => 'tx_commerce_products_related_mm',
            ],
        ],
    ],
];
