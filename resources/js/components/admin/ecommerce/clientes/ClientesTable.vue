<script setup lang="ts">
import ClienteModal from '@/components/admin/ecommerce/clientes/ClientesModal.vue';
import { Button, ButtonTable } from '@/components/ui/button';
import HeadingSmall from '@/components/ui/header/HeadingSmall.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Eye, Pencil } from 'lucide-vue-next';
import { ref } from 'vue';

const page = usePage();
const clientes = ref(Array.isArray(page.props.clientes) ? page.props.clientes : []);

const showModal = ref(false);
const selectedCliente = ref(null);

function verCliente(cliente: any) {
    selectedCliente.value = cliente;
    showModal.value = true;
}
</script>

<template>
    <div class="mb-6 flex items-center justify-between">
        <HeadingSmall title="Gerenciar Clientes" />
        <Link :href="route('admin.adicionar.clientes')">
            <Button> + Adicionar Novo Cliente </Button>
        </Link>
    </div>

    <div class="overflow-x-auto rounded-xl border border-border bg-card">
        <table class="min-w-full bg-card text-card-foreground">
            <thead class="border-b border-border bg-card">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">Id</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">Nome</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">Número</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">E-mail</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">Endereço</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">Data de criação</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold tracking-wider text-muted-foreground uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-card">
                <tr v-for="cliente in clientes" :key="cliente.id" class="transition-colors hover:bg-accent/40">
                    <td class="text-center text-foreground">
                        {{ cliente.id }}
                    </td>
                    <td class="text-center text-foreground">
                        {{ cliente.nome }}
                    </td>
                    <td class="text-center text-foreground">
                        {{ cliente.numero }}
                    </td>
                    <td class="text-center text-foreground">
                        {{ cliente.email }}
                    </td>
                    <td class="text-center text-foreground">
                        {{ cliente.endereco_completo }}
                    </td>
                    <td class="text-center text-foreground">
                        {{ cliente.created_at_formatted }}
                    </td>
                    <td class="flex justify-end gap-3 px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                        <Link :href="route('admin.editar.clientes', cliente.id)">
                            <ButtonTable :icon="Pencil" label="Editar" variant="ghost" class="text-indigo-600 hover:text-indigo-900" />
                        </Link>
                        <ButtonTable :icon="Eye" label="Ver" variant="ghost" class="text-red-600 hover:text-red-900" @click="verCliente(cliente)" />
                    </td>
                </tr>
                <tr v-if="clientes.length === 0">
                    <td colspan="7" class="py-6 text-center text-muted-foreground">Nenhum cliente encontrado.</td>
                </tr>
            </tbody>
        </table>
        <ClienteModal :show="showModal" :cliente="selectedCliente" @close="showModal = false" />
    </div>
</template>
