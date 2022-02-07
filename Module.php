<?php declare(strict_types=1);

namespace PdfCreator;

if (!class_exists(\Generic\AbstractModule::class)) {
    require file_exists(dirname(__DIR__) . '/Generic/AbstractModule.php')
        ? dirname(__DIR__) . '/Generic/AbstractModule.php'
        : __DIR__ . '/src/Generic/AbstractModule.php';
}

use Generic\AbstractModule;

/**
 * Pdf Creator
 *
 * @copyright Daniel Berthereau, 2022
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 */
class Module extends AbstractModule
{
    const NAMESPACE = __NAMESPACE__;

    protected function preInstall(): void
    {
        $js = __DIR__ . '/vendor/dompdf/dompdf/lib/Cpdf.php';
        if (!file_exists($js)) {
            $services = $this->getServiceLocator();
            $t = $services->get('MvcTranslator');
            throw new \Omeka\Module\Exception\ModuleCannotInstallException(
                $t->translate('The libraries should be installed. See module’s installation documentation.') // @translate
            );
        }
    }
}
