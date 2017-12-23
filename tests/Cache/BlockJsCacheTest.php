<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PageBundle\Tests\Page;

use PHPUnit\Framework\TestCase;
use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\Cache\CacheElement;
use Sonata\PageBundle\Cache\BlockJsCache;
use Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface;
use Symfony\Component\Routing\RouterInterface;

class BlockJsCacheTest extends TestCase
{
    /**
     * @dataProvider getExceptionCacheKeys
     */
    public function testExceptions($keys)
    {
        $this->expectException(\RuntimeException::class);

        $router = $this->createMock(RouterInterface::class);

        $cmsManager = $this->createMock(CmsManagerSelectorInterface::class);
        $blockRenderer = $this->createMock(BlockRendererInterface::class);
        $contextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockJsCache($router, $cmsManager, $blockRenderer, $contextManager);

        $cache->get($keys, 'data');
    }

    public static function getExceptionCacheKeys()
    {
        return [
            [[]],
            [['block_id' => 7]],
            [['block_id' => 7, 'page_id' => 8]],
            [['block_id' => 7, 'manager' => 8]],
            [['manager' => 7, 'page_id' => 8]],
            [['manager' => 7, 'page_id' => 8]],
            [['updated_at' => 'foo']],
        ];
    }

    public function testInitCache()
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->once())->method('generate')->will($this->returnValue('https://sonata-project.org/page/cache/js/block.js'));

        $cmsSelectorManager = $this->createMock(CmsManagerSelectorInterface::class);
        $blockRenderer = $this->createMock(BlockRendererInterface::class);
        $contextManager = $this->createMock(BlockContextManagerInterface::class);

        $cache = new BlockJsCache($router, $cmsSelectorManager, $blockRenderer, $contextManager);

        $this->assertTrue($cache->flush([]));
        $this->assertTrue($cache->flushAll());

        $keys = [
            'block_id' => 4,
            'page_id' => 5,
            'updated_at' => 'as',
            'manager' => 'page',
        ];

        $cacheElement = $cache->set($keys, 'data');

        $this->assertInstanceOf(CacheElement::class, $cacheElement);

        $this->assertTrue($cache->has(['id' => 7]));

        $cacheElement = $cache->get($keys);

        $this->assertInstanceOf(CacheElement::class, $cacheElement);

        $expected = <<<'EXPECTED'
<div id="block-cms-4" >
    <script>
        /*<![CDATA[*/
            (function() {
                var b = document.createElement("script");
                b.type = "text/javascript";
                b.async = true;
                b.src = "https://sonata-project.org/page/cache/js/block.js";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(b, s);
            })();

        /*]]>*/
    </script>
</div>
EXPECTED;

        $this->assertEquals($expected, $cacheElement->getData()->getContent());
    }
}
