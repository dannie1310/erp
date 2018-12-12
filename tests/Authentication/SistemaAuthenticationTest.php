<?php

namespace App\Tests\Authentication;
use Laravel\Passport\Passport;
use App\User;
use App\Sistema;
use App\Permissions\SistemaPermissions;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SistemaAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $baseUrl;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        $this->user->givePermissionTo(SistemaPermissions::RETRIEVE_SISTEMA);
        $this->user->givePermissionTo(SistemaPermissions::RETRIEVE_ALL_SISTEMAS);
        $this->user->givePermissionTo(SistemaPermissions::CREATE_SISTEMA);
        $this->user->givePermissionTo(SistemaPermissions::UPDATE_SISTEMA);
        $this->user->givePermissionTo(SistemaPermissions::DELETE_SISTEMA);

        $this->baseUrl = env('API_URL').'/sistemas/';
        $this->withHeaders(['Accept'=>'application/vnd.api+json']);
    }

    /** @test */
    public function it_creates_an_sistema_unauthenticated()
    {
        $response = $this->post($this->baseUrl);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_creates_an_sistema_authenticated()
    {
        Passport::actingAs($this->user);

        $response = $this->post($this->baseUrl);
        $response->assertStatus(201);
    }

    /** @test */
    public function it_updates_an_sistema_unauthenticated()
    {
        $sistema = factory(Sistema::class)->create();

        $response = $this->patch($this->baseUrl.$sistema->id, $sistema->toArray());
        $response->assertStatus(401);
    }

    /** @test */
    public function it_updates_an_sistema_authenticated()
    {
        Passport::actingAs($this->user);

        $sistema = factory(Sistema::class)->create();

        $response = $this->patch($this->baseUrl.$sistema->id, $sistema->toArray());

        $response->assertStatus(200);
    }

    /** @test */
    public function it_retrieves_an_sistema_unauthenticated()
    {
        $sistema = factory(Sistema::class)->create();

        $response = $this->get($this->baseUrl.$sistema->id);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_retrieves_an_sistema_authenticated()
    {
        Passport::actingAs($this->user);

        $sistema = factory(Sistema::class)->create();

        $response = $this->get($this->baseUrl.$sistema->id);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_retrieves_all_sistemas_unauthenticated()
    {
        factory(Sistema::class, 3)->create();

        $response = $this->get($this->baseUrl);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_retrieves_all_sistemas_authenticated()
    {
        Passport::actingAs($this->user);

        factory(Sistema::class, 3)->create();

        $response = $this->get($this->baseUrl);
        $response->assertStatus(206);
    }

    /** @test */
    public function it_deletes_an_sistema_unauthenticated()
    {
        $sistema = factory(Sistema::class)->create();

        $response = $this->delete($this->baseUrl.$sistema->id);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_deletes_an_sistema_authenticated()
    {
        Passport::actingAs($this->user);

        $sistema = factory(Sistema::class)->create();

        $response = $this->delete($this->baseUrl.$sistema->id);
        $response->assertStatus(204);
    }
}
