<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';


defineProps<{
    title?: string;
}>();

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white p-4 space-y-4 fixed inset-y-0 left-0">
        <h2 class="text-xl font-bold mb-6">Painel Admin</h2>
        <nav class="space-y-2">
            <Link :href="route('dashboard')" class="block px-2 py-2 rounded hover:bg-gray-700">Dashboard</Link>
            <Link :href="route('anuncio.config')" class="block px-2 py-2 rounded hover:bg-gray-700">Anúncios</Link>
            <Link :href="route('produtos.config')" class="block px-2 py-2 rounded hover:bg-gray-700">Produtos</Link>
            <Link :href="route('blog.config')"  class="block px-2 py-2 rounded hover:bg-gray-700">Blogs</Link>
            <Link :href="route('appearance')" class="block px-2 py-2 rounded hover:bg-gray-700">Configurações</Link>
            <Link class="flex w-full px-2 py-2 rounded hover:bg-gray-700" method="post" :href="route('logout')" @click="handleLogout" as="button">
            <LogOut  />
            Log out
            </Link>
        </nav>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 flex-1 bg-gray-100 p-6">
        <h1 class="text-2xl font-semibold mb-4">{{ title }}</h1>
        <slot />
        </main>
    </div>
</template>
