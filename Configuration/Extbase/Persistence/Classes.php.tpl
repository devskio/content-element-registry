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
            \Devsk\DsBoilerplate\Domain\Model\ContentElement\RegularTextElement::class => \Devsk\DsBoilerplate\Domain\Model\ContentElement\RegularTextElement::class,
        ]
    ],
];
