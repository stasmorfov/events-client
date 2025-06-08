<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShowApiFlowTest extends TestCase
{
    /**
     * Тест для эндпоинта получения списка мероприятий.
     * Проверяет успешный сценарий: внешний API доступен и возвращает данные.
     *
     * @return void
     */
    public function test_get_shows_endpoint_returns_a_successful_response_with_data(): void
    {
        $fakeApiResponse = [
            'response' => [
                ['id' => 1, 'name' => 'Концерт "Воображаемый"'],
                ['id' => 2, 'name' => 'Театральная постановка "Мечта"'],
            ]
        ];

        $expectedUrl = config('ticket_provider.base_url').'/shows';

        Http::fake([
            $expectedUrl => Http::response($fakeApiResponse, 200),
        ]);

        $response = $this->getJson('/api/shows');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'id' => 1,
            'name' => 'Концерт "Воображаемый"',
        ]);

        Http::assertSent(function ($request) use ($expectedUrl) {
            return $request->hasHeader('Authorization', 'Bearer '.config('ticket_provider.auth_token')) &&
                   $request->url() === $expectedUrl;
        });
    }
}