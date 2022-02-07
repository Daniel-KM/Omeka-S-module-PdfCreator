<?php declare(strict_types=1);

namespace PdfCreator\View\Helper;

// The libraries are loaded here in order to be added only when needed.
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Laminas\View\Helper\AbstractHelper;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;

/**
 * Create a pdf from a resource.
 */
class PdfCreator extends AbstractHelper
{
    /**
     * Render a resource as pdf.
     */
    public function __invoke(AbstractResourceEntityRepresentation $resource, ?string $template = null, array $options = []): string
    {
    }
}
