<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { ref } from 'vue';

// --- Dados de Exemplo ---
// Em uma aplicação real, estes dados viriam do seu backend (ex: via props do Inertia).
const products = ref([
]);

// --- Funções de Ação ---
// Estas funções simulam o que aconteceria ao clicar nos botões.
// Você deve substituí-las pela lógica de API (ex: usando o router do Inertia).

const editProduct = (productId: number) => {
    // Exemplo: router.get(`/admin/products/${productId}/edit`);
    alert(`Redirecionando para editar o produto ID: ${productId}`);
};

const deleteProduct = (productId: number) => {
    // Exemplo: router.delete(`/admin/products/${productId}`);
    if (confirm(`Tem certeza de que deseja excluir o produto ID: ${productId}?`)) {
        alert(`Excluindo o produto ID: ${productId}`);
        // Aqui você removeria o item da lista após a exclusão no backend.
        products.value = products.value.filter(p => p.id !== productId);
    }
};

</script>

<template>
    <Head>
        <title>Gerenciar Produtos - Família Mogi</title>
        <meta name="description" content="Gerenciamento de produtos da Família Mogi" />
    </Head>
    <AuthLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-800">
                                Gerenciar Produtos
                            </h1>
                            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition ease-in-out duration-150">
                                + Adicionar Novo Produto
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Produto
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descrição do Produto
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <img :src="product.imageUrl" :alt="product.name" class="w-16 h-16 object-cover rounded-md">
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ product.name }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="editProduct(product.id)" class="text-indigo-600 hover:text-indigo-900 mr-4 transition duration-150 ease-in-out">
                                                Editar
                                            </button>
                                            <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out">
                                                Excluir
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="products.length === 0">
                                      <td colspan="3" class="text-center py-6 text-gray-500">
                                        Nenhum produto encontrado.
                                      </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>