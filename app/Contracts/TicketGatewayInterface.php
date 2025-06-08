<?php

namespace App\Contracts;

/**
 * Интерфейс для взаимодействия со шлюзом поставщика билетов.
 *
 * Определяет набор методов, которые должна реализовать любая интеграция
 * с внешним API для получения информации о мероприятиях и бронирования.
 * Это позволяет легко заменять одного поставщика на другого, не изменяя
 * основную бизнес-логику приложения.
 */
interface TicketGatewayInterface
{
    /**
     * Получает список всех доступных мероприятий.
     *
     * @return array Список мероприятий.
     */
    public function getAvailableShows(): array;

    /**
     * Получает детальную информацию о конкретном мероприятии,
     * включая список его событий.
     *
     * @param string $showId ID мероприятия.
     * @return array Детальная информация о мероприятии.
     */
    public function getShowDetails(string $showId): array;

    /**
     * Получает схему зала и информацию о доступности мест для конкретного события.
     *
     * @param string $externalEventId ID события во внешнем API.
     * @return array Информация о местах в зале.
     */
    public function getEventSeatLayout(string $externalEventId): array;

    /**
     * Отправляет запрос на бронирование указанных мест для события.
     *
     * @param string $externalEventId ID события во внешнем API.
     * @param array  $selectedPlaceIds Массив ID мест для бронирования.
     * @param string $customerName Имя покупателя.
     * @return array Результат операции бронирования.
     */
    public function submitReservation(string $externalEventId, array $selectedPlaceIds, string $customerName): array;
}