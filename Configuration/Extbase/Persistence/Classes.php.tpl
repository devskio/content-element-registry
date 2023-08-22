<?php
declare(strict_types = 1);

return [
    \Devsk\ContentElementRegistry\Domain\Model\ContentElement::class => [
        'tableName' => 'tt_content',
        'properties' => [
             'CType' => [
                'fieldName' => 'CType'
             ],
             'header' => [
                 'fieldName' => 'header'
             ],
             'sectionIndex' => [
                 'fieldName' => 'sectionIndex'
             ],
        ],
        'subclasses' => [
            \DevSK\DsBoilerplate\Domain\Model\ContentElement\RegularTextElement::class => \DevSK\DsBoilerplate\Domain\Model\ContentElement\RegularTextElement::class,
        ]
    ],
];
