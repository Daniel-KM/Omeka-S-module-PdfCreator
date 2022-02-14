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
    const PARTIAL_NAME = 'common/template/record';

    /**
     * Render a resource as pdf (format A4 portrait by default).
     *
     * @link https://github.com/dompdf/dompdf
     * @link https://github.com/dompdf/dompdf/blob/HEAD/src/Options.php#L307-L359.
     *
     * @var AbstractResourceEntityRepresentation $resource
     * @var string $template The template to render. If it is a simple name
     *   ("record"), it should be inside "common/template".
     *   When the output from the template is empty, the default template of
     *   the resource in the current theme (for example "omeka/site/item/show")
     *   is used as a fallback. You can force it with template "default" too.
     * @var array $options Options passed to Dompdf and to the template.
     *   See all available options here in the documentation of DomPdf.
     *   A specific option for the module is "skipFallback", false by default.
     * @return string The helper will exit the pdf content automatically, else
     *   it outputs an empty string.
     */
    public function __invoke(AbstractResourceEntityRepresentation $resource, ?string $template = null, array $options = [], bool $isFirst = false): string
    {
        static $isNextCall = false;

        if ($isNextCall) {
            return '';
        }

        $tempDir = $resource->getServiceLocator()->get('Config')['temp_dir'];
        $defaultOptions = [
            'defaultPaperSize' => 'a4',
            // Required, because css, images, etc. are all served by the server.
            'isRemoteEnabled' => true,
            'pdfBackend' => 'auto',
            'tempDir' => $tempDir,
            'fontCache' => $tempDir,
            'skipFallback' => false,
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

        // When there is no output, use the default page.
        if (!$html) {
            if (!empty($options['skipFallback'])) {
                return '';
            }
            if (!$isFallbackTemplate) {
                $isNextCall = false;
                return $this->__invoke($resource, $fallbackTemplate, [
                    'site' => $site,
                    $resourceName => $resource,
                    'resource' => $resource,
                    'options' => $options,
                ]);
            }
        }

        // Fix relative links to application assets, mainly fallback thumbnails.
        $baseUrl = $view->serverUrl($view->basePath());
        $applicationAsset = $baseUrl . '/application/asset/';
        $modules = $baseUrl . '/modules/';
        $themes = $baseUrl . '/themes/';
        $replace = [
            '"/application/asset/' => '"' . $applicationAsset,
            "'/application/asset/" => "'" . $applicationAsset,
            '"&#x2F;application&#x2F;asset&#x2F;' => '"' . $applicationAsset,
            "'&#x2F;application&#x2F;asset&#x2F;" => "'" . $applicationAsset,
            '"/modules/' => '"' . $modules,
            "'/modules/" => "'" . $modules,
            '"&#x2F;modules&#x2F;' => '"' . $modules,
            "'&#x2F;modules&#x2F;" => "'" . $modules,
            '"/themes/' => '"' . $themes,
            "'/themes/" => "'" . $themes,
            '"&#x2F;themes&#x2F;' => '"' . $themes,
            "'&#x2F;themes&#x2F;" => "'" . $themes,
        ];
        $html = str_replace(array_keys($replace), array_values($replace), $html);

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
