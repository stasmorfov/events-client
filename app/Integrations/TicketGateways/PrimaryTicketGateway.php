<?php

namespace App\Integrations\TicketGateways;

use App\Contracts\TicketGatewayInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Конкретная реализация шлюза для взаимодействия с текущим поставщиком билетов.
 * Этот класс знает все о специфике работы с API leadbook.ru.
 */
class PrimaryTicketGateway implements TicketGatewayInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAvailableShows(): array
    {
        $response = $this->makeRequest()->get('/shows');

        return $response->json('response', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getShowDetails(string $showId): array
    {
        $response = $this->makeRequest()->get("/shows/{$showId}/events");

        return $response->json('response', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventSeatLayout(string $externalEventId): array
    {
        $response = $this->makeRequest()->get("/events/{$externalEventId}/places");

        return $response->json('response', []);
    }

    /**
     * {@inheritdoc}
     */
    public function submitReservation(string $externalEventId, array $selectedPlaceIds, string $customerName): array
    {
        $payload = [
            'name' => $customerName,
            'places' => $selectedPlaceIds,
        ];

        $url = "/events/{$externalEventId}/reserve?eventId={$externalEventId}";

        $response = $this->makeRequest()->asForm()->post($url, $payload);

        return $response->json('response', []);
    }

    /**
     * Создает и настраивает экземпляр HTTP-клиента.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    private function makeRequest(): PendingRequest
    {
        return Http::withToken(config('ticket_provider.auth_token'))
                    ->baseUrl(config('ticket_provider.base_url'))
                    ->acceptJson()
                    ->throw();
    }
}