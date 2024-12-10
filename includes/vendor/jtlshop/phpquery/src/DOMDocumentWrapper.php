<?php

namespace JTL\phpQuery;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Exception;

/**
 * DOMDocumentWrapper class simplifies work with DOMDocument.
 *
 * Know bug:
 * - in XHTML fragments, <br /> changes to <br clear="none" />
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 * @package JTL\phpQuery
 */
class DOMDocumentWrapper
{
    /**
     * @var DOMDocument
     */
    public $document;

    /**
     * @var null|string
     */
    public $id;

    /**
     * @todo Rewrite as method and quess if null.
     * @var string
     */
    public $contentType = '';

    /**
     * @var DOMXPath
     */
    public $xpath;

    /**
     * @var int
     */
    public $uuid = 0;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var array
     */
    public array $dataNodes = [];

    /**
     * @var array
     */
    public array $events = [];

    /**
     * @var array
     */
    public array $eventsNodes = [];

    /**
     * @var array
     */
    public array $eventsGlobal = [];

    /**
     * Document root, by default equals to document itself.
     * Used by documentFragments.
     *
     * @var DOMNode
     */
    public $root;

    /**
     * @var bool|null
     */
    public $isDocumentFragment;

    /**
     * @var bool
     */
    public bool $isXML = false;

    /**
     * @var bool
     */
    public bool $isXHTML = false;

    /**
     * @var bool
     */
    public bool $isHTML = false;

    /**
     * @var string
     */
    public $charset;

    /**
     * DOMDocumentWrapper constructor.
     * @param string|null $markup
     * @param string|null $contentType
     * @param string|null $newDocumentID
     */
    public function __construct(string $markup = null, string $contentType = null, string $newDocumentID = null)
    {
        if ($markup !== null) {
            $this->load($markup, $contentType);
        }
        $this->id = $newDocumentID ?? \md5(\microtime());
    }

    /**
     * @param string      $markup
     * @param string|null $contentType
     * @return bool
     * @throws Exception
     */
    public function load(string $markup, string $contentType = null): bool
    {
        $this->contentType = \strtolower($contentType);
        $loaded            = $this->loadMarkup($markup);
        if ($loaded) {
            $this->document->preserveWhiteSpace = true;
            $this->xpath                        = new DOMXPath($this->document);
            $this->afterMarkupLoad();

            return true;
        }

        return false;
    }

    /**
     *
     */
    protected function afterMarkupLoad(): void
    {
        if ($this->isXHTML) {
            $this->xpath->registerNamespace('html', 'http://www.w3.org/1999/xhtml');
        }
    }

    /**
     * @param $markup
     * @return bool|mixed
     * @throws Exception
     */
    protected function loadMarkup($markup)
    {
        $loaded = false;
        if ($this->contentType) {
            // content determined by contentType
            [$contentType, $charset] = $this->contentTypeToArray($this->contentType);
            switch ($contentType) {
                case 'text/html':
                    $loaded = $this->loadMarkupHTML($markup, $charset);
                    break;
                case 'text/xml':
                case 'application/xhtml+xml':
                    $loaded = $this->loadMarkupXML($markup, $charset);
                    break;
                default:
                    // for feeds or anything that sometimes doesn't use text/xml
                    if (\str_contains('xml', $this->contentType)) {
                        $loaded = $this->loadMarkupXML($markup, $charset);
                    } else {
                        phpQuery::debug("Could not determine document type from content type '{$this->contentType}'");
                    }
            }
        } elseif ($this->isXML($markup)) {
            $loaded = $this->loadMarkupXML($markup);
            if (!$loaded && $this->isXHTML) {
                $loaded = $this->loadMarkupHTML($markup);
            }
        } else {
            $loaded = $this->loadMarkupHTML($markup);
        }

        return $loaded;
    }

    /**
     *
     */
    protected function loadMarkupReset(): void
    {
        $this->isXML   = false;
        $this->isXHTML = false;
        $this->isHTML  = false;
    }

    /**
     * @param string $charset
     * @param string $version
     */
    protected function documentCreate($charset, $version = '1.0'): void
    {
        if (!$version) {
            $version = '1.0';
        }
        $this->document                     = new DOMDocument($version, $charset);
        $this->charset                      = $this->document->encoding;
        $this->document->formatOutput       = true;
        $this->document->preserveWhiteSpace = true;
    }

    /**
     * @param string      $markup
     * @param string|null $requestedCharset
     * @return bool
     */
    protected function loadMarkupHTML($markup, $requestedCharset = null): bool
    {
        if (phpQuery::$debug) {
            phpQuery::debug('Full markup load (HTML): ' . \substr($markup, 0, 250));
        }
        $this->loadMarkupReset();
        $this->isHTML = true;
        if ($this->isDocumentFragment === null) {
            $this->isDocumentFragment = self::isDocumentFragmentHTML($markup);
        }
        $charset         = null;
        $documentCharset = $this->charsetFromHTML($markup);
        if ($documentCharset) {
            $charset = $documentCharset;
            $markup  = $this->charsetFixHTML($markup);
        } elseif ($requestedCharset) {
            $charset = $requestedCharset;
        }
        if (!$charset) {
            $charset = phpQuery::$defaultCharset;
        }
        // HTTP 1.1 says that the default charset is ISO-8859-1
        // @see http://www.w3.org/International/O-HTTP-charset
        if (!$documentCharset) {
            $documentCharset = \JTL_CHARSET;
        }
        // Should be careful here, still need 'magic encoding detection' since lots of pages have other 'default encoding'
        // Worse, some pages can have mixed encodings... we'll try not to worry about that
        $requestedCharset = \strtoupper($requestedCharset ?? '');
        $documentCharset  = \strtoupper($documentCharset ?? '');
        if ($requestedCharset && $documentCharset && $requestedCharset !== $documentCharset) {
            // Document Encoding Conversion
            // http://code.google.com/p/phpquery/issues/detail?id=86
            if (\function_exists('mb_detect_encoding')) {
                $possibleCharsets = [$documentCharset, $requestedCharset, 'AUTO'];
                $docEncoding      = mb_detect_encoding($markup, \implode(', ', $possibleCharsets));
                if (!$docEncoding) {
                    $docEncoding = $documentCharset;
                } // ok trust the document
                // Detected does not match what document says...
                if ($docEncoding !== $documentCharset) {
                    // Tricky..
                }
                if ($docEncoding !== $requestedCharset) {
                    $markup  = mb_convert_encoding($markup, $requestedCharset, $docEncoding);
                    $charset = $requestedCharset;
                }
            } else {
                phpQuery::debug('TODO: charset conversion without mbstring...');
            }
        }
        if ($this->isDocumentFragment) {
            $return = $this->documentFragmentLoadMarkup($this, $charset, $markup);
        } else {
            $this->documentCreate($charset);
            $return = phpQuery::$debug === 2
                ? $this->document->loadHTML($markup, \LIBXML_SCHEMA_CREATE)
                : @$this->document->loadHTML($markup, \LIBXML_SCHEMA_CREATE | \LIBXML_NOWARNING | \LIBXML_NONET | \LIBXML_NOERROR);
            if ($return) {
                $this->root = $this->document;
            }
        }
        if ($return && !$this->contentType) {
            $this->contentType = 'text/html';
        }

        return $return;
    }

    /**
     * @param      $markup
     * @param null $requestedCharset
     * @return bool|mixed
     * @throws Exception
     */
    protected function loadMarkupXML($markup, $requestedCharset = null)
    {
        if (phpQuery::$debug) {
            phpQuery::debug('Full markup load (XML): ' . \substr($markup, 0, 250));
        }
        $this->loadMarkupReset();
        $this->isXML = true;
        // check agains XHTML in contentType or markup
        $isContentTypeXHTML = $this->isXHTML();
        $isMarkupXHTML      = $this->isXHTML($markup);
        if ($isContentTypeXHTML || $isMarkupXHTML) {
            $this->isXHTML = true;
        }
        // determine document fragment
        if ($this->isDocumentFragment === null) {
            $this->isDocumentFragment = $this->isXHTML
                ? self::isDocumentFragmentXHTML($markup)
                : self::isDocumentFragmentXML($markup);
        }
        // this charset will be used
        $charset = null;
        // charset from XML declaration @var string
        $documentCharset = $this->charsetFromXML($markup);
        if (!$documentCharset) {
            if ($this->isXHTML) {
                // this is XHTML, try to get charset from content-type meta header
                $documentCharset = $this->charsetFromHTML($markup);
                if ($documentCharset) {
                    $this->charsetAppendToXML($markup, $documentCharset);
                    $charset = $documentCharset;
                }
            }
            if (!$documentCharset) {
                // if still no document charset...
                $charset = $requestedCharset;
            }
        } elseif ($requestedCharset) {
            $charset = $requestedCharset;
        }
        if (!$charset) {
            $charset = phpQuery::$defaultCharset;
        }
        if ($this->isDocumentFragment) {
            $return = $this->documentFragmentLoadMarkup($this, $charset, $markup);
        } else {
            if ($isContentTypeXHTML && !$isMarkupXHTML && !$documentCharset) {
                $markup = $this->charsetAppendToXML($markup, $charset);
            }
            // see http://pl2.php.net/manual/en/book.dom.php#78929
            // LIBXML_DTDLOAD (>= PHP 5.1)
            // does XML ctalogues works with LIBXML_NONET
            //      $this->document->resolveExternals = true;
            // TODO test LIBXML_COMPACT for performance improvement
            // create document
            $this->documentCreate($charset);
            /** @link http://pl2.php.net/manual/en/libxml.constants.php */
            $libxmlStatic = phpQuery::$debug === 2
                ? \LIBXML_DTDLOAD | \LIBXML_DTDATTR | \LIBXML_NONET
                : \LIBXML_DTDLOAD | \LIBXML_DTDATTR | \LIBXML_NONET | \LIBXML_NOWARNING | \LIBXML_NOERROR;
            $return       = $this->document->loadXML($markup, $libxmlStatic);
            if ($return) {
                $this->root = $this->document;
            }
        }
        if ($return) {
            if (!$this->contentType) {
                if ($this->isXHTML) {
                    $this->contentType = 'application/xhtml+xml';
                } else {
                    $this->contentType = 'text/xml';
                }
            }

            return $return;
        }
        throw new Exception('Error loading XML markup');
    }

    /**
     * @param string|null $markup
     * @return bool
     */
    protected function isXHTML($markup = null): bool
    {
        if (!isset($markup)) {
            return \str_contains($this->contentType, 'xhtml');
        }

        return \str_contains($markup, '<!DOCTYPE html');
    }

    /**
     * @param string $markup
     * @return bool
     */
    protected function isXML($markup): bool
    {
        return \str_contains(\substr($markup, 0, 100), '<' . '?xml');
    }

    /**
     * @param string $contentType
     * @return array
     */
    protected function contentTypeToArray($contentType): array
    {
        $matches = \explode(';', \strtolower(\trim($contentType)));
        if (isset($matches[1])) {
            $matches[1] = \explode('=', $matches[1]);
            // strip 'charset='
            $matches[1] = isset($matches[1][1]) && \trim($matches[1][1])
                ? $matches[1][1]
                : $matches[1][0];
        } else {
            $matches[1] = null;
        }

        return $matches;
    }

    /**
     * @param string $markup
     * @return array contentType, charset
     */
    protected function contentTypeFromHTML($markup): array
    {
        $matches = [];
        // find meta tag
        \preg_match(
            '@<meta[^>]+http-equiv\\s*=\\s*(["|\'])Content-Type\\1([^>]+?)>@i',
            $markup,
            $matches
        );
        if (!isset($matches[0])) {
            return [null, null];
        }
        // get attr 'content'
        \preg_match('@content\\s*=\\s*(["|\'])(.+?)\\1@', $matches[0], $matches);
        if (!isset($matches[0])) {
            return [null, null];
        }

        return $this->contentTypeToArray($matches[2]);
    }

    /**
     * @param string $markup
     * @return mixed
     */
    protected function charsetFromHTML($markup)
    {
        $contentType = $this->contentTypeFromHTML($markup);

        return $contentType[1];
    }

    /**
     * @param string $markup
     * @return null|string
     */
    protected function charsetFromXML($markup): ?string
    {
        // find declaration
        \preg_match(
            '@<' . '?xml[^>]+encoding\\s*=\\s*(["|\'])(.*?)\\1@i',
            $markup,
            $matches
        );

        return isset($matches[2])
            ? \strtolower($matches[2])
            : null;
    }

    /**
     * Repositions meta[type=charset] at the start of head. Bypasses DOMDocument bug.
     *
     * @link http://code.google.com/p/phpquery/issues/detail?id=80
     * @param string $markup
     * @return string|null
     */
    protected function charsetFixHTML($markup): ?string
    {
        $matches = [];
        // find meta tag
        \preg_match(
            '@\s*<meta[^>]+http-equiv\\s*=\\s*(["|\'])Content-Type\\1([^>]+?)>@i',
            $markup,
            $matches,
            \PREG_OFFSET_CAPTURE
        );
        if (!isset($matches[0])) {
            return null;
        }
        $metaContentType = $matches[0][0];
        $markup          = \substr($markup, 0, $matches[0][1])
            . \substr($markup, $matches[0][1] + \strlen($metaContentType));
        $headStart       = \stripos($markup, '<head>');

        return \substr($markup, 0, $headStart + 6) .
            $metaContentType .
            \substr($markup, $headStart + 6);
    }

    /**
     * @param string $html
     * @param string $charset
     * @param bool   $xhtml
     * @return mixed|string
     */
    protected function charsetAppendToHTML($html, $charset, $xhtml = false)
    {
        // remove existing meta[type=content-type]
        $html = \preg_replace('@\s*<meta[^>]+http-equiv\\s*=\\s*(["|\'])Content-Type\\1([^>]+?)>@i', '', $html);
        $meta = '<meta http-equiv="Content-Type" content="text/html;charset='
            . $charset . '" '
            . ($xhtml ? '/' : '')
            . '>';
        if (!\str_contains($html, '<head')) {
            if (!\str_contains($html, '<html')) {
                return $meta . $html;
            }
            return \preg_replace(
                '@<html(.*?)(?(?<!\?)>)@s',
                "<html\\1><head>{$meta}</head>",
                $html
            );
        }

        return \preg_replace(
            '@<head(.*?)(?(?<!\?)>)@s',
            '<head\\1>' . $meta,
            $html
        );
    }

    /**
     * @param string $markup
     * @param string $charset
     * @return string
     */
    protected function charsetAppendToXML($markup, $charset): string
    {
        return '<' . '?xml version="1.0" encoding="' . $charset . '"?' . '>' . $markup;
    }

    /**
     * @param string $markup
     * @return bool
     */
    public static function isDocumentFragmentHTML($markup): bool
    {
        return \stripos($markup, '<html') === false && \stripos($markup, '<!doctype') === false;
    }

    /**
     * @param string $markup
     * @return bool
     */
    public static function isDocumentFragmentXML($markup): bool
    {
        return \stripos($markup, '<' . '?xml') === false;
    }

    /**
     * @param string $markup
     * @return bool
     */
    public static function isDocumentFragmentXHTML($markup): bool
    {
        return self::isDocumentFragmentHTML($markup);
    }

    /**
     * @param mixed $value
     */
    public function importAttr($value): void
    {
    }

    /**
     * @param DOMNode|DOMNodeList|array $source
     * @param string|null               $sourceCharset
     * @return DOMNode[]
     * @throws Exception
     */
    public function import($source, $sourceCharset = null): array
    {
        $return = [];
        if ($source instanceof DOMNode && !($source instanceof DOMNodeList)) {
            $source = [$source];
        }
        if (\is_array($source) || $source instanceof DOMNodeList) {
            // dom nodes
            foreach ($source as $node) {
                $return[] = $this->document->importNode($node, true);
            }
        } else {
            // string markup
            $fake = $this->documentFragmentCreate($source, $sourceCharset);
            if ($fake === false) {
                throw new Exception('Error loading documentFragment markup');
            }

            return $this->import($fake->root->childNodes);
        }

        return $return;
    }

    /**
     * Creates new document fragment.
     *
     * @param string $source
     * @param string $charset
     * @return DOMDocumentWrapper|bool
     * @throws Exception
     */
    protected function documentFragmentCreate($source, $charset = null)
    {
        $fake              = new DOMDocumentWrapper();
        $fake->contentType = $this->contentType;
        $fake->isXML       = $this->isXML;
        $fake->isHTML      = $this->isHTML;
        $fake->isXHTML     = $this->isXHTML;
        $fake->root        = $fake->document;
        if (!$charset) {
            $charset = $this->charset;
        }
        if ($source instanceof DOMNode && !($source instanceof DOMNodeList)) {
            $source = [$source];
        }
        if (\is_array($source) || $source instanceof DOMNodeList) {
            // dom nodes
            // load fake document
            if (!$this->documentFragmentLoadMarkup($fake, $charset)) {
                return false;
            }
            $nodes = $fake->import($source);
            foreach ($nodes as $node) {
                $fake->root->appendChild($node);
            }
        } else {
            // string markup
            $this->documentFragmentLoadMarkup($fake, $charset, $source);
        }

        return $fake;
    }

    /**
     * @param DOMDocumentWrapper  $fragment
     * @param string      $charset
     * @param string|null $markup
     * @return bool
     */
    private function documentFragmentLoadMarkup($fragment, $charset, $markup = null): bool
    {
        // tempolary turn off
        $fragment->isDocumentFragment = false;
        if ($fragment->isXML) {
            if ($fragment->isXHTML) {
                // add FAKE element to set default namespace
                $fragment->loadMarkupXML(
                    '<?xml version="1.0" encoding="' . $charset . '"?>'
                    . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '
                    . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
                    . '<fake xmlns="http://www.w3.org/1999/xhtml">' . $markup . '</fake>'
                );
                $fragment->root = $fragment->document->firstChild->nextSibling;
            } else {
                $fragment->loadMarkupXML('<?xml version="1.0" encoding="' . $charset . '"?><fake>' . $markup . '</fake>');
                $fragment->root = $fragment->document->firstChild;
            }
        } else {
            $markup2 = phpQuery::$defaultDoctype . '<html><head><meta http-equiv="Content-Type" content="text/html;charset='
                . $charset . '"></head>';
            $noBody  = $markup === null || !\str_contains($markup, '<body');
            if ($noBody) {
                $markup2 .= '<body>';
            }
            $markup2 .= $markup;
            if ($noBody) {
                $markup2 .= '</body>';
            }
            $markup2 .= '</html>';
            $fragment->loadMarkupHTML($markup2);
            // TODO resolv body tag merging issue
            $fragment->root = $fragment->document->firstChild->nextSibling->firstChild->nextSibling;
        }
        if (!$fragment->root) {
            return false;
        }
        $fragment->isDocumentFragment = true;

        return true;
    }

    /**
     * @param DOMDocumentWrapper $fragment
     * @return string
     */
    protected function documentFragmentToMarkup($fragment): string
    {
        $tmp                          = $fragment->isDocumentFragment;
        $fragment->isDocumentFragment = false;
        $markup                       = $fragment->markup();
        if ($fragment->isXML) {
            $markup = \substr($markup, 0, \strrpos($markup, '</fake>'));
            if ($fragment->isXHTML) {
                $markup = \substr($markup, \strpos($markup, '<fake') + 43);
            } else {
                $markup = \substr($markup, \strpos($markup, '<fake>') + 6);
            }
        } else {
            $markup = \substr($markup, \strpos($markup, '<body>') + 6);
            $markup = \substr($markup, 0, \strrpos($markup, '</body>'));
        }
        $fragment->isDocumentFragment = $tmp;

        return $markup;
    }

    /**
     * Return document markup, starting with optional $nodes as root.
     *
     * @param DOMNodeList|DOMElement[]|null $nodes
     * @param bool $innerMarkup
     * @return string
     */
    public function markup($nodes = null, $innerMarkup = false): string
    {
        if (isset($nodes) && \count($nodes) === 1 && $nodes[0] instanceof DOMDocument) {
            $nodes = null;
        }
        if (isset($nodes)) {
            $markup = '';
            if (!\is_array($nodes) && !($nodes instanceof DOMNodeList)) {
                $nodes = [$nodes];
            }
            if ($this->isDocumentFragment && !$innerMarkup) {
                foreach ($nodes as $i => $node) {
                    if ($node->isSameNode($this->root)) {
                        $nodes = \array_slice($nodes, 0, $i)
                            + phpQuery::DOMNodeListToArray($node->childNodes)
                            + \array_slice($nodes, $i + 1);
                    }
                }
            }
            if ($this->isXML && !$innerMarkup) {
                // we need outerXML, so we can benefit from
                // $node param support in saveXML()
                foreach ($nodes as $node) {
                    $markup .= $this->document->saveXML($node);
                }
            } else {
                $loop = [];
                if ($innerMarkup) {
                    foreach ($nodes as $node) {
                        if ($node->childNodes) {
                            foreach ($node->childNodes as $child) {
                                $loop[] = $child;
                            }
                        } else {
                            $loop[] = $node;
                        }
                    }
                } else {
                    $loop = $nodes;
                }
                $fake   = $this->documentFragmentCreate($loop);
                $markup = $this->documentFragmentToMarkup($fake);
            }
            if ($this->isXHTML) {
                $markup = self::markupFixXHTML($markup);
            }

            return $markup;
        }
        if ($this->isDocumentFragment) {
            // no need for markupFixXHTML, as it's done thought markup($nodes) method
            return $this->documentFragmentToMarkup($this);
        }
        $markup = $this->isXML
            ? $this->document->saveXML()
            : $this->document->saveHTML();
        if ($this->isXHTML) {
            $markup = self::markupFixXHTML($markup);
        }

        return $markup;
    }

    /**
     * @param $markup
     * @return string
     */
    protected static function markupFixXHTML($markup): string
    {
        $markup = self::expandEmptyTag('script', $markup);
        $markup = self::expandEmptyTag('select', $markup);

        return self::expandEmptyTag('textarea', $markup);
    }

    /**
     * @param string $text
     */
    public static function debug($text): void
    {
        phpQuery::debug($text);
    }

    /**
     * expandEmptyTag
     *
     * @param string $tag
     * @param string $xml
     * @return string
     * @author mjaque at ilkebenson dot com
     * @link http://php.net/manual/en/domdocument.savehtml.php#81256
     */
    public static function expandEmptyTag($tag, $xml): string
    {
        $indice = 0;
        while ($indice < \strlen($xml)) {
            $pos = \strpos($xml, "<$tag ", $indice);
            if ($pos) {
                $posCierre = \strpos($xml, '>', $pos);
                if ($xml[$posCierre - 1] === '/') {
                    $xml = \substr_replace($xml, "></$tag>", $posCierre - 1, 2);
                }
                $indice = $posCierre;
            } else {
                break;
            }
        }

        return $xml;
    }
}
