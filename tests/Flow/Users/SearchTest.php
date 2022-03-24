<?php

namespace App\Tests\Flow\Users;

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
        $user1 = $fixtures->addUser(['email' => '4@e.com']);
        $user2 = $fixtures->addUser(['email' => '5@e.com']);
        $user3 = $fixtures->addUser(['email' => '6@e.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'users' => [
                    1 => $user1->jsonSerialize(),
                    2 => $user2->jsonSerialize(),
                    3 => $user3->jsonSerialize(),
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

        $this->client->request('GET', '/users', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('users.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByTermSearchEmail(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => '4@e.com']);
        $user2 = $fixtures->addUser(['email' => '5@e.com']);
        $fixtures->addUser(['email' => '6@e.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'users' => [
                    2 => $user2->jsonSerialize(),
                ],
                'report' => [
                    'search' => '5',
                    'start' => 0,
                    'count' => 50,
                    'total' => 1,
                    'prev' => '',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request('GET', '/users', ['search' => '5'], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('users.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByTermOffset(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => '4@e.com']);
        $user2 = $fixtures->addUser(['email' => '5@e.com']);
        $user3 = $fixtures->addUser(['email' => '6@e.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'users' => [
                    2 => $user2->jsonSerialize(),
                    3 => $user3->jsonSerialize(),
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

        $this->client->request('GET', '/users', ['start' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('users.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByTermOffsetAndLimit(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => '4@e.com']);
        $user2 = $fixtures->addUser(['email' => '5@e.com']);
        $fixtures->addUser(['email' => '6@e.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'users' => [
                    2 => $user2->jsonSerialize(),
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

        $this->client->request('GET', '/users', ['start' => 1, 'count' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('users.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByIdsListAll(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '4@e.com']);
        $fixtures->addUser(['email' => '5@e.com']);
        $user3 = $fixtures->addUser(['email' => '6@e.com']);
        $this->loadFixtures($fixtures);

        $search = [$user1->getId(), $user3->getId()];
        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'users' => [
                    1 => $user1->jsonSerialize(),
                    3 => $user3->jsonSerialize(),
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

        $this->client->request('GET', '/users', ['search' => $search], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('users.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByIdsWithOffset(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '1@e.com']);
        $fixtures->addUser(['email' => '2@e.com']);
        $user3 = $fixtures->addUser(['email' => '3@e.com']);
        $fixtures->addUser(['username' => 'username.4', 'email5' => '4@e.com']);
        $user5 = $fixtures->addUser(['username' => 'username.5', 'email' => '5@e.com']);
        $this->loadFixtures($fixtures);

        $search = [$user1->getId(), $user3->getId(), $user5->getId()];
        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'users' => [
                    3 => $user3->jsonSerialize(),
                    5 => $user5->jsonSerialize(),
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

        $this->client->request('GET', '/users', ['search' => $search, 'start' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('users.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByIdsWithOffsetAndCount(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '1@e.com']);
        $fixtures->addUser(['email' => '2@e.com']);
        $user3 = $fixtures->addUser(['email' => '3@e.com']);
        $fixtures->addUser(['username' => 'username.4', 'email5' => '4@e.com']);
        $user5 = $fixtures->addUser(['username' => 'username.5', 'email' => '5@e.com']);
        $this->loadFixtures($fixtures);

        $search = [$user1->getId(), $user3->getId(), $user5->getId()];
        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'users' => [
                    3 => $user3->jsonSerialize(),
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

        $this->client->request('GET', '/users', ['search' => $search, 'start' => 1, 'count' => 1], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('users.search', Response::HTTP_OK);
    }
}
