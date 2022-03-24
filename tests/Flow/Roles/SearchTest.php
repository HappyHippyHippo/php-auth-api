<?php

namespace App\Tests\Flow\Roles;

use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class SearchTest extends EndpointTester
{
    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByTermListAll(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    1 => $role1->jsonSerialize(),
                    2 => $role2->jsonSerialize(),
                    3 => $role3->jsonSerialize(),
                ],
                'report' => [
                    'search' => '',
                    'start' => 0,
                    'count' => 50,
                    'total' => 3,
                    'prev' => '',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request('GET', '/roles', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('roles.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByTermSearchName(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $fixtures->addRole(['name' => '__dummy_name_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    2 => $role2->jsonSerialize(),
                ],
                'report' => [
                    'search' => '2',
                    'start' => 0,
                    'count' => 50,
                    'total' => 1,
                    'prev' => '',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request('GET', '/roles', ['search' => '2'], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('roles.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByTermOffset(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    2 => $role2->jsonSerialize(),
                    3 => $role3->jsonSerialize(),
                ],
                'report' => [
                    'search' => '',
                    'start' => 1,
                    'count' => 50,
                    'total' => 3,
                    'prev' => '?search=&start=0&count=50',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request('GET', '/roles', ['start' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('roles.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByTermOffsetAndLimit(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $fixtures->addRole(['name' => '__dummy_name_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    2 => $role2->jsonSerialize(),
                ],
                'report' => [
                    'search' => '',
                    'start' => 1,
                    'count' => 1,
                    'total' => 3,
                    'prev' => '?search=&start=0&count=1',
                    'next' => '?search=&start=2&count=1'
                ],
            ],
        ];

        $this->client->request('GET', '/roles', ['start' => 1, 'count' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('roles.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByIdsListAll(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $this->loadFixtures($fixtures);

        $search = [$role1->getId(), $role3->getId()];
        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    1 => $role1->jsonSerialize(),
                    3 => $role3->jsonSerialize(),
                ],
                'report' => [
                    'search' => json_encode($search),
                    'start' => 0,
                    'count' => 50,
                    'total' => 2,
                    'prev' => '',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request('GET', '/roles', ['search' => $search], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('roles.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByIdsWithOffset(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $fixtures->addRole(['name' => '__dummy_name_4__']);
        $role5 = $fixtures->addRole(['name' => '__dummy_name_5__']);
        $this->loadFixtures($fixtures);

        $search = [$role1->getId(), $role3->getId(), $role5->getId()];
        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    3 => $role3->jsonSerialize(),
                    5 => $role5->jsonSerialize(),
                ],
                'report' => [
                    'search' => json_encode($search),
                    'start' => 1,
                    'count' => 50,
                    'total' => 3,
                    'prev' => '?search=' . json_encode($search) . '&start=0&count=50',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request('GET', '/roles', ['search' => $search, 'start' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('roles.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByIdsWithOffsetAndCount(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $fixtures->addRole(['name' => '__dummy_name_4__']);
        $role5 = $fixtures->addRole(['name' => '__dummy_name_5__']);
        $this->loadFixtures($fixtures);

        $search = [$role1->getId(), $role3->getId(), $role5->getId()];
        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    3 => $role3->jsonSerialize(),
                ],
                'report' => [
                    'search' => json_encode($search),
                    'start' => 1,
                    'count' => 1,
                    'total' => 3,
                    'prev' => '?search=' . json_encode($search) . '&start=0&count=1',
                    'next' => '?search=' . json_encode($search) . '&start=2&count=1'
                ],
            ],
        ];

        $this->client->request('GET', '/roles', ['search' => $search, 'start' => 1, 'count' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('roles.search', Response::HTTP_OK);
    }
}
