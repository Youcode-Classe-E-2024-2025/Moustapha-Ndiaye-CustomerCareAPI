<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketHistoryResource;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket; 

class TicketHistoryController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/{id}/history",
     *     summary="Get ticket history",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ticket ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket history",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="ticket_id", type="integer"),
     *                     @OA\Property(property="action", type="string", example="status_changed"),
     *                     @OA\Property(property="description", type="string", example="Status changed from 'Open' to 'In Progress'"),
     *                     @OA\Property(property="performed_by", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function history(int $id): ResourceCollection
    {
        $history = $this->ticketService->getTicketHistory($id);
        
        
        return TicketHistoryResource::collection($history);
    }

    // public function history(int $id): JsonResponse
    // {
        
    //     $history = $this->ticketService->getTicketHistory($id);
    
    //     if ($history->isEmpty()) {
    //         return response()->json([
    //             'message' => 'No history available for this ticket.'
    //         ], 404);  
    //     }
    
    //     return TicketHistoryResource::collection($history);
    // }
    
}
