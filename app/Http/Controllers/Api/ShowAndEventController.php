<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShowAndEventCoordinator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Ticket Booking API",
 *      description="API для получения информации о мероприятиях и бронирования билетов."
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Основной API сервер"
 * )
 */
class ShowAndEventController extends Controller
{
    /**
     * Конструктор для внедрения зависимостей.
     *
     * @param \App\Services\ShowAndEventCoordinator $coordinator
     */
    public function __construct(
        private readonly ShowAndEventCoordinator $coordinator
    ) {
    }


    /**
     * @OA\Get(
     *      path="/shows",
     *      summary="Получить список всех мероприятий",
     *      tags={"Мероприятия (Shows)"},
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ со списком мероприятий.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Show #1")
     *              )
     *          )
     *      )
     * )
     */
    public function index(): JsonResponse
    {
        $shows = $this->coordinator->getAvailableShows();

        return response()->json($shows);
    }

    /**
     * @OA\Get(
     *      path="/shows/{showId}",
     *      summary="Получить детальную информацию о мероприятии",
     *      description="Возвращает информацию о конкретном мероприятии и список его событий.",
     *      tags={"Мероприятия (Shows)"},
     *      @OA\Parameter(
     *          name="showId",
     *          description="ID мероприятия",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=46),
     *                  @OA\Property(property="showId", type="integer", example=10),
     *                  @OA\Property(property="date", type="string", format="date-time", example="2019-08-22 20:26:38")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Мероприятие не найдено."
     *      )
     * )
     */
    public function show(string $showId): JsonResponse
    {
        $details = $this->coordinator->getShowDetails($showId);

        return response()->json($details);
    }


    /**
     * @OA\Get(
     *      path="/shows/{showId}/events/{eventId}/places",
     *      summary="Получить схему зала для события",
     *      description="Возвращает список всех мест в зале для конкретного события с указанием их доступности.",
     *      tags={"События (Events)"},
     *      @OA\Parameter(
     *          name="showId",
     *          description="ID мероприятия (для полноты URL)",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="eventId",
     *          description="ID события",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ со схемой зала.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="x", type="number", format="float", example=0),
     *                  @OA\Property(property="y", type="number", format="float", example=30),
     *                  @OA\Property(property="width", type="number", format="float", example=20),
     *                  @OA\Property(property="height", type="number", format="float", example=20),
     *                  @OA\Property(property="is_available", type="boolean", example=true)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Событие не найдено."
     *      )
     * )
     */
    public function seatLayout(string $showId, string $eventId): JsonResponse
    {
        $layout = $this->coordinator->getEventSeatLayout($eventId);

        return response()->json($layout);
    }

    /**
     * @OA\Post(
     *      path="/shows/{showId}/events/{eventId}/reserve",
     *      summary="Забронировать места на событие",
     *      description="Отправляет запрос на бронирование выбранных мест для конкретного события от имени покупателя.",
     *      tags={"События (Events)"},
     *      @OA\Parameter(
     *          name="showId",
     *          description="ID мероприятия (для полноты URL)",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="eventId",
     *          description="ID события",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Данные для бронирования",
     *          @OA\JsonContent(
     *              required={"buyer_name", "places"},
     *              @OA\Property(property="buyer_name", type="string", example="Иван Иванов"),
     *              @OA\Property(property="places", type="array", @OA\Items(type="integer", example=101))
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешное бронирование.",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="reservation_id", type="string", example="5d519fe58e3cf")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации (например, не передано имя или места)."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Событие не найдено."
     *      )
     * )
     */
    public function reserve(Request $request, string $showId, string $eventId): JsonResponse
    {
        $validatedData = $request->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'places' => ['required', 'array'],
            'places.*' => ['integer', 'distinct'],
        ]);

        $result = $this->coordinator->submitReservation(
            $eventId,
            $validatedData['places'],
            $validatedData['buyer_name']
        );

        return response()->json($result);
    }
}