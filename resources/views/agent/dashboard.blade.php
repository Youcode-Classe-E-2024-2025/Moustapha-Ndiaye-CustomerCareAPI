<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Ticket Management</title>
</head>
<body class="bg-gray-100">
    <div 
        x-data="ticketManagementSystem()" 
        x-init="initializeData()"
        class="container mx-auto px-4 py-8"
    >
        <div class="bg-white shadow-rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Ticket Management</h1>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <select 
                    x-model="filters.status_id" 
                    @change="fetchTickets()"
                    class="form-select border-gray-300 rounded-md"
                >
                    <option value="">All statuses</option>
                    <template x-for="status in statuses" :key="status.id">
                        <option :value="status.id" x-text="status.name"></option>
                    </template>
                </select>

                <select 
                    x-model="filters.priority" 
                    @change="fetchTickets()"
                    class="form-select border-gray-300 rounded-md"
                >
                    <option value="">All priorities</option>
                    <template x-for="priority in priorities" :key="priority.id">
                        <option :value="priority.name" x-text="priority.name"></option>
                    </template>
                </select>

                <select 
                    x-model="filters.category" 
                    @change="fetchTickets()"
                    class="form-select border-gray-300 rounded-md"
                >
                    <option value="">All categories</option>
                    <template x-for="category in categories" :key="category.id">
                        <option :value="category.name" x-text="category.name"></option>
                    </template>
                </select>

                <button 
                    @click="resetFilters()"
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                >
                    Reset
                </button>
            </div>

            <!-- Tickets Table -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-md rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Title</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Priority</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="ticket in tickets.data" :key="ticket.id">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3" x-text="ticket.id"></td>
                                <td class="px-4 py-3" x-text="ticket.title"></td>
                                <td class="px-4 py-3">
                                    <span 
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': ticket.status.id === 1,
                                            'bg-blue-100 text-blue-800': ticket.status.id === 2,
                                            'bg-green-100 text-green-800': ticket.status.id === 3
                                        }"
                                        class="px-2 py-1 rounded-full text-sm"
                                        x-text="ticket.status.name"
                                    ></span>
                                </td>
                                <td class="px-4 py-3">
                                    <span 
                                        :class="{
                                            'bg-green-100 text-green-800': ticket.priority.name === 'low',
                                            'bg-yellow-100 text-yellow-800': ticket.priority.name === 'medium',
                                            'bg-red-100 text-red-800': ticket.priority.name === 'high'
                                        }"
                                        class="px-2 py-1 rounded-full text-sm"
                                        x-text="ticket.priority.name"
                                    ></span>
                                </td>
                                <td class="px-4 py-3" x-text="ticket.category.name"></td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button 
                                            @click="openUpdateModal(ticket)"
                                            class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600"
                                        >
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <button 
                    @click="prevPage()" 
                    :disabled="!tickets.prev_page_url"
                    class="bg-gray-200 px-4 py-2 rounded-md disabled:opacity-50"
                >
                    Previous
                </button>
                <span x-text="`Page ${tickets.current_page} / ${tickets.last_page}`" class="text-gray-600"></span>
                <button 
                    @click="nextPage()" 
                    :disabled="!tickets.next_page_url"
                    class="bg-gray-200 px-4 py-2 rounded-md disabled:opacity-50"
                >
                    Next
                </button>
            </div>

            <!-- Ticket Update Modal -->
            <div 
                x-show="updateModal.isOpen" 
                x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
            >
                <div class="bg-white rounded-lg p-6 w-96">
                    <h2 class="text-xl font-bold mb-4">Edit Ticket</h2>
                    <form @submit.prevent="updateTicket()">
                        <div class="mb-4">
                            <label class="block mb-2">Status</label>
                            <select 
                                x-model="updateModal.status_id" 
                                class="w-full border rounded-md p-2"
                            >
                                <template x-for="status in statuses" :key="status.id">
                                    <option :value="status.id" x-text="status.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block mb-2">Priority</label>
                            <select 
                                x-model="updateModal.priority_id" 
                                class="w-full border rounded-md p-2"
                            >
                                <template x-for="priority in priorities" :key="priority.id">
                                    <option :value="priority.id" x-text="priority.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button 
                                type="button" 
                                @click="updateModal.isOpen = false"
                                class="bg-gray-200 px-4 py-2 rounded-md"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-md"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function ticketManagementSystem() {
        return {
            tickets: {
                data: [],
                current_page: 1,
                last_page: 1,
                prev_page_url: null,
                next_page_url: null
            },
            statuses: [],
            priorities: [],
            categories: [],
            filters: {
                status_id: '',
                priority: '',
                category: ''
            },
            updateModal: {
                isOpen: false,
                ticketId: null,
                status_id: null,
                priority_id: null
            },
            
            initializeData() {
                this.fetchStatuses()
                this.fetchPriorities()
                this.fetchCategories()
                this.fetchTickets()
            },

            async fetchStatuses() {
                try {
                    const response = await fetch('/api/tickets/statuses')
                    this.statuses = await response.json()
                } catch (error) {
                    console.error('Error loading statuses:', error)
                }
            },

            async fetchPriorities() {
                try {
                    const response = await fetch('/api/tickets/priorities')
                    this.priorities = await response.json()
                } catch (error) {
                    console.error('Error loading priorities:', error)
                }
            },

            async fetchCategories() {
                try {
                    const response = await fetch('/api/tickets/categories')
                    this.categories = await response.json()
                } catch (error) {
                    console.error('Error loading categories:', error)
                }
            },

            async fetchTickets() {
                try {
                    const params = new URLSearchParams()
                    if (this.filters.status_id) params.append('status_id', this.filters.status_id)
                    if (this.filters.priority) params.append('priority', this.filters.priority)
                    if (this.filters.category) params.append('category', this.filters.category)
                    
                    const response = await fetch(`/api/tickets?${params.toString()}`)
                    this.tickets = await response.json()
                } catch (error) {
                    console.error('Error loading tickets:', error)
                }
            },

            async updateTicket() {
                try {
                    const response = await fetch(`/api/tickets/${this.updateModal.ticketId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            status_id: this.updateModal.status_id,
                            priority_id: this.updateModal.priority_id
                        })
                    })

                    if (response.ok) {
                        this.updateModal.isOpen = false
                        this.fetchTickets()
                    }
                } catch (error) {
                    console.error('Error updating ticket:', error)
                }
            },

            resetFilters() {
                this.filters = {
                    status_id: '',
                    priority: '',
                    category: ''
                }
                this.fetchTickets()
            },

            openUpdateModal(ticket) {
                this.updateModal = {
                    isOpen: true,
                    ticketId: ticket.id,
                    status_id: ticket.status.id,
                    priority_id: ticket.priority.id
                }
            },

            prevPage() {
                if (this.tickets.prev_page_url) {
                    this.tickets.current_page--
                    this.fetchTickets()
                }
            },

            nextPage() {
                if (this.tickets.next_page_url) {
                    this.tickets.current_page++
                    this.fetchTickets()
                }
            }
        }
    }
    </script>
</body>
</html>
