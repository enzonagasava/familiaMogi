<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';
import { House } from 'lucide-vue-next';
import DropdownButtonAdmin from '@/components/ui/dropdown-button/DropdownButtonAdmin.vue';

defineProps<{
    title?: string;
}>();

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 left-0 w-64 space-y-4 bg-gray-800 p-4 text-white">
            <h2 class="mb-6 text-xl font-bold">Painel Admin</h2>
            <nav class="space-y-2">
                <Link :href="route('admin.dashboard')" class="block rounded px-2 py-2 hover:bg-gray-700">Dashboard</Link>
                <Link :href="route('anuncio.config')" class="block rounded px-2 py-2 hover:bg-gray-700">Anúncios</Link>
                <Link :href="route('paginas.config')" class="block rounded px-2 py-2 hover:bg-gray-700">Páginas</Link>
                <Link :href="route('produtos.config')" class="block rounded px-2 py-2 hover:bg-gray-700">Produtos</Link>
                <DropdownButtonAdmin label="Clientes" class="relative">
                    <Link :href="route('adicionar.clientes')" class="block rounded px-4 py-2 hover:bg-gray-700">
                        Adicionar Cliente
                    </Link>
                    <Link :href="route('clientes.index')" class="block rounded px-4 py-2 hover:bg-gray-700">
                        Clientes
                    </Link>

                </DropdownButtonAdmin>

                <DropdownButtonAdmin label='Pedidos' class="relative">
                    <Link :href="route('admin.pedidos.create')" class="block rounded px-4 py-2 hover:bg-gray-700">Adicionar Pedido</Link>
                    <Link :href="route('admin.pedidos.index', 'em-andamento')" class="block rounded px-4 py-2  hover:bg-gray-700">Pedidos em Andamento</Link>
                    <Link :href="route('admin.pedidos.index', 'a-caminho')" class="block rounded px-4 py-2  hover:bg-gray-700">Pedidos a Caminho</Link>
                    <Link :href="route('admin.pedidos.index', 'finalizado')" class="block rounded px-4 py-2  hover:bg-gray-700">Pedidos Finalizados</Link>

                </DropdownButtonAdmin>

                <DropdownButtonAdmin label="Configurações" class="relative">
                    <Link :href="route('config.geral')" class="block rounded px-4 py-2 hover:bg-gray-700">Acesso</Link>
                    <Link :href="route('empresa.config.geral')" class="block rounded px-4 py-2 hover:bg-gray-700">Informações da Empresa</Link>
                    <Link :href="route('config.pagamento')" class="block rounded px-4 py-2 hover:bg-gray-700">Métodos de Pagamento</Link>

                </DropdownButtonAdmin>
                <Link :href="route('home')" class="flex w-full rounded px-2 py-2 hover:bg-gray-700">
                    <House class="mr-[0.5rem]"/>
                    Ir para o site</Link>
                <Link class="flex w-full rounded px-2 py-2 hover:bg-gray-700" method="post" :href="route('logout')" @click="handleLogout" as="button">
                    <LogOut class="mr-[0.5rem]"/>
                    Log out
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1 bg-gray-100 p-6">
            <h1 class="mb-4 text-2xl font-semibold">{{ title }}</h1>
            <slot />
        </main>
    </div>
</template>
