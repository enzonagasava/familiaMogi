<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useCartStore } from '@/stores/cart';

interface Produto {
  id: number;
  name: string;
}

const props = withDefaults(
  defineProps<{
    produto: Produto;
    portion: string;
    price?: number;
  }>(),
  { price: 0 }
);

const emit = defineEmits<{
  (e: 'add-to-cart', produto: Produto, portion: string): void;
}>();

const cartStore = useCartStore();
const isLoading = ref(false);

const addToCart = async (e: SubmitEvent) => {
  e.preventDefault();        // evita recarregar a página
  if (isLoading.value) return;

  isLoading.value = true;

  try {
    const { data } = await axios.post('/cart/add', {
      id: props.produto.id,
      porcao: props.portion,
      preco: props.price,
      quantidade: 1,
    });

    console.log('RESPOSTA /cart/add =>', data);

    cartStore.setCart(Object.values(data.cart));
    emit('add-to-cart', props.produto, props.portion);
  } catch (err) {
    console.error('Erro ao adicionar produto:', err);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <form @submit="addToCart">
    <button
      type="submit"
      :disabled="isLoading"
      class="bg-[#6aab9c] text-white rounded-md px-4 py-3 text-white font-semibold mb-2 hover:bg-[#77bdad] transition cursor-pointer disabled:opacity-50 w-full"
    >
      {{ isLoading ? 'Adicionando…' : 'Adicionar no carrinho' }}
    </button>
  </form>
</template>