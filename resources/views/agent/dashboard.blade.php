<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Gestion des Tickets</title>
</head>
<body class="bg-gray-100">
    <div 
        x-data="ticketManagementSystem()" 
        x-init="initializeData()"
        class="container mx-auto px-4 py-8"
    >
        <div class="bg-white shadow-rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Gestion des Tickets</h1>

            <!-- Filtres -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <select 
                    x-model="filters.status_id" 
                    @change="fetchTickets()"
                    class="form-select border-gray-300 rounded-md"
                >
                    <option value="">Tous les statuts</option>
                    <template x-for="status in statuses" :key="status.id">
                        <option :value="status.id" x-text="status.name"></option>
                    </template>
                </select>

                <select 
                    x-model="filters.priority" 
                    @change="fetchTickets()"
                    class="form-select border-gray-300 rounded-md"
                >
                    <option value="">Toutes les priorités</option>
                    <template x-for="priority in priorities" :key="priority.id">
                        <option :value="priority.name" x-text="priority.name"></option>
                    </template>
                </select>

                <select 
                    x-model="filters.category" 
                    @change="fetchTickets()"
                    class="form-select border-gray-300 rounded-md"
                >
                    <option value="">Toutes les catégories</option>
                    <template x-for="category in categories" :key="category.id">
                        <option :value="category.name" x-text="category.name"></option>
                    </template>
                </select>

                <button 
                    @click="resetFilters()"
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                >
                    Réinitialiser
                </button>
            </div>

            <!-- Tableau des Tickets -->
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-md rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Titre</th>
                            <th class="px-4 py-3 text-left">Statut</th>
                            <th class="px-4 py-3 text-left">Priorité</th>
                            <th class="px-4 py-3 text-left">Catégorie</th>
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
                                            @click="viewTicketDetails(ticket.id)"
                                            class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600"
                                        >
                                            Détails
                                        </button>
                                        <button 
                                            @click="openUpdateModal(ticket)"
                                            class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600"
                                        >
                                            Modifier
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
                    Précédent
                </button>
                <span x-text="`Page ${tickets.current_page} / ${tickets.last_page}`" class="text-gray-600"></span>
                <button 
                    @click="nextPage()" 
                    :disabled="!tickets.next_page_url"
                    class="bg-gray-200 px-4 py-2 rounded-md disabled:opacity-50"
                >
                    Suivant
                </button>
            </div>

            <!-- Modal de Modification de Ticket -->
            <div 
                x-show="updateModal.isOpen" 
                x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
            >
                <div class="bg-white rounded-lg p-6 w-96">
                    <h2 class="text-xl font-bold mb-4">Modifier le Ticket</h2>
                    <form @submit.prevent="updateTicket()">
                        <div class="mb-4">
                            <label class="block mb-2">Statut</label>
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
                            <label class="block mb-2">Priorité</label>
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
                                Annuler
                            </button>
                            <button 
                                type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-md"
                            >
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  
    <style>
    [x-cloak] { display: none !important; }
    </style>
</body>
</html>