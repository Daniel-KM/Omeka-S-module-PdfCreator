<?php declare(strict_types=1);

namespace PdfCreator;

return [
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'pdfCreator' => View\Helper\PdfCreator::class,
        ],
    ],
    // Keep empty config for automatic management.
    'pdfcreator' => [
    ],
];
