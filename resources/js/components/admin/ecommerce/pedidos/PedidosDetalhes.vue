<script setup lang="ts">
defineProps({
    pedido: {
        type: Object,
        required: true,
    },
});

const formatStatus = (status: string) => {
    if (!status) return '';
    return status.replace(/-/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};
</script>

<template>
    <div class="space-y-6 rounded-2xl border border-border bg-card p-6 text-card-foreground shadow-sm">
        <h1 class="text-2xl font-bold text-foreground">Pedido #{{ pedido.cod_pedido }}</h1>

        <!-- Cliente -->
        <section>
            <h2 class="mb-2 text-lg font-semibold text-foreground">Cliente</h2>
            <div class="space-y-1 text-muted-foreground">
                <p><strong>Nome:</strong> {{ pedido.cliente?.nome }}</p>
                <p><strong>Email:</strong> {{ pedido.cliente?.email }}</p>
            </div>
        </section>

        <!-- Produtos -->
        <section>
            <h2 class="mb-2 text-lg font-semibold text-foreground">Produtos</h2>
            <table class="w-full rounded-lg border border-border bg-card text-sm text-foreground">
                <thead class="border-b border-border bg-card">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">Produto</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">Quantidade</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold tracking-wider text-muted-foreground uppercase">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-card">
                    <tr v-for="item in pedido.cod_pedidos" :key="item.id" class="transition-colors hover:bg-accent/40">
                        <td class="px-4 py-2">{{ item.produto?.nome || '—' }}</td>
                        <td class="px-4 py-2 text-center">{{ item.quantidade }}</td>
                        <td class="px-4 py-2 text-right">R$ {{ item.valor_pedido }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Resumo -->
        <section class="text-right">
            <p class="text-muted-foreground"><strong>Status:</strong> {{ formatStatus(pedido.status) }}</p>
            <p class="mt-2 text-xl font-semibold text-foreground">Total: R$ {{ pedido.valor.toFixed(2) }}</p>
        </section>
    </div>
</template>
