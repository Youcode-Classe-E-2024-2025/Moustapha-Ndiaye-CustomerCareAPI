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
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen p-4" x-data="{
        tickets: [],
        filteredTickets: [],
        loading: true,
        error: null,
        successMessage: null,
        searchQuery: '',
        statusFilter: 'all',
        priorityFilter: 'all',
        categoryFilter: 'all',
        currentTab: 'all',
        selectedTicket: null,
        editMode: false,
        ticketForm: {
            title: '',
            description: '',
            priority: '',
            status_id: '',
            assigned_to: ''
        },
        
        init() {
            this.fetchTickets();
        },
        
        async fetchTickets() {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/tickets');
                if (!response.ok) {
                    throw new Error('Failed to fetch tickets');
                }
                const data = await response.json();
                this.tickets = data.data;
                this.applyFilters();
                this.loading = false;
            } catch (err) {
                this.error = err.message;
                this.loading = false;
            }
        },
        
        applyFilters() {
            let result = [...this.tickets];
            
            // Apply tab filter
            if (this.currentTab === 'assigned') {
                result = result.filter(ticket => ticket.assigned_to !== null);
            } else if (this.currentTab === 'unassigned') {
                result = result.filter(ticket => ticket.assigned_to === null);
            }
            
            // Apply search filter
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(ticket => 
                    ticket.title.toLowerCase().includes(query) || 
                    ticket.description.toLowerCase().includes(query)
                );
            }
            
            // Apply status filter
            if (this.statusFilter !== 'all') {
                result = result.filter(ticket => ticket.status.name === this.statusFilter);
            }
            
            // Apply priority filter
            if (this.priorityFilter !== 'all') {
                result = result.filter(ticket => ticket.priority === this.priorityFilter);
            }
            
            // Apply category filter
            if (this.categoryFilter !== 'all') {
                result = result.filter(ticket => ticket.category === this.categoryFilter);
            }
            
            this.filteredTickets = result;
        },
        
        getUniqueValues(key, nestedKey = null) {
            if (!this.tickets.length) return [];
            
            if (nestedKey) {
                return [...new Set(this.tickets.map(ticket => ticket[key][nestedKey]))];
            }
            
            return [...new Set(this.tickets.map(ticket => ticket[key]))];
        },
        
        getPriorityColor(priority) {
            const normalizedPriority = priority.toLowerCase();
            if (normalizedPriority.includes('high') || normalizedPriority.includes('haute') || normalizedPriority === 'urgent') {
                return 'bg-red-100 text-red-800';
            } else if (normalizedPriority.includes('medium') || normalizedPriority.includes('moyenne')) {
                return 'bg-yellow-100 text-yellow-800';
            } else {
                return 'bg-green-100 text-green-800';
            }
        },
        
        async assignTicket(ticketId, agentId) {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/tickets/${ticketId}/assign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ agent_id: agentId })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to assign ticket');
                }
                
                // Refresh tickets after assignment
                await this.fetchTickets();
            } catch (err) {
                console.error('Error assigning ticket:', err);
            }
        },
        
        async changeStatus(ticketId, statusId, comment = '') {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/tickets/${ticketId}/change-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ 
                        status_id: statusId,
                        comment: comment
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to change ticket status');
                }
                
                // Refresh tickets after status change
                await this.fetchTickets();
            } catch (err) {
                console.error('Error changing ticket status:', err);
            }
        },
        
        selectTicket(ticket) {
            this.selectedTicket = ticket;
            this.editMode = false;
        },
        
        enableEditMode() {
            this.ticketForm = {
                title: this.selectedTicket.title,
                description: this.selectedTicket.description,
                priority: this.selectedTicket.priority,
                status_id: this.selectedTicket.status.id,
                assigned_to: this.selectedTicket.assigned_to ? 2 : '' // Assuming Agent User has ID 2
            };
            this.editMode = true;
        },
        
        cancelEdit() {
            this.editMode = false;
        },
        
        async updateTicket() {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/tickets/${this.selectedTicket.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.ticketForm)
                });
                
                if (!response.ok) {
                    throw new Error('Failed to update ticket');
                }
                
                const data = await response.json();
                
                // Update the ticket in the list
                const index = this.tickets.findIndex(t => t.id === this.selectedTicket.id);
                if (index !== -1) {
                    this.tickets[index] = data.data;
                }
                
                // Update the selected ticket
                this.selectedTicket = data.data;
                this.editMode = false;
                
                // Apply filters to update the filtered list
                this.applyFilters();
                
                // Show success message
                this.successMessage = 'Ticket updated successfully';
                setTimeout(() => {
                    this.successMessage = null;
                }, 3000);
            } catch (err) {
                console.error('Error updating ticket:', err);
                this.error = err.message;
                setTimeout(() => {
                    this.error = null;
                }, 3000);
            }
        },
        
        closeTicketModal() {
            this.selectedTicket = null;
            this.editMode = false;
        }
    }" x-effect="applyFilters">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold mb-6">Ticket Management Dashboard</h1>
            
            <!-- Success Message -->
            <div x-show="successMessage" x-cloak class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <span x-text="successMessage"></span>
            </div>
            
            <!-- Error Message -->
            <div x-show="error" x-cloak class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <span x-text="error"></span>
            </div>
            
            <!-- Loading state -->
            <div x-show="loading" class="flex items-center justify-center h-64">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>
            
            <div x-show="!loading && !error">
                <!-- Search and filters -->
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="relative flex-grow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                        <input 
                            type="text" 
                            placeholder="Search tickets..." 
                            class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            x-model="searchQuery"
                            x-on:input="applyFilters()"
                        />
                    </div>
                    
                    <div class="flex gap-2 flex-wrap">
                        <select 
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            x-model="statusFilter"
                            x-on:change="applyFilters()"
                        >
                            <option value="all">All Statuses</option>
                            <template x-for="status in getUniqueValues('status', 'name')" :key="status">
                                <option x-text="status" :value="status"></option>
                            </template>
                        </select>
                        
                        <select 
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            x-model="priorityFilter"
                            x-on:change="applyFilters()"
                        >
                            <option value="all">All Priorities</option>
                            <template x-for="priority in getUniqueValues('priority')" :key="priority">
                                <option x-text="priority" :value="priority"></option>
                            </template>
                        </select>
                        
                        <select 
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            x-model="categoryFilter"
                            x-on:change="applyFilters()"
                        >
                            <option value="all">All Categories</option>
                            <template x-for="category in getUniqueValues('category')" :key="category">
                                <option x-text="category" :value="category"></option>
                            </template>
                        </select>
                    </div>
                </div>
                
                <!-- Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button 
                                class="py-2 px-4 border-b-2 font-medium text-sm"
                                :class="currentTab === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                x-on:click="currentTab = 'all'; applyFilters()"
                            >
                                All Tickets
                            </button>
                            <button 
                                class="py-2 px-4 border-b-2 font-medium text-sm"
                                :class="currentTab === 'assigned' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                x-on:click="currentTab = 'assigned'; applyFilters()"
                            >
                                Assigned to Me
                            </button>
                            <button 
                                class="py-2 px-4 border-b-2 font-medium text-sm"
                                :class="currentTab === 'unassigned' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                x-on:click="currentTab = 'unassigned'; applyFilters()"
                            >
                                Unassigned
                            </button>
                        </nav>
                    </div>
                </div>
                
                <!-- Tickets grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="ticket in filteredTickets" :key="ticket.id">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-900" x-text="ticket.title"></h3>
                                        <p class="text-sm text-gray-500">
                                            #<span x-text="ticket.id"></span> • 
                                            <span x-text="new Date(ticket.created_at).toLocaleDateString()"></span>
                                        </p>
                                    </div>
                                    <span 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="getPriorityColor(ticket.priority)"
                                        x-text="ticket.priority"
                                    ></span>
                                </div>
                                
                                <div class="mt-2">
                                    <p class="text-sm text-gray-700 line-clamp-2" x-text="ticket.description"></p>
                                </div>
                                
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" x-text="ticket.category"></span>
                                    <span 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :style="`background-color: ${ticket.status.color}20; color: ${ticket.status.color};`"
                                        x-text="ticket.status.name"
                                    ></span>
                                </div>
                            </div>
                            
                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    <template x-if="ticket.assigned_to">
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            <span x-text="ticket.assigned_to"></span>
                                        </span>
                                    </template>
                                    <template x-if="!ticket.assigned_to">
                                        <span class="text-yellow-600">Unassigned</span>
                                    </template>
                                </div>
                                <button 
                                    type="button"
                                    @click="selectTicket(ticket)"
                                    class="inline-flex justify-center py-1 px-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    View Details
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Empty state -->
                    <div 
                        x-show="filteredTickets.length === 0" 
                        class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 text-gray-500"
                    >
                        No tickets found matching your filters
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ticket Detail Modal -->
        <div x-show="selectedTicket" 
             x-cloak
             class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full lg:max-w-2xl">
                    <!-- View Mode -->
                    <div x-show="!editMode">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <div class="flex justify-between items-center mb-2">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="selectedTicket?.title"></h3>
                                        <button 
                                            @click="enableEditMode()"
                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Edit
                                        </button>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span class="px-2 py-1 text-xs rounded-full"
                                              :style="`background-color: ${selectedTicket?.status.color}20; color: ${selectedTicket?.status.color}`"
                                              x-text="selectedTicket?.status.name"></span>
                                              
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full"
                                              x-text="selectedTicket?.priority"></span>
                                              
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full"
                                              x-text="selectedTicket?.category"></span>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                        <p class="text-sm text-gray-700" x-text="selectedTicket?.description"></p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h4 class="font-medium text-gray-700 mb-2">Ticket Details</h4>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div>
                                                <span class="text-gray-500">Created:</span>
                                                <span x-text="new Date(selectedTicket?.created_at).toLocaleString()"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Updated:</span>
                                                <span x-text="new Date(selectedTicket?.updated_at).toLocaleString()"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Assigned to:</span>
                                                <span x-text="selectedTicket?.assigned_to || 'Unassigned'"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">ID:</span>
                                                <span x-text="selectedTicket?.id"></span>
                                            </div>
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
                    
                    <!-- Edit Mode -->
                    <div x-show="editMode">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Ticket #<span x-text="selectedTicket?.id"></span></h3>
                                    
                                    <form @submit.prevent="updateTicket" class="space-y-4">
                                        <!-- Title -->
                                        <div>
                                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                            <input type="text" id="title" x-model="ticketForm.title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                        
                                        <!-- Description -->
                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                            <textarea id="description" x-model="ticketForm.description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <!-- Priority -->
                                            <div>
                                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                                <select id="priority" x-model="ticketForm.priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="low">Low</option>
                                                    <option value="medium">Medium</option>
                                                    <option value="high">High</option>
                                                    <option value="urgent">Urgent</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Status -->
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                                <select id="status" x-model="ticketForm.status_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="1">New</option>
                                                    <option value="2">Open</option>
                                                    <option value="3">In Progress</option>
                                                    <option value="4">Resolved</option>
                                                    <option value="5">Closed</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Assigned To -->
                                            <div>
                                                <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assigned To</label>
                                                <select id="assigned_to" x-model="ticketForm.assigned_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="">Unassigned</option>
                                                    <option value="1">Admin User</option>
                                                    <option value="2">Agent User</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end space-x-3 pt-4">
                                            <button 
                                                type="button" 
                                                @click="cancelEdit()" 
                                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            >
                                                Cancel
                                            </button>
                                            <button 
                                                type="submit" 
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            >
                                                Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>