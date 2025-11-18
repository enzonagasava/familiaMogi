<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head } from '@inertiajs/vue3'
import PedidosTable from '@/components/admin/pedidos/PedidosTable.vue'
import type { Pedido } from '@/types'

const props = withDefaults(defineProps<{
  pedidos: Pedido[]
  statusFiltro?: string
}>(), {
  statusFiltro: 'todos'
})
</script>

<template>
  <Head>
    <title>Pedidos - Família Mogi</title>
  </Head>

  <AuthLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

              <div class="space-x-4">

              </div>

          <div class="p-6">
            <PedidosTable :pedidos="props.pedidos" :status-filtro="props.statusFiltro" />
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>
