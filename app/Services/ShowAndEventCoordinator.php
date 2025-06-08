<?php

namespace App\Services;

use App\Contracts\TicketGatewayInterface;

/**
 * Координирует бизнес-логику, связанную с мероприятиями и событиями.
 * Служит связующим звеном между контроллерами и шлюзом данных.
 */
class ShowAndEventCoordinator
{
    /**
     * Конструктор для внедрения зависимостей.
     *
     * @param \App\Contracts\TicketGatewayInterface $gateway
     */
    public function __construct(
        private readonly TicketGatewayInterface $gateway
    ) {
    }

    /**
     * Получает список всех доступных мероприятий.
     *
     * @return array
     */
    public function getAvailableShows(): array
    {
        return $this->gateway->getAvailableShows();
    }

    /**
     * Получает детальную информацию о конкретном мероприятии.
     *
     * @param string $showId
     * @return array
     */
    public function getShowDetails(string $showId): array
    {
        return $this->gateway->getShowDetails($showId);
    }

    /**
     * Получает схему зала для конкретного события.
     *
     * @param string $externalEventId
     * @return array
     */
    public function getEventSeatLayout(string $externalEventId): array
    {
        return $this->gateway->getEventSeatLayout($externalEventId);
    }

    /**
     * Выполняет операцию бронирования мест.
     *
     * @param string $externalEventId
     * @param array $selectedPlaceIds
     * @param string $customerName
     * @return array
     */
    public function submitReservation(string $externalEventId, array $selectedPlaceIds, string $customerName): array
    {
        return $this->gateway->submitReservation($externalEventId, $selectedPlaceIds, $customerName);
    }
}