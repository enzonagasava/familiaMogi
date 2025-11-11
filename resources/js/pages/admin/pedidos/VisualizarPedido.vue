<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Head } from '@inertiajs/vue3'
import PedidosDetalhes from '@/components/admin/pedidos/PedidosDetalhes.vue'

const props = defineProps({
  pedido: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <Head title="Visualizar Pedido" />

  <AuthLayout>
        <PedidosDetalhes :pedido="props.pedido" />
  </AuthLayout>
</template>
