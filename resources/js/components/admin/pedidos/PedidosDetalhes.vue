<script setup lang="ts">
defineProps({
  pedido: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="bg-white shadow rounded-2xl p-6 space-y-6">
    <h1 class="text-2xl font-bold">Pedido #{{ pedido.cod_pedido }}</h1>

    <!-- Cliente -->
    <section>
      <h2 class="text-lg font-semibold mb-2">Cliente</h2>
      <div class="text-gray-700 space-y-1">
        <p><strong>Nome:</strong> {{ pedido.cliente?.nome }}</p>
        <p><strong>Email:</strong> {{ pedido.cliente?.email }}</p>
      </div>
    </section>

    <!-- Produtos -->
    <section>
      <h2 class="text-lg font-semibold mb-2">Produtos</h2>
      <table class="w-full border border-gray-200 rounded-lg text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Produto</th>
            <th class="px-4 py-2 text-center">Quantidade</th>
            <th class="px-4 py-2 text-right">Valor</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in pedido.cod_pedidos"
            :key="item.id"
            class="border-t"
          >
            <td class="px-4 py-2">{{ item.produto?.nome || '—' }}</td>
            <td class="px-4 py-2 text-center">{{ item.quantidade }}</td>
            <td class="px-4 py-2 text-right">R$ {{ item.valor_pedido }}</td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Resumo -->
    <section class="text-right">
      <p class="text-gray-700">
        <strong>Status:</strong> {{ pedido.status }}
      </p>
      <p class="text-xl font-semibold mt-2">
        Total: R$ {{ pedido.valor.toFixed(2) }}
      </p>
    </section>
  </div>
</template>
