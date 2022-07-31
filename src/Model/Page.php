<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Page.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
abstract class Page implements PageInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var \DateTimeInterface|null
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $routeName;

    /**
     * @var string|null
     */
    protected $pageAlias;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $slug;

    /**
     * @var string|null
     */
    protected $url;

    /**
     * @var string|null
     */
    protected $customUrl;

    /**
     * @var string|null
     */
    protected $requestMethod;

    /**
     * @var string|null
     */
    protected $metaKeyword;

    /**
     * @var string|null
     */
    protected $metaDescription;

    /**
     * @var string|null
     */
    protected $javascript;

    /**
     * @var string|null
     */
    protected $stylesheet;

    /**
     * @var string|null
     */
    protected $rawHeaders;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var Collection<array-key, PageBlockInterface>
     */
    protected $blocks;

    /**
     * @var PageInterface|null
     */
    protected $parent;

    /**
     * @var PageInterface[]
     */
    protected $parents;

    /**
     * @var Collection<array-key, PageInterface>
     */
    protected $children;

    /**
     * @var SnapshotInterface[]
     */
    protected $snapshots = [];

    /**
     * @var string|null
     */
    protected $templateCode;

    /**
     * @var int
     */
    protected $position = 1;

    /**
     * @var bool
     */
    protected $decorate = true;

    /**
     * @var SiteInterface|null
     */
    protected $site;

    /**
     * @var bool
     */
    protected $edited;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->routeName = PageInterface::PAGE_ROUTE_CMS_NAME;
        $this->requestMethod = 'GET|POST|HEAD|DELETE|PUT';
        $this->edited = true;
    }

    public function __toString()
    {
        return $this->getName() ?: '-';
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setRouteName($routeName): void
    {
        $this->routeName = $routeName;
    }

    public function getRouteName()
    {
        return $this->routeName;
    }

    public function setPageAlias($pageAlias): void
    {
        if ('_page_alias_' !== substr((string) $pageAlias, 0, 12)) {
            $pageAlias = '_page_alias_'.$pageAlias;
        }

        $this->pageAlias = $pageAlias;
    }

    public function getPageAlias()
    {
        return $this->pageAlias;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setCustomUrl($customUrl): void
    {
        $this->customUrl = $customUrl;
    }

    public function getCustomUrl()
    {
        return $this->customUrl;
    }

    public function setRequestMethod($method): void
    {
        $this->requestMethod = $method;
    }

    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    public function setMetaKeyword($metaKeyword): void
    {
        $this->metaKeyword = $metaKeyword;
    }

    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }

    public function setMetaDescription($metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    public function setJavascript($javascript): void
    {
        $this->javascript = $javascript;
    }

    public function getJavascript()
    {
        return $this->javascript;
    }

    public function setStylesheet($stylesheet): void
    {
        $this->stylesheet = $stylesheet;
    }

    public function getStylesheet()
    {
        return $this->stylesheet;
    }

    public function setRawHeaders($rawHeaders): void
    {
        $headers = $this->getHeadersAsArray($rawHeaders);

        $this->setHeaders($headers);
    }

    public function getRawHeaders()
    {
        return $this->rawHeaders;
    }

    public function addHeader($name, $value): void
    {
        $headers = $this->getHeaders();

        $headers[$name] = $value;

        $this->headers = $headers;

        $this->rawHeaders = $this->getHeadersAsString($headers);
    }

    public function setHeaders(array $headers = []): void
    {
        $this->headers = [];
        $this->rawHeaders = null;
        foreach ($headers as $name => $header) {
            $this->addHeader($name, $header);
        }
    }

    public function getHeaders(): array
    {
        if (null === $this->headers) {
            $rawHeaders = $this->getRawHeaders();
            $this->headers = $this->getHeadersAsArray($rawHeaders);
        }

        return $this->headers;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function addChild(PageInterface $child): void
    {
        $this->children[] = $child;

        $child->setParent($this);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children): void
    {
        $this->children = $children;
    }

    public function getSnapshot()
    {
        return $this->snapshots[0] ?? null;
    }

    public function getSnapshots()
    {
        return $this->snapshots;
    }

    public function setSnapshots($snapshots): void
    {
        $this->snapshots = $snapshots;
    }

    public function addSnapshot(SnapshotInterface $snapshot): void
    {
        $this->snapshots[] = $snapshot;

        $snapshot->setPage($this);
    }

    public function addBlocks(PageBlockInterface $block): void
    {
        $block->setPage($this);

        $this->blocks[] = $block;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    public function setParent(?PageInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    public function getParent($level = -1)
    {
        if (-1 === $level) {
            return $this->parent;
        }

        $parents = $this->getParents();

        if ($level < 0) {
            $level = \count($parents) + $level;
        }

        return $parents[$level] ?? null;
    }

    public function setParents(array $parents): void
    {
        $this->parents = $parents;
    }

    public function getParents()
    {
        if (!$this->parents) {
            $page = $this;
            $parents = [];

            while ($page->getParent()) {
                $page = $page->getParent();
                $parents[] = $page;
            }

            $this->setParents(array_reverse($parents));
        }

        return $this->parents;
    }

    public function setTemplateCode($templateCode): void
    {
        $this->templateCode = $templateCode;
    }

    public function getTemplateCode()
    {
        return $this->templateCode;
    }

    public function setDecorate($decorate): void
    {
        $this->decorate = $decorate;
    }

    public function getDecorate()
    {
        return $this->decorate;
    }

    public function isHybrid()
    {
        return PageInterface::PAGE_ROUTE_CMS_NAME !== $this->getRouteName() && !$this->isInternal();
    }

    public function isCms()
    {
        return PageInterface::PAGE_ROUTE_CMS_NAME === $this->getRouteName() && !$this->isInternal();
    }

    public function isInternal()
    {
        return '_page_internal_' === substr($this->getRouteName(), 0, 15);
    }

    public function isDynamic()
    {
        return $this->isHybrid() && false !== strpos($this->getUrl(), '{');
    }

    public function isError()
    {
        return '_page_internal_error_' === substr($this->getRouteName(), 0, 21);
    }

    public function setPosition($position): void
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setUrl($url): void
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Retrieve a block by code.
     *
     * @param string $code
     *
     * @return PageBlockInterface
     */
    public function getContainerByCode($code)
    {
        $block = null;

        foreach ($this->getBlocks() as $blockTmp) {
            if (\in_array($blockTmp->getType(), ['sonata.page.block.container', 'sonata.block.service.container'], true) && $blockTmp->getSetting('code') === $code) {
                $block = $blockTmp;

                break;
            }
        }

        return $block;
    }

    /**
     * Retrieve blocks by type.
     *
     * @param string $type
     *
     * @return array
     */
    public function getBlocksByType($type)
    {
        $blocks = [];

        foreach ($this->getBlocks() as $block) {
            if ($type === $block->getType()) {
                $blocks[] = $block;
            }
        }

        return $blocks;
    }

    public function hasRequestMethod($method)
    {
        $method = strtoupper($method);

        if (!\in_array($method, ['PUT', 'POST', 'GET', 'DELETE', 'HEAD'], true)) {
            return false;
        }

        return !$this->getRequestMethod() || false !== strpos($this->getRequestMethod(), $method);
    }

    public function setSite(?SiteInterface $site = null): void
    {
        $this->site = $site;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setEdited($edited): void
    {
        $this->edited = $edited;
    }

    public function getEdited()
    {
        return $this->edited;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Converts the headers passed as string to an array.
     *
     * @param string $rawHeaders The headers
     *
     * @return array
     */
    protected function getHeadersAsArray($rawHeaders)
    {
        $headers = [];

        foreach (explode("\r\n", (string) $rawHeaders) as $header) {
            if (false !== strpos($header, ':')) {
                [$name, $headerStr] = explode(':', $header, 2);
                $headers[trim($name)] = trim($headerStr);
            }
        }

        return $headers;
    }

    /**
     * Converts the headers passed as an associative array to a string.
     *
     * @param array $headers The headers
     *
     * @return string
     */
    protected function getHeadersAsString(array $headers)
    {
        $rawHeaders = [];

        foreach ($headers as $name => $header) {
            $rawHeaders[] = sprintf('%s: %s', trim($name), trim($header));
        }

        $rawHeaders = implode("\r\n", $rawHeaders);

        return $rawHeaders;
    }
}
