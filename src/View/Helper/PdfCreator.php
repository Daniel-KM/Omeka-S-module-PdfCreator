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
     * The default partial view script.
     */
    const PARTIAL_NAME = 'common/template/default';

    /**
     * Render a resource as pdf (format A4 portrait by default).
     *
     * @link https://github.com/dompdf/dompdf
     * @link https://github.com/dompdf/dompdf/blob/HEAD/src/Options.php#L307-L359.
     *
     * @var AbstractResourceEntityRepresentation $resource
     * @var string $template The template to render. If it is a simple name
     *   ("default"), it should be inside "common/template".
     *   When the output from the template is empty, the default template of
     *   the resource in the current theme (for example "omeka/site/item/show")
     *   is used as a fallback.
     * @var array $options Options passed to Dompdf and to the template.
     *   See all available options here in the documentation of DomPdf.
     * @return string|Dompdf
     */
    public function __invoke(AbstractResourceEntityRepresentation $resource, ?string $template = null, array $options = [], bool $isFirst = false)
    {
        static $isNextCall = false;

        if ($isNextCall) {
            return '';
        }

        $defaultOptions = [
            'defaultPaperSize' => 'a4',
            'pdfBackend' => 'auto',
            'tempDir' => $resource->getServiceLocator()->get('Config')['temp_dir'],
        ];
        $options += $defaultOptions;

        $view = $this->getView();

        $site = $this->currentSite();

        if ($template) {
            if (strpos($template, '/') === false) {
                $template = 'common/template/' . $template;
            }
            if (!$view->resolver($template)) {
                $template = self::PARTIAL_NAME;
            }
        } else {
            $template = self::PARTIAL_NAME;
        }

        // Don't include itself in the output: this helper is generally used in
        // "/show" and it should avoid an infinite loop.
        // @todo Other templates are not checked for infinite loops for now.
        $fallbackTemplate= 'omeka/site/' . $resource->getControllerName() . '/show';
        $isFallbackTemplate = $fallbackTemplate === $template;

        $isNextCall = true;

        $resourceName = lcfirst(substr($resource->getResourceJsonLdType(), 2));
        $html = $view->partial($template, [
            'site' => $site,
            $resourceName => $resource,
            'resource' => $resource,
            'options' => $options,
        ]);

        // When there is output, use the default page.
        if (!$html && !$isFallbackTemplate) {
            $isNextCall = false;
            return $this->__invoke($resource, $fallbackTemplate, [
                'site' => $site,
                $resourceName => $resource,
                'resource' => $resource,
                'options' => $options,
            ]);
        }

        $isNextCall = true;

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream();
        exit();
    }

    protected function currentSite(): ?\Omeka\Api\Representation\SiteRepresentation
    {
        return $this->view->site ?? $this->view->site = $this->view
            ->getHelperPluginManager()
            ->get('Laminas\View\Helper\ViewModel')
            ->getRoot()
            ->getVariable('site');
    }
}
