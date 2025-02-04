<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Saraswanti Indo Genetech</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        @keyframes popup {
            0% {
                transform: scale(0.95);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-popup {
            animation: popup 0.3s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div id="app" class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <img src="https://cdnb.uns.ac.id/cdc/image/2022/10/cdhdoL-oUvDNOzrr2NvwjerXCyOHjY7I.jpg" alt="PT Saraswanti Indo Genetech" class="mx-auto h-16">
        </div>
        <div class="mb-8 bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">PT Saraswanti Indo Genetech</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block mb-2">Type</label>
                    <select v-model="filters.type" class="w-full p-2 border rounded">
                        <option value="">All</option>
                        @foreach($types as $code => $title)
                            <option value="{{ $code }}">{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-2">Status</label>
                    <select v-model="filters.status" class="w-full p-2 border rounded">
                        <option value="">All</option>
                        <option value="1">Approved</option>
                        <option value="0">Unapproved</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2">Attachment</label>
                    <select v-model="filters.attachment" class="w-full p-2 border rounded">
                        <option value="">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2">Discount</label>
                    <select v-model="filters.discount" class="w-full p-2 border rounded">
                        <option value="">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachment</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="product in products" :key="product.id">
                        <td class="px-6 py-4 whitespace-nowrap">@{{ product.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">@{{ product.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">@{{ product.type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="product.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                @{{ product.status ? 'Approved' : 'Unapproved' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">@{{ formatCurrency(product.price) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @{{ product.discount > 0 ? formatCurrency(product.discount) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @{{ product.attachment ? 'Yes' : 'No' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Discount Modal -->
        <div v-if="showDiscountModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full sm:max-w-sm transform transition-all duration-300 scale-95 opacity-0 animate-popup">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Discount Alert</h3>
                <p class="text-gray-600 mb-4">@{{ discountMessage }}</p>
                <button @click="showDiscountModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                products: [],
                filters: {
                    type: '',
                    status: '',
                    attachment: '',
                    discount: ''
                },
                showDiscountModal: false,
                discountMessage: ''
            },
            methods: {
                fetchProducts() {
                    axios.get('/fetch-products', {
                        params: {
                            ...this.filters
                        }
                    })
                    .then(response => {
                        this.products = response.data;
                        this.checkDiscounts();
                    });
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(value);
                },
                checkDiscounts() {
                    this.products.forEach(product => {
                        if (product.discount > 0 && product.discount < 1000000) {
                            this.showDiscountModal = true;
                            this.discountMessage = `Discount: ${this.formatCurrency(product.discount)}`;
                        } else if (product.discount >= 1000000) {
                            this.showDiscountModal = true;
                            this.discountMessage = `Discount: ${this.formatCurrency(product.discount)} - Approval needed`;
                        }
                    });
                }
            },
            watch: {
                filters: {
                    deep: true,
                    handler() {
                        this.fetchProducts();
                    }
                }
            },
            mounted() {
                this.fetchProducts();
            }   
        });
    </script>
</body>
</html>
