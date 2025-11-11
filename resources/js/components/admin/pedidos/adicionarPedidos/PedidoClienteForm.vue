<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import ClienteCard from '@/components/ui/card/ClienteCard.vue'

const props = defineProps({
  plataformas: Array,
  modelValue: Object,
  status: String,
  data: String,
  endereco: String,
  plataformaSelecionada: Number
})

const emit = defineEmits([
  'update:modelValue',
  'update:status',
  'update:data',
  'update:endereco',
  'update:plataformaSelecionada'
])

const search = ref('')
const clientes = ref<any[]>([])

// --- usamos um proxy local vinculado ao modelValue ---
const clienteSelecionado = ref(props.modelValue ?? null)

watch(() => props.modelValue, (novo) => {
  clienteSelecionado.value = novo
})

// --- busca clientes ---
let controller: AbortController | null = null

async function buscarClientes() {
  const termo = search.value.trim()

  if (!termo.length) {
    clientes.value = []
    return
  }

  if (controller) controller.abort()
  controller = new AbortController()

  try {
    const { data } = await axios.get(route('clientes.buscar'), {
      params: { search: termo },
      signal: controller.signal,
    })
    clientes.value = data
  } catch (err: any) {
    if (err.name === 'CanceledError') return
  }
}

// --- selecionar cliente ---
function selecionarCliente(c: any) {
  clienteSelecionado.value = c
  emit('update:modelValue', c)
  search.value = c.nome
  clientes.value = []
}
</script>

<template>
  <div class="space-y-4">
    <div class="relative">
      <input
        v-model="search"
        type="search"
        placeholder="Buscar cliente..."
        class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        @input="buscarClientes"
      />
      <ul
        v-if="clientes.length"
        class="absolute bg-white border rounded-lg w-full mt-1 z-10"
      >
        <li
          v-for="c in clientes"
          :key="c.id"
          @click="selecionarCliente(c)"
          class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
        >
          {{ c.id }} — {{ c.nome }} — {{ c.email }}
        </li>
      </ul>
    </div>

    <div>
      <ClienteCard v-if="clienteSelecionado" :cliente="clienteSelecionado" />
      <div
        v-else
        class="border border-dashed border-gray-300 rounded-2xl p-6 text-center text-gray-500 bg-gray-50"
      >
        Nenhum cliente selecionado ainda.
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4" v-if="clienteSelecionado">
      <div>
        <label class="block text-sm font-medium mb-1">Endereço</label>
        <input
          :value="props.endereco"
          @input="emit('update:endereco', $event.target.value)"
          type="text"
          class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Plataforma</label>
        <select
          :value="props.plataformaSelecionada"
          @change="emit('update:plataformaSelecionada', $event.target.value)"
          class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          <option value="">Selecione...</option>
          <option
            v-for="p in props.plataformas"
            :key="p.id"
            :value="p.id"
          >
            {{ p.nome }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Data</label>
        <input
          type="datetime-local"
          :value="props.data"
          @input="$emit('update:data', $event.target.value)"
          class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select
          :value="props.status"
          @change="$emit('update:status', $event.target.value)"
          class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          <option>Em Andamento</option>
          <option>Finalizado</option>
        </select>
      </div>
    </div>
  </div>
</template>
