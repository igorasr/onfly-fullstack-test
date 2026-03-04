<?php

namespace App\Http\Controllers;

use App\Enums\TravelRequestStatus;
use App\Http\Requests\ListTravelRequestsRequest;
use App\Http\Requests\StoreTravelRequestRequest;
use App\Http\Requests\UpdateTravelRequestRequest;
use App\Http\Requests\UpdateTravelRequestStatusRequest;
use App\Http\Resources\TravelRequestResource;
use App\Models\TravelRequest;
use App\DTOs\TravelRequestFiltersData;
use App\Services\TravelRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TravelRequestController extends Controller
{
    public function __construct(private TravelRequestService $travelRequestService) {}

    public function index(ListTravelRequestsRequest $request): JsonResponse
    {
        $filters = TravelRequestFiltersData::fromArray($request->validated());
        $travelRequests = $this->travelRequestService->list($filters);

        return TravelRequestResource::collection($travelRequests)->response();
    }

    public function store(StoreTravelRequestRequest $request): JsonResponse
    {
        $travelRequest = $this->travelRequestService->create($request->user(), $request->validated());

        return $travelRequest->toTravelRequestResource()->response()->setStatusCode(201);
    }

    public function show(TravelRequest $travelRequest): JsonResponse
    {
        return $travelRequest->load('requester')->toTravelRequestResource()->response();
    }

    public function update(UpdateTravelRequestRequest $request, TravelRequest $travelRequest): JsonResponse
    {
        $travelRequest = $this->travelRequestService->update($travelRequest, $request->validated());

        return $travelRequest->toTravelRequestResource()
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(TravelRequest $travelRequest): JsonResponse
    {
        $this->travelRequestService->delete($travelRequest);

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }

    public function updateStatus(UpdateTravelRequestStatusRequest $request, TravelRequest $travelRequest): JsonResponse
    {
        $newStatus = TravelRequestStatus::from($request->validated('status'));
        $travelRequest = $this->travelRequestService->updateStatus($travelRequest, $newStatus);

        return $travelRequest->toTravelRequestResource()
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
