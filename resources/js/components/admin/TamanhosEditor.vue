<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

type Tamanho = {
  nome: string;
  preco: number;
}

defineProps<{
  tamanhos: Tamanho[]
}>()

const emits = defineEmits<{
  (e: 'adicionar'): void
  (e: 'remover', index: number): void
}>()

function onRemover(index: number) {
  emits('remover', index)
}
</script>

<template>
  <div>
    <label class="block font-semibold mb-2 text-gray-700">Tamanhos e Preços</label>
    <div v-for="(tamanho, index) in tamanhos" :key="index" class="flex gap-4 items-center mb-3">
      <input
        v-model="tamanho.nome"
        type="text"
        placeholder="Ex: 200g, 1kg"
        class="flex-1 rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        required
      />
      <input
        v-model.number="tamanho.preco"
        type="number"
        min="0"
        step="0.01"
        placeholder="Preço"
        class="w-32 rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        required
      />
      <button
        type="button"
        @click="onRemover(index)"
        class="text-red-600 hover:text-red-800 font-bold px-2 py-1 rounded border border-red-600"
        title="Remover tamanho"
      >
        &times;
      </button>
    </div>
    <button
      type="button"
      @click="$emit('adicionar')"
      class="mt-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded transition"
    >
      + Adicionar Tamanho
    </button>
  </div>
</template>
