<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head } from '@inertiajs/vue3'
import PedidosTable from '@/components/admin/pedidos/PedidosTable.vue'

defineProps({
  pedidos: {
    type: Array,
    default: () => []
  },
  status: {
    type: String,
    default: 'Em Andamento'
  }
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
            <PedidosTable :pedidos="pedidos" :status="status" />
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>
