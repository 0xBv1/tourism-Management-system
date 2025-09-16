<?php

namespace App\Http\Controllers\Api;

use App\Events\NewBookingEvent;
use App\Exceptions\CouponNotAvailableForSelectedToursException;
use App\Exceptions\EmptyCartException;
use App\Exceptions\ExpiredCouponException;
use App\Exceptions\InActiveCouponException;
use App\Exceptions\InvalidPaymentGateWayException;
use App\Exceptions\InvalidStopLocationException;
use App\Exceptions\LoginFirstToUseCouponException;
use App\Exceptions\NoAvailableCarForRouteException;
use App\Exceptions\UsedCouponException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\BookingRequest;
use App\Http\Requests\Api\Booking\SaveDealRequest;
use App\Http\Resources\Api\BookingResource;
use App\Models\Booking;
use App\Payments\PaymentGateway;
use App\Services\Client\Booking as BookingService;
use App\Services\Query\QueryBuilder;
use App\Traits\Response\HasApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BookingController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        $queryBuilder = new QueryBuilder(new Booking, $request);
        $bookings = $queryBuilder->build()->where('client_id', auth()->id())->paginate();
        $collection = BookingResource::collection($bookings->getCollection());
        $bookings->setCollection(collect($collection));
        return $this->send($bookings);
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show(Request $request, mixed $id)
    {
        $queryBuilder = new QueryBuilder(new Booking, $request);
        $booking = $queryBuilder->build()->find($id);
        return $this->send(new BookingResource($booking));
    }

    public function create(BookingRequest $request, BookingService $bookingService)
    {
        DB::beginTransaction();
        try {
            $bookingService->create($request->getSanitized());

            $booking = $bookingService->getBooking();

            NewBookingEvent::dispatchIf($booking->isCod(), $booking);

            DB::commit();

            $booking->load('tours', 'rentals', 'client', 'coupon');

//            $gateway->pay($booking);

            return $this->send(
                data: [
                    'booking' => new BookingResource($booking),
//                    'payment' => [
//                        'redirect' => $gateway->redirect(),
//                        'message' => $gateway->message()
//                    ]
                ],
                message: __('messages.bookings.created'),
                statusCode: Response::HTTP_CREATED
            );
        } catch (InActiveCouponException|
        ExpiredCouponException|
        UsedCouponException|
        EmptyCartException|
        InvalidPaymentGateWayException|
        InvalidStopLocationException|
        LoginFirstToUseCouponException|
        NoAvailableCarForRouteException|
        CouponNotAvailableForSelectedToursException $exception) {
            DB::rollBack();
            return $this->send(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        } catch (Throwable|Exception $exception) {
            DB::rollBack();
            report($exception);
            return $this->send(
                data: [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace()
                ],
                message: __('messages.bookings.error'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function saveDeal(SaveDealRequest $request): JsonResponse
    {
        $data = $request->getSanitized();

        // find or create client by email (and update phone/name if provided)
        $client = \App\Models\Client::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                'phone' => $data['phone'],
                'password' => bcrypt(str()->random(16)),
            ]
        );

        // If client existed, optionally update details
        if (!$client->wasRecentlyCreated) {
            $client->fill([
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                'phone' => $data['phone'],
            ])->save();
        }

        // create a lightweight booking record as a saved deal/inquiry
        $booking = Booking::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'notes' => $data['questions'] ?? null,
            'client_id' => $client->id,
            'sub_total_price' => 0,
            'total_price' => 0,
            'currency_exchange_rate' => 1,
            'meta' => [
                'deal_date' => $data['date'],
                'adults' => $data['adults'],
                'children' => $data['children'] ?? 0,
            ],
        ]);

        return $this->send([
            'booking' => new BookingResource($booking),
        ], __('messages.bookings.created'));
    }
}
