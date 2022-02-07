<?php declare(strict_types=1);

namespace PdfCreator\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class OutputController extends AbstractActionController
{
    public function showAction()
    {
        // Wrong id is automatically managed.
        $resource = $this->api()->read('resources', $this->params('id'))->getContent();

        $template = $this->params()->fromQuery('template') ?: null;
        $resourceName = lcfirst(substr($resource->getResourceJsonLdType(), 2));

        return new ViewModel([
            'site' => $this->currentSite(),
            $resourceName => $resource,
            'resource' => $resource,
            'template' => $template,
        ]);
    }
}
