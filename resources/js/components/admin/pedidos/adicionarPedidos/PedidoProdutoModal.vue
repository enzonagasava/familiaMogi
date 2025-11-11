<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import type { Produto, ProdutoSelecionado } from '@/types/index'
import { Button } from '@/components/ui/button';

const emit = defineEmits(['close', 'adicionar'])

const produtos = ref<Produto[]>([])
const search = ref('')
const produtoSelecionado = ref<ProdutoSelecionado | null>(null)
let controller: AbortController | null = null


async function buscarProdutos() {
  const termo = search.value.trim()

  if (termo.length === 0) {
    produtos.value = []
    produtoSelecionado.value = null
    return
  }

  if (controller) controller.abort()
  controller = new AbortController()

  try {
    console.log('acessando rota')
    const { data } = await axios.get<Produto[]>('/admin/pedidos/buscarProduto', {
      params: { search: termo },
      signal: controller.signal,
    })
    produtos.value = data
  } catch (err: any) {
    if (err.name === 'CanceledError') return
  }
}


const close = () => emit('close')

function selecionarProduto(item: Produto) {
  if (produtoSelecionado.value && produtoSelecionado.value.id === item.id) {
    produtoSelecionado.value.quantidade += 1
  } else {
    const precoBase = item.tamanhos?.[0]?.pivot?.preco ?? 0

    produtoSelecionado.value = {
      ...item,
      quantidade: 1,
      valor_unitario: precoBase,
      valor: precoBase,
    }
  }
}


watch(
  () => [
    produtoSelecionado.value?.quantidade,
    produtoSelecionado.value?.valor_unitario,
  ],
  ([quantidade, valorUnitario]) => {
    if (!produtoSelecionado.value || !quantidade || !valorUnitario) return
    produtoSelecionado.value.valor = quantidade * valorUnitario
  }
)


function adicionar() {
  if (!produtoSelecionado.value) return
  emit('adicionar', { ...produtoSelecionado.value })
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 backdrop-blur-xs">
    <div class="bg-white rounded-lg relative p-6 w-96 space-y-4">
      <h3 class="text-lg font-semibold">Adicionar Produto</h3>

      <button
        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
        @click="close"
      >
        ✕
      </button>

      <!-- Campo de busca -->
      <input
        v-model="search"
        placeholder="Pesquisar Produto"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
        @input="buscarProdutos"
      />

      <!-- Lista de produtos -->
      <div class="space-y-2 max-h-60 overflow-y-auto">
        <div
          v-for="item in produtos"
          :key="item.id"
          class="flex items-center gap-4 border rounded-xl p-3 hover:shadow-md transition cursor-pointer"
          @click="selecionarProduto(item)"
        >
          <img
              :src="item.imagens?.[0]?.imagem_url"
              alt="Imagem do produto"
            class="w-16 h-16 object-cover rounded-lg border"
          />
          <div class="flex-1">
            <h3 class="font-semibold text-sm">{{ item.titulo }}</h3>
            <p class="text-xs text-gray-600 line-clamp-2">{{ item.descricao }}</p>
            <div class="mt-1 text-xs flex justify-between">
              <span class="font-medium text-blue-600">
                R$ {{ item.tamanhos?.[0]?.pivot?.preco ?? '—' }}
              </span>
              <span class="text-gray-500">
                {{ item.estoque }} em estoque
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Produto selecionado -->
      <div v-if="produtoSelecionado" class="border-t pt-3 space-y-2">
        <label class="block font-medium">Produto Selecionado:</label>
        <p class="text-gray-700 font-semibold">{{ produtoSelecionado.titulo }}</p>

        <div class="flex gap-2">
          <input
            v-model.number="produtoSelecionado.quantidade"
            type="number"
            min="1"
            :max="produtoSelecionado.estoque"
            placeholder="Quantidade"
            class="w-1/2 border border-gray-300 rounded-lg px-3 py-2"
          />
          <input
            :value="produtoSelecionado ? produtoSelecionado.valor.toFixed(2) : '0.00'"
            type="text"
            step="0.01"
            placeholder="Valor"
            class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 bg-gray-100"
            readonly
          />
        </div>
      </div>

      <!-- Botões -->
      <div class="flex justify-end gap-2 pt-2">
        <Button @click="emit('close')"
          variant='destructive'
        >
          Cancelar
        </Button>
        <Button
          @click="adicionar"
          variant='default'
          :disabled="!produtoSelecionado"
        >
          Adicionar
        </Button>
      </div>
    </div>
  </div>
</template>
