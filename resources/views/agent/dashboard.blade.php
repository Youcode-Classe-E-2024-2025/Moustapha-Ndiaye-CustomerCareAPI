<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Custom Tailwind config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'new': '#3498db',
                        'open': '#2ecc71',
                        'in-progress': '#f39c12',
                        'resolved': '#27ae60',
                        'closed': '#7f8c8d',
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js component script -->
    <script>
        function ticketDashboard() {
            return {
                tickets: [],
                meta: {},
                links: {},
                loading: true,
                filters: {
                    status_id: '',
                    priority: '',
                    category: '',
                    sort_field: 'created_at',
                    sort_direction: 'desc',
                    per_page: 10
                },
                selectedTicket: null,
                newStatus: '',
                newComment: '',
                uniqueCategories: [],
                
                init() {
                    this.loadTickets();
                },
                
                loadTickets() {
                    this.loading = true;
                    
                    // Build query string from filters
                    const queryParams = new URLSearchParams();
                    Object.entries(this.filters).forEach(([key, value]) => {
                        if (value) queryParams.append(key, value);
                    });
                    
                    fetch(`http://127.0.0.1:8000/api/tickets?${queryParams.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            // Add your authentication token here if needed
                            // 'Authorization': 'Bearer YOUR_TOKEN'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.tickets = data.data;
                        this.meta = data.meta;
                        this.links = data.links;
                        this.loading = false;
                        
                        // Extract unique categories for filter dropdown
                        this.uniqueCategories = [...new Set(data.data.map(ticket => ticket.category))];
                    })
                    .catch(error => {
                        console.error('Error loading tickets:', error);
                        this.loading = false;
                    });
                },
                
                navigateToPage(url) {
                    if (!url) return;
                    
                    this.loading = true;
                    
                    fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            // Add your authentication token here if needed
                            // 'Authorization': 'Bearer YOUR_TOKEN'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.tickets = data.data;
                        this.meta = data.meta;
                        this.links = data.links;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error navigating to page:', error);
                        this.loading = false;
                    });
                },
                
                toggleSortDirection() {
                    this.filters.sort_direction = this.filters.sort_direction === 'asc' ? 'desc' : 'asc';
                    this.loadTickets();
                },
                
                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return new Intl.DateTimeFormat('fr-FR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }).format(date);
                },
                
                formatPriority(priority) {
                    // Handle both English and French priority values
                    const priorityMap = {
                        'low': 'Basse',
                        'medium': 'Moyenne',
                        'high': 'Haute',
                        'urgent': 'Urgente',
                        'basse': 'Basse',
                        'moyenne': 'Moyenne',
                        'haute': 'Haute'
                    };
                    
                    return priorityMap[priority.toLowerCase()] || priority;
                },
                
                selectTicket(ticket) {
                    this.selectedTicket = ticket;
                    this.newStatus = ticket.status.id.toString();
                    this.newComment = '';
                },
                
                openTicketModal(ticket) {
                    this.selectTicket(ticket);
                },
                
                closeTicketModal() {
                    this.selectedTicket = null;
                },
                
                updateTicketStatus() {
                    if (!this.selectedTicket) return;
                    
                    fetch(`http://127.0.0.1:8000/api/tickets/${this.selectedTicket.id}/change-status`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            // Add your authentication token here if needed
                            // 'Authorization': 'Bearer YOUR_TOKEN'
                        },
                        body: JSON.stringify({
                            status_id: parseInt(this.newStatus),
                            comment: this.newComment
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update the ticket in the list
                        const index = this.tickets.findIndex(t => t.id === this.selectedTicket.id);
                        if (index !== -1) {
                            this.tickets[index] = data.data;
                        }
                        
                        // Update the selected ticket
                        this.selectedTicket = data.data;
                        this.newComment = '';
                        
                        // Show success message
                        alert('Ticket status updated successfully');
                    })
                    .catch(error => {
                        console.error('Error updating ticket status:', error);
                        alert('Failed to update ticket status');
                    });
                },
                
                addComment() {
                    if (!this.selectedTicket || !this.newComment.trim()) return;
                    
                    // This assumes you have an endpoint for adding comments
                    // If not, you can modify this to use the change-status endpoint with the current status
                    fetch(`http://127.0.0.1:8000/api/tickets/${this.selectedTicket.id}/change-status`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            // Add your authentication token here if needed
                            // 'Authorization': 'Bearer YOUR_TOKEN'
                        },
                        body: JSON.stringify({
                            status_id: parseInt(this.newStatus),
                            comment: this.newComment
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update the ticket in the list
                        const index = this.tickets.findIndex(t => t.id === this.selectedTicket.id);
                        if (index !== -1) {
                            this.tickets[index] = data.data;
                        }
                        
                        // Update the selected ticket
                        this.selectedTicket = data.data;
                        this.newComment = '';
                        
                        // Show success message
                        alert('Comment added successfully');
                    })
                    .catch(error => {
                        console.error('Error adding comment:', error);
                        alert('Failed to add comment');
                    });
                }
            };
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div x-data="ticketDashboard()" class="container mx-auto px-4 py-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Agent Ticket Dashboard</h1>
            <p class="text-gray-600">Manage and respond to customer support tickets</p>
        </header>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Filters</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="filters.status_id" @change="loadTickets()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Statuses</option>
                        <option value="1">New</option>
                        <option value="2">Open</option>
                        <option value="3">In Progress</option>
                    </select>
                </div>
                
                <!-- Priority Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select x-model="filters.priority" @change="loadTickets()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select x-model="filters.category" @change="loadTickets()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Categories</option>
                        <template x-for="category in uniqueCategories" :key="category">
                            <option :value="category" x-text="category"></option>
                        </template>
                    </select>
                </div>
                
                <!-- Sort Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <div class="flex space-x-2">
                        <select x-model="filters.sort_field" @change="loadTickets()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="created_at">Date Created</option>
                            <option value="priority">Priority</option>
                            <option value="status_id">Status</option>
                            <option value="title">Title</option>
                        </select>
                        <button @click="toggleSortDirection()" class="px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                            <template x-if="filters.sort_direction === 'asc'">
                                <span>↑</span>
                            </template>
                            <template x-if="filters.sort_direction === 'desc'">
                                <span>↓</span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center my-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
        </div>

        <!-- Tickets Table -->
        <div x-show="!loading" class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="ticket in tickets" :key="ticket.id">
                        <tr @click="selectTicket(ticket)" class="hover:bg-gray-50 cursor-pointer">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="ticket.id"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="ticket.title"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :style="`background-color: ${ticket.status.color}20; color: ${ticket.status.color}`"
                                      x-text="ticket.status.name"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatPriority(ticket.priority)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="ticket.category"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="ticket.assigned_to || 'Unassigned'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(ticket.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click.stop="openTicketModal(ticket)" class="text-indigo-600 hover:text-indigo-900">View</button>
                            </td>
                        </tr>
                    </template>
                    <!-- Empty State -->
                    <tr x-show="tickets.length === 0">
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No tickets found matching your filters.
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium" x-text="meta.from || 0"></span>
                            to
                            <span class="font-medium" x-text="meta.to || 0"></span>
                            of
                            <span class="font-medium" x-text="meta.total || 0"></span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <template x-for="link in meta.links" :key="link.label">
                                <button 
                                    @click="navigateToPage(link.url)"
                                    :disabled="!link.url || link.active"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        !link.url ? 'cursor-not-allowed' : 'cursor-pointer'
                                    ]"
                                    x-html="link.label">
                                </button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Detail Modal -->
        <div x-show="selectedTicket" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-10 overflow-y-auto" 
             style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="selectedTicket"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full lg:max-w-2xl"
                     style="display: none;">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2" x-text="selectedTicket?.title"></h3>
                                
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-2 py-1 text-xs rounded-full"
                                          :style="`background-color: ${selectedTicket?.status.color}20; color: ${selectedTicket?.status.color}`"
                                          x-text="selectedTicket?.status.name"></span>
                                          
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full"
                                          x-text="formatPriority(selectedTicket?.priority)"></span>
                                          
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full"
                                          x-text="selectedTicket?.category"></span>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <p class="text-sm text-gray-700" x-text="selectedTicket?.description"></p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium text-gray-700 mb-2">Update Status</h4>
                                    <div class="flex space-x-2">
                                        <select x-model="newStatus" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="1">New</option>
                                            <option value="2">Open</option>
                                            <option value="3">In Progress</option>
                                            <option value="4">Resolved</option>
                                            <option value="5">Closed</option>
                                        </select>
                                        <button @click="updateTicketStatus()" class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                            Update
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="font-medium text-gray-700 mb-2">Add Comment</h4>
                                    <textarea x-model="newComment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Enter your comment..."></textarea>
                                    <div class="mt-2 flex justify-end">
                                        <button @click="addComment()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="closeTicketModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>