<?php
$EM_CONF['enetcache'] = [
    'title' => 'Plugin cache engine',
    'description' => 'Provides an interface to cache plugin content elements',
    'category' => 'Frontend',
    'version' => '1.2.1',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Christian Kuhn',
    'author_email' => 'lolli@schwarzbu.ch',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-7.6.99',
            'php' => '5.5.0-7.1.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
