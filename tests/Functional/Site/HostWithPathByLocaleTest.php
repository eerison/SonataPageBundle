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

namespace Sonata\PageBundle\Tests\Functional\Site;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HostWithPathByLocaleTest extends WebTestCase
{
    public function testMultiSiteWithSameHost(): void
    {
        $client = static::createClient(server: [
            'HTTP_HOST' => 'foo.example.com',
        ]);

        $client->request('GET', '/admin/tests/app/sonatapagesite/create', ['uniqid' => 'site']);
        $client->submitForm('btn_create_and_list', [
            'site[name]' => 'foo.br',
            'site[host]' => 'foo.example.com',
            'site[enabled]' => true,
            'site[isDefault]' => true,
            'site[relativePath]' => '/',
        ]);
        $client->followRedirect();

        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/tests/app/sonatapagepage/create', ['uniqid' => 'page', 'siteId' => 1]);
        $client->submitForm('btn_create_and_list', [
            'page[site]' => 1,
            'page[name]' => 'Name',
            'page[enabled]' => 1,
            'page[position]' => 1,
            'page[customUrl]' => '/',
            'page[title]' => 'Title',
        ]);

        self::assertResponseIsSuccessful();

        $client->request('GET', '/');

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
    }
}
