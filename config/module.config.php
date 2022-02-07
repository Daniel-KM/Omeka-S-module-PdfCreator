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
    'controllers' => [
        'invokables' => [
            'PdfCreator\Controller\Output' => Controller\OutputController::class,
        ],
    ],
    'router' => [
        'routes' => [
            'site' => [
                'child_routes' => [
                    // This route is used when BulkExport is not available.
                    'pdf-creator' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/pdf/:id',
                            'constraints' => [
                                'id' => '\d+',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'PdfCreator\Controller',
                                'controller' => 'Output',
                                'action' => 'show',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    // Keep empty config for automatic management.
    'pdfcreator' => [
    ],
];
