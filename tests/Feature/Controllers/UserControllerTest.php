<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Contracts\CountryRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;

class UserControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->country = resolve(CountryRepositoryInterface::class);
        $this->user = resolve(UserRepositoryInterface::class);
        $this->account = resolve(AccountRepositoryInterface::class);

        $this->hasher = resolve(Hasher::class);
    }

    public function createUser()
    {
        $user = $this->user->model()->create($this->getAllInputsInValidState());

        $this->account->model()->create(
            ['user_id' => $user->id] + $this->getAllAccountInputsInValidState()
        );

        return $user;
    }

    public function getJsonStructure()
    {
        return [
            'id',
            'email',
            'account',
        ];
    }

    public function getAllAccountInputsInValidState()
    {
        return [
            'email' => 'foobar@example.com',
            'first_name' => 'Foobius',
            'last_name' => 'Barius',
            'address_one' => '123 Any Street',
            'address_two' => 'Apt 1',
            'city' => 'New York',
            'territory' => 'New York',
            'country_code' => 'US',
            'postal_code' => '10110',
            'phone' => '+1 510 200 3000',
            'alt_phone' => null,
        ];
    }

    public function getAllInputsInValidState()
    {
        return [
            'email' => 'foo@bar.com',
            'password' => 'secretsauce',
            'account' => $this->getAllAccountInputsInValidState()
        ];
    }

    public function test_all_returnsOkStatusAndExpectedJsonStructure()
    {
        $this->createUser();

        $this->json('GET', route('users.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    public function test_show_returnsOkStatusAndExpectedJsonStructure()
    {
        $user = $this->createUser();

        $this->json('GET', route('users.show', ['id' => $user->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_store_withValidInput_returnsOkStatusAndExpectedJsonStructure()
    {
        $r = $this->json('POST', route('users.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_store_withInvalidInput_returnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $this->json('POST', route('users.store'), [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function test_update_withValidInput_returnsOkStatusAndExpectedJsonStructure()
    {
        $user = $this->createUser();

        $inputs = $this->getAllInputsInValidState();

        $inputs['username'] = 'FoobiusBazius';

        $this->json('PUT', route('users.update', ['id' => $user->id]), $inputs)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_update_withInvalidInput_returnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $user = $this->createUser();

        $this->json('PUT', route('users.update', ['id' => $user->id]), ['email' => ''])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function test_destroy_withValidInput_returnsOkStatusAndExpectedJsonStructure()
    {
        $user = $this->createUser();

        $this->json('DELETE', route('users.destroy', ['id' => $user->id]))
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    public function test_destroy_withInvalidInput_returnsUnprocessableEntityStatus()
    {
        $this->json('DELETE', route('users.destroy', ['id' => 'foo']))
            ->assertStatus(404);
    }

    public function test_destroy_withMissingInput_returnsMethodNotAllowedStatus()
    {
        $this->json('DELETE', route('users.destroy', ['id' => null]))
            ->assertStatus(405);
    }

    public function test_storePassword_withValidInput_authenticationWorksForPassword()
    {
        $inputs = $this->getAllInputsInValidState();

        $this->json('POST', route('users.store'), $inputs)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);

        $user = $this->user->model()->where('email', $inputs['email'])->first();

        $this->assertTrue($this->hasher->check($inputs['password'], $user->password));
    }

    public function test_updatePassword_withValidInput_authenticationWorksForPassword()
    {
        $user = $this->createUser();

        $inputs = ['password' => 'secretsauce'];

        $this->json('PUT', route('users.update', ['id' => $user->id]), $inputs)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);

        $user = $this->user->findById($user->id);

        $this->assertTrue($this->hasher->check($inputs['password'], $user->password));
    }
}