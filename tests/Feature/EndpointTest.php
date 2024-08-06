<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndpointTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        // $this->post('/api/register', [
        //     "name" => "David Pinarto",
        //     "email" => "davidpinarto90@gmail.com",
        //     "password" => "davidpinarto"
        // ])->assertStatus(201);

        // $this->post('/api/register', [
        //     "name" => "David Pinarto",
        //     "email" => "test@gmail.com",
        //     "password" => "davidpinarto"
        // ])->assertStatus(201);

        $this->post('/api/login', [
            "email" => "davidpinarto90@admin.com",
            "password" => "davidpinarto"
        ])->assertStatus(201);

        // $this->post('/api/login', [
        //     "email" => "davidpinarto90@admin.com",
        //     "password" => "feweyrf983erjewh"
        // ])->assertStatus(201);
        // $response->assertStatus(200);
    }
}
