<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3'; // Se precisar de navegação Inertia aqui

interface CartItem {
  id: number;
  image: string;
  name: string;
  portion: string;
  price: number;
  quantity: number;
}

// Dados do carrinho (em um cenário real, viriam das props do Laravel ou de um store global)
const cartItems = ref<CartItem[]>([
  { id: 1, image: '/images/cogumelo_shiitake.jpg', name: 'Cogumelo Shitake', portion: '200g', price: 19.90, quantity: 1 },
  { id: 2, image: '/images/cogumelo_shiitake.jpg', name: 'Cogumelo Shitake', portion: '200g', price: 19.90, quantity: 1 },
  { id: 3, image: '/images/cogumelo_shiitake.jpg', name: 'Cogumelo Shitake', portion: '200g', price: 19.90, quantity: 1 },
]);

const cepInput = ref('');

// --- Funções de Lógica do Carrinho ---

const incrementQuantity = (item: CartItem) => {
  item.quantity++;
  // Em um app real: enviar atualização para o backend/store
};

const decrementQuantity = (item: CartItem) => {
  if (item.quantity > 1) {
    item.quantity--;
    // Em um app real: enviar atualização para o backend/store
  }
};

const removeItem = (id: number) => {
  cartItems.value = cartItems.value.filter(item => item.id !== id);
  // Em um app real: enviar atualização para o backend/store
};

const calculateShipping = () => {
  if (cepInput.value.length === 8) { // Exemplo de validação simples para CEP
    alert(`Calculando frete para CEP: ${cepInput.value}`);
    // Lógica real de cálculo de frete (integração com API de frete)
  } else {
    alert('Por favor, insira um CEP válido com 8 dígitos.');
  }
};

// --- Computed Property para o Total ---
const cartTotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

</script>

<template>
  <div class="min-h-screen flex justify-center py-12 px-4 sm:px-6 lg:px-8 text-black">
    <div class="max-w-4xl w-full shadow-xl rounded-lg p-8">

      <h1 class="text-3xl font-bold mb-8 text-black">Carrinho</h1>

      <div class="hidden md:grid grid-cols-12 gap-4 text-green-400 font-semibold border-b border-green-700 pb-2 mb-4">
        <div class="col-span-6">Produto:</div>
        <div class="col-span-3 text-center">Quantidade:</div>
        <div class="col-span-2 text-right">Valor:</div>
        <div class="col-span-1"></div> </div>

      <div v-if="cartItems.length === 0" class="text-center py-8 text-black">
        Seu carrinho está vazio.
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="item in cartItems"
          :key="item.id"
          class="grid grid-cols-12 items-center gap-4 py-4 border-b border-gray-700 last:border-b-0"
        >
          <div class="col-span-12 md:col-span-6 flex items-center">
            <img :src="item.image" :alt="item.name" class="w-20 h-20 object-cover rounded-md mr-4 border border-gray-600">
            <div>
              <p class="font-semibold text-black">{{ item.name }}</p>
              <p class="text-sm text-black">Porção: {{ item.portion }}</p>
            </div>
          </div>

          <div class="col-span-6 md:col-span-3 flex items-center justify-center md:justify-start mt-2 md:mt-0">
            <div class="flex items-center border border-gray-600 rounded-md">
              <button @click="decrementQuantity(item)" class="p-2 text-green-400 hover:text-green-500">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M7 10H13V11H7V10Z"/></svg>
              </button>
              <input
                type="text"
                v-model.number="item.quantity"
                class="w-10 text-center bg-white text-black border-x border-gray-600 focus:outline-none"
                @change="item.quantity = Math.max(1, Math.floor(item.quantity))"
              >
              <button @click="incrementQuantity(item)" class="p-2 text-green-400 hover:text-green-500">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M13 9H11V7H10V9H8V10H10V12H11V10H13V9Z"/></svg>
              </button>
            </div>
          </div>

          <div class="col-span-6 md:col-span-3 flex items-center justify-end pr-2 md:pr-0 mt-2 md:mt-0">
            <span class="text-lg font-bold text-black mr-4">R$ {{ (item.price * item.quantity).toFixed(2).replace('.', ',') }}</span>
            <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-600 transition-colors duration-200">
              <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>

      <div class="flex justify-end items-center mt-8 pt-4 border-t border-gray-700">
        <span class="text-xl font-bold text-black mr-4">Total:</span>
        <span class="text-3xl font-bold text-black">R$ {{ cartTotal.toFixed(2).replace('.', ',') }}</span>
      </div>

      <div class="mt-10">
        <h2 class="text-xl font-bold mb-4 text-black">Calcule o frete:</h2>
        <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-3">
          <input
            type="text"
            v-model="cepInput"
            placeholder="Digite o seu CEP"
            class="flex-grow w-full sm:w-auto border border-gray-600 rounded-md py-2 px-4 bg-white text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
            maxlength="8"
          />
          <button
            @click="calculateShipping"
            class="w-full sm:w-auto bg-green-600 text-black py-2 px-6 rounded-md font-semibold hover:bg-green-700 transition duration-300"
          >
            Calcular
          </button>
        </div>
      </div>

      <div class="mt-8 bg-white rounded-lg p-6 text-center text-black h-64 flex items-center justify-center">
        [Localização ou Mapa aqui]
      </div>

    </div>
  </div>
</template>

<style scoped>
/* Estilos específicos se necessário */
input[type="text"]::-webkit-outer-spin-button,
input[type="text"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type="text"] {
  -moz-appearance: textfield;
}
</style>