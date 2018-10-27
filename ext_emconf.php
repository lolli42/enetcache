<?php
$EM_CONF['enetcache'] = [
    'title' => 'Plugin cache engine',
    'description' => 'Provides an interface to cache plugin content elements',
    'category' => 'Frontend',
    'version' => '1.3.0',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Christian Kuhn',
    'author_email' => 'lolli@schwarzbu.ch',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'php' => '7.0.0-7.2.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
