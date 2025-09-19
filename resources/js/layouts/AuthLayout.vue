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
        <aside class="fixed inset-y-0 left-0 w-64 space-y-4 bg-gray-800 p-4 text-white">
            <h2 class="mb-6 text-xl font-bold">Painel Admin</h2>
            <nav class="space-y-2">
                <Link :href="route('admin.dashboard')" class="block rounded px-2 py-2 hover:bg-gray-700">Dashboard</Link>
                <Link :href="route('anuncio.config')" class="block rounded px-2 py-2 hover:bg-gray-700">Anúncios</Link>
                <Link :href="route('produtos.config')" class="block rounded px-2 py-2 hover:bg-gray-700">Produtos</Link>
                <Link :href="route('blog.config')" class="block rounded px-2 py-2 hover:bg-gray-700">Blogs</Link>
                <Link :href="route('appearance')" class="block rounded px-2 py-2 hover:bg-gray-700">Configurações</Link>
                <Link class="flex w-full rounded px-2 py-2 hover:bg-gray-700" method="post" :href="route('logout')" @click="handleLogout" as="button">
                    <LogOut />
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
