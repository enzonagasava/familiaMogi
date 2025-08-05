<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';


const props = defineProps<{
  produto: {
    id: number;
    title: string;
    image: string;
    tamanhos: string;
  };
    addProduct: Record <number | string, string> ;
}>();




const emit = defineEmits(['addProduct']);

const addProductItem = () =>{
    emit('addProduct', props.produto.id);
}

</script>
<template>
    <button class="bg-[#6aab9c] text-white rounded-full px-4 py-2 text-sm font-semibold mb-2 hover:bg-[#77bdad] transition cursor-pointer" 
    @click="addProductItem()"
    >
        Adicionar no carrinho
    </button>
</template>