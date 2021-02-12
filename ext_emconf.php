<?php
$EM_CONF['enetcache'] = [
    'title' => 'Plugin cache engine',
    'description' => 'Provides an interface to cache plugin content elements',
    'category' => 'Frontend',
    'version' => '4.1.1',
    'module' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Christian Kuhn',
    'author_email' => 'lolli@schwarzbu.ch',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
