<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EntityEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EntityEmailTest extends TestCase
{
    use  RefreshDatabase;

    protected string $endpoint = '/api/entityEmails';
    protected string $tableName = 'entityEmails';

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateEntityEmail(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        $payload = EntityEmail::factory()->make([])->toArray();

        $this->json('POST', $this->endpoint, $payload)
             ->assertStatus(201)
             ->assertSee($payload['name']);

        $this->assertDatabaseHas($this->tableName, ['id' => 1]);
    }

    public function testViewAllEntityEmailsSuccessfully(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        EntityEmail::factory(5)->create();

        $this->json('GET', $this->endpoint)
             ->assertStatus(200)
             ->assertJsonCount(5, 'data')
             ->assertSee(EntityEmail::find(rand(1, 5))->name);
    }

    public function testViewAllEntityEmailsByFooFilter(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        EntityEmail::factory(5)->create();

        $this->json('GET', $this->endpoint.'?foo=1')
             ->assertStatus(200)
             ->assertSee('foo')
             ->assertDontSee('foo');
    }

    public function testsCreateEntityEmailValidation(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        $data = [
        ];

        $this->json('post', $this->endpoint, $data)
             ->assertStatus(422);
    }

    public function testViewEntityEmailData(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        EntityEmail::factory()->create();

        $this->json('GET', $this->endpoint.'/1')
             ->assertSee(EntityEmail::first()->name)
             ->assertStatus(200);
    }

    public function testUpdateEntityEmail(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        EntityEmail::factory()->create();

        $payload = [
            'name' => 'Random'
        ];

        $this->json('PUT', $this->endpoint.'/1', $payload)
             ->assertStatus(200)
             ->assertSee($payload['name']);
    }

    public function testDeleteEntityEmail(): void
    {
        $this->markTestIncomplete('This test case needs review.');

        $this->actingAs(User::factory()->create());

        EntityEmail::factory()->create();

        $this->json('DELETE', $this->endpoint.'/1')
             ->assertStatus(204);

        $this->assertEquals(0, EntityEmail::count());
    }
    
}
