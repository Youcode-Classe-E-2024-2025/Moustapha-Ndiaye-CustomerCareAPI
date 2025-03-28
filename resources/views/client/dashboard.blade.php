<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Support Ticket</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12" x-data="ticketSubmission()">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Customer Support</h1>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Submit a ticket and our support team will help you as soon as possible.
                </p>
            </div>
            
            <!-- Success Message -->
            <div x-show="successMessage" x-cloak class="rounded-md bg-green-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Ticket Submitted Successfully</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p x-text="successMessage"></p>
                        </div>
                        <div class="mt-4">
                            <button 
                                @click="resetForm()" 
                                type="button" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                Submit another ticket
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Error Message -->
            <div x-show="errorMessage" x-cloak class="rounded-md bg-red-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There was an error submitting your ticket</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p x-text="errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Form -->
            <div x-show="!successMessage" class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-100">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">New Support Ticket</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Please provide as much detail as possible so we can help you quickly.</p>
                </div>
                
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <form @submit.prevent="submitTicket">
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <div class="mt-1">
                                    <input 
                                        type="text" 
                                        name="title" 
                                        id="title" 
                                        x-model="ticketForm.title"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="Brief summary of your issue"
                                        required
                                    >
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Keep it short and descriptive.</p>
                            </div>
                            
                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="mt-1">
                                    <textarea 
                                        id="description" 
                                        name="description" 
                                        rows="5" 
                                        x-model="ticketForm.description"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                        placeholder="Please provide details about your issue..."
                                        required
                                    ></textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Include any error messages, steps to reproduce, and what you've already tried.</p>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <!-- Category -->
                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                    <div class="mt-1">
                                        <select 
                                            id="category" 
                                            name="category" 
                                            x-model="ticketForm.category"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            required
                                        >
                                            <option value="">Select a category</option>
                                            <option value="Login Issue">Login Issue</option>
                                            <option value="Account Access">Account Access</option>
                                            <option value="Payment Problem">Payment Problem</option>
                                            <option value="Technical Error">Technical Error</option>
                                            <option value="Feature Request">Feature Request</option>
                                            <option value="General Question">General Question</option>
                                            <option value="Bug">Bug</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Priority -->
                                <div>
                                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                    <div class="mt-1">
                                        <select 
                                            id="priority" 
                                            name="priority" 
                                            x-model="ticketForm.priority"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            required
                                        >
                                            <option value="">Select priority</option>
                                            <option value="low">Low - No rush</option>
                                            <option value="medium">Medium - Affects my work</option>
                                            <option value="high">High - Urgent issue</option>
                                            <option value="urgent">Urgent - Critical problem</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    :disabled="loading"
                                >
                                    <span x-show="loading" class="mr-2">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    <span x-text="loading ? 'Submitting...' : 'Submit Ticket'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Recent Tickets Section -->
            <div class="mt-10" x-show="userTickets.length > 0">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Your Recent Tickets</h2>
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <template x-for="ticket in userTickets" :key="ticket.id">
                            <li>
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-blue-600 truncate" x-text="ticket.title"></p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p 
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800': ticket.status.name === 'Open',
                                                    'bg-yellow-100 text-yellow-800': ticket.status.name === 'In Progress',
                                                    'bg-blue-100 text-blue-800': ticket.status.name === 'New',
                                                    'bg-gray-100 text-gray-800': ticket.status.name === 'Closed'
                                                }"
                                                x-text="ticket.status.name"
                                            ></p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                <span class="truncate" x-text="ticket.category"></span>
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                <span 
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="{
                                                        'bg-red-100 text-red-800': ticket.priority === 'high' || ticket.priority === 'urgent',
                                                        'bg-yellow-100 text-yellow-800': ticket.priority === 'medium',
                                                        'bg-green-100 text-green-800': ticket.priority === 'low'
                                                    }"
                                                    x-text="ticket.priority"
                                                ></span>
                                            </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <p>
                                                <span>Created on </span>
                                                <span x-text="new Date(ticket.created_at).toLocaleDateString()"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function ticketSubmission() {
            return {
                ticketForm: {
                    title: '',
                    description: '',
                    priority: '',
                    category: '',
                    // These fields will be set by the backend or have default values
                    creator_id: 2, // This would typically come from the authenticated user
                    assigned_to: null, // Let the system assign this
                    status_id: 1 // New tickets typically start with status "New"
                },
                loading: false,
                successMessage: null,
                errorMessage: null,
                userTickets: [],
                
                init() {
                    // Fetch user's recent tickets if they're logged in
                    this.fetchUserTickets();
                },
                
                async fetchUserTickets() {
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/tickets?creator_id=2', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to fetch your tickets');
                        }
                        
                        const data = await response.json();
                         // Show only the 5 most recent tickets
                        this.userTickets = data.data.slice(0, 5);
                    } catch (error) {
                        console.error('Error fetching tickets:', error);
                        // We don't show an error for this as it's not critical
                    }
                },
                
                async submitTicket() {
                    this.loading = true;
                    this.errorMessage = null;
                    
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/tickets', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.ticketForm)
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to submit ticket. Please try again.');
                        }
                        
                        const data = await response.json();
                        
                        // Show success message with ticket ID
                        this.successMessage = `Your ticket #${data.data.id} has been submitted successfully. Our support team will review it shortly.`;
                        
                        // Add the new ticket to the user's tickets list
                        this.userTickets.unshift(data.data);
                        
                    } catch (error) {
                        console.error('Error submitting ticket:', error);
                        this.errorMessage = error.message;
                    } finally {
                        this.loading = false;
                    }
                },
                
                resetForm() {
                    this.ticketForm = {
                        title: '',
                        description: '',
                        priority: '',
                        category: '',
                        creator_id: 2,
                        assigned_to: null,
                        status_id: 1
                    };
                    this.successMessage = null;
                }
            };
        }
    </script>
</body>
</html>