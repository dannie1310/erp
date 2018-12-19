<?php

namespace App\Tests\Authentication;
use Laravel\Passport\Passport;
use App\User;
use App\Usuario;
use App\Permissions\UsuarioPermissions;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UsuarioAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $baseUrl;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        $this->user->givePermissionTo(UsuarioPermissions::RETRIEVE_USUARIO);
        $this->user->givePermissionTo(UsuarioPermissions::RETRIEVE_ALL_USUARIOS);
        $this->user->givePermissionTo(UsuarioPermissions::CREATE_USUARIO);
        $this->user->givePermissionTo(UsuarioPermissions::UPDATE_USUARIO);
        $this->user->givePermissionTo(UsuarioPermissions::DELETE_USUARIO);

        $this->baseUrl = env('API_URL').'/usuarios/';
        $this->withHeaders(['Accept'=>'application/vnd.api+json']);
    }

    /** @test */
    public function it_creates_an_usuario_unauthenticated()
    {
        $response = $this->post($this->baseUrl);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_creates_an_usuario_authenticated()
    {
        Passport::actingAs($this->user);

        $response = $this->post($this->baseUrl);
        $response->assertStatus(201);
    }

    /** @test */
    public function it_updates_an_usuario_unauthenticated()
    {
        $usuario = factory(Usuario::class)->create();

        $response = $this->patch($this->baseUrl.$usuario->id, $usuario->toArray());
        $response->assertStatus(401);
    }

    /** @test */
    public function it_updates_an_usuario_authenticated()
    {
        Passport::actingAs($this->user);

        $usuario = factory(Usuario::class)->create();

        $response = $this->patch($this->baseUrl.$usuario->id, $usuario->toArray());

        $response->assertStatus(200);
    }

    /** @test */
    public function it_retrieves_an_usuario_unauthenticated()
    {
        $usuario = factory(Usuario::class)->create();

        $response = $this->get($this->baseUrl.$usuario->id);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_retrieves_an_usuario_authenticated()
    {
        Passport::actingAs($this->user);

        $usuario = factory(Usuario::class)->create();

        $response = $this->get($this->baseUrl.$usuario->id);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_retrieves_all_usuarios_unauthenticated()
    {
        factory(Usuario::class, 3)->create();

        $response = $this->get($this->baseUrl);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_retrieves_all_usuarios_authenticated()
    {
        Passport::actingAs($this->user);

        factory(Usuario::class, 3)->create();

        $response = $this->get($this->baseUrl);
        $response->assertStatus(206);
    }

    /** @test */
    public function it_deletes_an_usuario_unauthenticated()
    {
        $usuario = factory(Usuario::class)->create();

        $response = $this->delete($this->baseUrl.$usuario->id);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_deletes_an_usuario_authenticated()
    {
        Passport::actingAs($this->user);

        $usuario = factory(Usuario::class)->create();

        $response = $this->delete($this->baseUrl.$usuario->id);
        $response->assertStatus(204);
    }
}
