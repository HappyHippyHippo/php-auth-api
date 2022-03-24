<?php

namespace App\Tests\Flow\Base;

use App\Tests\Flow\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class IndexTest extends EndpointTester
{
    /**
     * @return void
     */
    public function testCall(): void
    {
        $expected = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'name' => 'auth',
                'version' => 'development',
                'routes' => [
                    'auth.token.recover' => '[PUT] /auth',
                    'auth.token.check' => '[GET] /auth',
                    'auth.chap.request' => '[GET] /auth/chap',
                    'auth.chap.response' => '[POST] /auth/chap',
                    'auth.legacy' => '[POST] /auth/legacy',
                    'base.check.preflight' => '[OPTIONS] /__check',
                    'base.check' => '[GET] /__check',
                    'base.config.preflight' => '[OPTIONS] /__config',
                    'base.config' => '[GET] /__config',
                    'base.index.preflight' => '[OPTIONS] /',
                    'base.index' => '[GET] /',
                    'base.openapi.preflight' => '[OPTIONS] /__openapi',
                    'base.openapi' => '[GET] /__openapi',
                    'roles.get' => '[GET] /roles/{roleId}',
                    'roles.search' => '[GET] /roles',
                    'roles.create' => '[POST] /roles',
                    'roles.update' => '[POST] /roles/{roleId}',
                    'roles.disable' => '[POST] /roles/{roleId}/disable',
                    'roles.enable' => '[POST] /roles/{roleId}/enable',
                    'role_permissions.get' => '[GET] /roles/{roleId}/permissions/{permissionId}',
                    'role_permissions.search' => '[GET] /roles/{roleId}/permissions',
                    'role_permissions.create' => '[POST] /roles/{roleId}/permissions',
                    'role_permissions.update' => '[POST] /roles/{roleId}/permissions/{permissionId}',
                    'role_permissions.disable' => '[POST] /roles/{roleId}/permissions/{permissionId}/disable',
                    'role_permissions.enable' => '[POST] /roles/{roleId}/permissions/{permissionId}/enable',
                    'users.get' => '[GET] /users/{userId}',
                    'users.search' => '[GET] /users',
                    'users.create' => '[POST] /users',
                    'users.update' => '[POST] /users/{userId}',
                    'users.disable' => '[POST] /users/{userId}/disable',
                    'users.enable' => '[POST] /users/{userId}/enable',
                    'users.warmup' => '[POST] /users/{userId}/warmup',
                    'user_permissions.get' => '[GET] /users/{userId}/permissions/{permissionId}',
                    'user_permissions.search' => '[GET] /users/{userId}/permissions',
                    'user_permissions.create' => '[POST] /users/{userId}/permissions',
                    'user_permissions.update' => '[POST] /users/{userId}/permissions/{permissionId}',
                    'user_permissions.disable' => '[POST] /users/{userId}/permissions/{permissionId}/disable',
                    'user_permissions.enable' => '[POST] /users/{userId}/permissions/{permissionId}/enable',
                    'users.roles' => '[GET] /users/{userId}/roles',
                    'users.roles.add' => '[POST] /users/{userId}/roles',
                    'users.roles.remove' => '[DELETE] /users/{userId}/roles/{roleId}',
                    'users.roles.enable' => '[POST] /users/{userId}/roles/{roleId}/enable',
                    'users.roles.disable' => '[POST] /users/{userId}/roles/{roleId}/disable',
                    'users.roles.priority' => '[POST] /users/{userId}/roles/{roleId}/priority',
                ],
            ],
        ];

        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);

        $this->assertLogSuccess('base.index', Response::HTTP_OK);
    }
}
