<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ButtonTable, Button } from '@/components/ui/button'
import { Pencil, Eye } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import HeadingSmall from '@/components/ui/header/HeadingSmall.vue'

const page = usePage()
const pedidos = ref(Array.isArray(page.props.pedidos) ? page.props.pedidos : [])
const status = page.props.status as string

// 🔍 Computed para filtrar automaticamente conforme o status recebido
const pedidosFiltrados = computed(() => {
  if (status === 'Em Andamento') {
    return pedidos.value.filter(p => p.status.toLowerCase() === 'em andamento')
  }
  if (status === 'Finalizado') {
    return pedidos.value.filter(p => p.status.toLowerCase() === 'finalizado')
  }
  return pedidos.value
})
</script>

<template>
  <div class="mb-6 flex items-center justify-between">
    <HeadingSmall
      :title="status === 'Em Andamento' ? 'Pedidos em Andamento' : 'Pedidos Finalizados'"
    />
    <Link :href="route('admin.pedidos.create')">
      <Button> + Adicionar Novo Pedido </Button>
    </Link>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white rounded-xl shadow">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Cliente</th>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Código Pedido</th>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Valor</th>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Endereço</th>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Plataforma</th>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Data</th>
          <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Ações</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-200">
        <tr
          v-for="pedido in pedidosFiltrados"
          :key="pedido.id"
          class="hover:bg-gray-50 transition-colors"
        >
          <td class="text-center">{{ pedido.cliente }}</td>
          <td class="text-center">{{ pedido.cod_pedido }}</td>
          <td class="text-center">{{ pedido.valor }}</td>
          <td class="text-center">{{ pedido.endereco }}</td>
          <td class="text-center">{{ pedido.plataforma }}</td>
          <td class="text-center capitalize">
            <span
              :class="[
                'px-3 py-1 rounded-full text-xs font-semibold',
                pedido.status.toLowerCase() === 'em andamento'
                  ? 'bg-yellow-100 text-yellow-800'
                  : 'bg-green-100 text-green-800'
              ]"
            >
              {{ pedido.status }}
            </span>
          </td>
          <td class="text-center">{{ pedido.created_at_formatted }}</td>
          <td class="px-6 py-4 text-right text-sm font-medium flex justify-end gap-3">
            <Link :href="route('admin.pedidos.edit', pedido.id)">
              <ButtonTable
                :icon="Pencil"
                label="Editar"
                variant="ghost"
                class="text-indigo-600 hover:text-indigo-900"
              />
            </Link>
            <Link :href="route('admin.pedidos.view', pedido.id)">
            <ButtonTable
              :icon="Eye"
              label="Ver"
              variant="ghost"
              class="text-red-600 hover:text-red-900"
            />
            </Link>

          </td>
        </tr>

        <tr v-if="pedidosFiltrados.length === 0">
          <td colspan="8" class="py-6 text-center text-gray-500">
            Nenhum pedido {{ status === 'andamento' ? 'em andamento' : 'finalizado' }} encontrado.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
