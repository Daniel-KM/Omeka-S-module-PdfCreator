Pdf Creator (module for Omeka S)
================================

> __New versions of this module and support for Omeka S version 3.0 and above
> are available on [GitLab], which seems to respect users and privacy better
> than the previous repository.__

[Pdf Creator] is a module for [Omeka S] that allows to create dynamically a pdf
for a resource from an html template.

The pdf engine is a pure php one [DomPdf], so you don't need any dependency on
the server. It supports css 2.1 and some common css 3 features.


Installation
------------

First, install the optional module [Generic] if wanted.

The module uses external libraries, so use the release zip to install it, or use
and init the source.

* From the zip

Download the last release [PdfCreator.zip] from the list of releases (the master
does not contain the dependency), and uncompress it in the `modules` directory.

* From the source and for development:

If the module was installed from the source, rename the name of the folder of
the module to `PdfCreator`, and go to the root module, and run:

```sh
composer install --no-dev
```

Then install it like any other Omeka module.

See general end user documentation for [Installing a module].


Usage
-----


Add the following code somewhere into your theme to get the url of the pdf:

```php
echo $this->hyperlink('PDF', $this->url('site/pdf-creator', ['id' => $resource->id()], true), ['target' => '_blank']);
```

To pass a specific template, add it to the query:

```php
echo $this->hyperlink('PDF', $this->url('site/pdf-creator', ['id' => $resource->id()], ['query' => ['template' => 'record']], true), ['target' => '_blank']);
```

Note that the url in module Bulk Export is:

```php
echo $this->hyperlink('PDF', $this->url('site/resource-output-id', ['id' => $resource->id(), 'format' => 'pdf']], true), ['target' => '_blank']);
```

The url is something like "https://example.org/s/my-site/pdf/1", where the last
part is the resource id. If you use BulkExport, the url is the resource one,
plus ".pdf", as it was a real file.

The template is a phtml file from the theme. The default is "common/template/record.phtml"
or it shortcut "record". When the template returns nothing, like the template
"default", the template of the resource in the current theme (for example "omeka/site/item/show")
is used as a fallback. Set option "skipFallback" to avoid to use it.

To modify the options, add them to the template "view/pdf-creator/output/show".
The options are passed to DomPdf and to the template. See all available options
of DomPdf in [its documentation]. You can find examples and tools to debug
a template online [here].


TODO
----

- [ ] Include in Bulk Export output (included route) and archive this module.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitLab.


License
-------

### Module

This module is published under the [CeCILL v2.1] license, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

This software is governed by the CeCILL license under French law and abiding by
the rules of distribution of free software. You can use, modify and/ or
redistribute the software under the terms of the CeCILL license as circulated by
CEA, CNRS and INRIA at the following URL "http://www.cecill.info".

As a counterpart to the access to the source code and rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors have only limited liability.

In this respect, the user’s attention is drawn to the risks associated with
loading, using, modifying and/or developing or reproducing the software by the
user in light of its specific status of free software, that may mean that it is
complicated to manipulate, and that also therefore means that it is reserved for
developers and experienced professionals having in-depth computer knowledge.
Users are therefore encouraged to load and test the software’s suitability as
regards their requirements in conditions enabling the security of their systems
and/or data to be ensured and, more generally, to use and operate it in the same
conditions as regards security.

The fact that you are presently reading this means that you have had knowledge
of the CeCILL license and that you accept its terms.

### Libraries

- DomPdf: LGPL-2.1 License


Copyright
---------

* Copyright Daniel Berthereau, 2017-2022 (see [Daniel-KM] on GitLab)

See included libraries for copyright of dependencies.


[Pdf Creator]: https://gitlab.com/Daniel-KM/Omeka-S-module-PdfCreator
[Omeka S]: https://omeka.org/s
[DomPdf]: https://github.com/dompdf/dompdf
[Installing a module]: https://omeka.org/s/docs/user-manual/modules/
[Generic]: https://gitlab.com/Daniel-KM/Omeka-S-module-Generic
[its documentation]: https://github.com/dompdf/dompdf
[here]: https://eclecticgeek.com/dompdf/debug.php
[module issues]: https://gitlab.com/Daniel-KM/Omeka-S-module-PdfCreator/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[MIT]: https://github.com/sandywalker/webui-popover/blob/master/LICENSE.txt
[GitLab]: https://gitlab.com/Daniel-KM
[Daniel-KM]: https://gitlab.com/Daniel-KM "Daniel Berthereau"
