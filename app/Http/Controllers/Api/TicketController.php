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

/**
 * @OA\Info(
 *     title="Customer Care API",
 *     version="1.0.0",
 *     description="API for Customer Care Service",
 *     termsOfService="http://example.com/terms",
 *     contact={
 *         "email": "support@example.com"
 *     },
 *     license={
 *         "name": "MIT",
 *         "url": "http://opensource.org/licenses/MIT"
 *     }
 * )
 */

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * @OA\Get(
     *     path="/api/tickets",
     *     summary="Get all tickets",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="Filter by status ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low", "medium", "high", "urgent"})
     *     ),
     *     @OA\Parameter(
     *         name="assigned_to",
     *         in="query",
     *         description="Filter by assigned agent ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "updated_at", "priority"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tickets",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="priority", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    // public function index(Request $request): ResourceCollection
    // {
    //     $filters = $request->only([
    //         'status_id', 'priority', 'assigned_to', 'category',
    //         'sort_field', 'sort_direction', 'per_page'
    //     ]);
        
    //     $tickets = $this->ticketService->getAllTickets($filters);
        
    //     return TicketResource::collection($tickets);
    // }
    public function index(Request $request): ResourceCollection
{
    $validatedFilters = $request->validate([
        'status_id' => 'nullable|exists:statuses,id',
        'priority' => 'nullable|in:low,medium,high,urgent',
        'assigned_to' => 'nullable|exists:users,id',
        'category' => 'nullable|string|max:255',
        'sort_field' => 'nullable|in:title,created_at,priority,status_id',
        'sort_direction' => 'nullable|in:asc,desc',
        'per_page' => 'nullable|integer|min:1|max:100',
    ]);

    $tickets = $this->ticketService->getAllTickets($validatedFilters);
    return TicketResource::collection($tickets);
}


    /**
     * @OA\Post(
     *     path="/api/tickets",
     *     summary="Create a new ticket",
     *     tags={"Tickets"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "priority"},
     *             @OA\Property(property="title", type="string", example="System Error #45"),
     *             @OA\Property(property="description", type="string", example="The system is showing an error when trying to process payments"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"}, example="high"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="attachments", type="array", @OA\Items(type="string", format="binary"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="System Error #45"),
     *                 @OA\Property(property="description", type="string", example="The system is showing an error when trying to process payments"),
     *                 @OA\Property(property="status", type="string", example="Open"),
     *                 @OA\Property(property="priority", type="string", example="high"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket($request->validated());
        
        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/tickets/{id}",
     *     summary="Get a ticket by ID",
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
     *         description="Ticket details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="System Error #45"),
     *                 @OA\Property(property="description", type="string", example="The system is showing an error when trying to process payments"),
     *                 @OA\Property(property="status", type="string", example="Open"),
     *                 @OA\Property(property="priority", type="string", example="high"),
     *                 @OA\Property(property="category", type="string", example="Technical"),
     *                 @OA\Property(property="assigned_to", type="object", 
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="name", type="string", example="John Doe")
     *                 ),
     *                 @OA\Property(property="created_by", type="object", 
     *                     @OA\Property(property="id", type="integer", example=12),
     *                     @OA\Property(property="name", type="string", example="Jane Smith")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="attachments", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="filename", type="string"),
     *                     @OA\Property(property="url", type="string")
     *                 ))
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
    public function show(int $id): TicketResource
    {
        $ticket = $this->ticketService->getTicket($id);
        
        return new TicketResource($ticket);
    }

    /**
     * @OA\Put(
     *     path="/api/tickets/{id}",
     *     summary="Update a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ticket ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated System Error #45"),
     *             @OA\Property(property="description", type="string", example="Updated description with more details"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"}, example="urgent"),
     *             @OA\Property(property="status_id", type="integer", example=2),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="assigned_to", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated System Error #45"),
     *                 @OA\Property(property="description", type="string", example="Updated description with more details"),
     *                 @OA\Property(property="status", type="string", example="In Progress"),
     *                 @OA\Property(property="priority", type="string", example="urgent"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function update(UpdateTicketRequest $request, int $id): TicketResource
    {
        $ticket = $this->ticketService->updateTicket($id, $request->validated());
        
        return new TicketResource($ticket);
    }

    /**
     * @OA\Delete(
     *     path="/api/tickets/{id}",
     *     summary="Delete a ticket",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ticket ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Ticket deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->ticketService->deleteTicket($id);
        
        return response()->json([
            'message' => 'Ticket deleted successfully.'
        ], 200);
    }

    

    /**
     * @OA\Post(
     *     path="/api/tickets/{id}/assign",
     *     summary="Assign a ticket to an agent",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ticket ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"agent_id"},
     *             @OA\Property(
     *                 property="agent_id",
     *                 type="integer",
     *                 description="ID of the agent to assign",
     *                 example=5
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket assigned successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="System Error #45"),
     *                 @OA\Property(property="status", type="string", example="Assigned"),
     *                 @OA\Property(property="assigned_to", type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="name", type="string", example="John Doe")
     *                 ),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket or agent not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function assign(Request $request, int $id): TicketResource
    {
        $request->validate([
            'agent_id' => 'required|integer|exists:users,id'
        ]);
        
        $ticket = $this->ticketService->assignTicket($id, $request->agent_id);
        
        return new TicketResource($ticket);
    }

    /**
     * @OA\Post(
     *     path="/api/tickets/{id}/change-status",
     *     summary="Change ticket status",
     *     tags={"Tickets"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ticket ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status_id"},
     *             @OA\Property(
     *                 property="status_id",
     *                 type="integer",
     *                 description="ID of the new status",
     *                 example=3
     *             ),
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 description="Optional comment about the status change",
     *                 example="Issue has been fixed in the latest deployment"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket status changed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="System Error #45"),
     *                 @OA\Property(property="status", type="string", example="Resolved"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket or status not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function changeStatus(Request $request, int $id): TicketResource
    {
        $request->validate([
            'status_id' => 'required|integer|exists:ticket_statuses,id',
            'comment' => 'nullable|string|max:500'
        ]);
        
        $ticket = $this->ticketService->changeTicketStatus(
            $id, 
            $request->status_id,
            $request->comment
        );
        
        return new TicketResource($ticket);
    }
}