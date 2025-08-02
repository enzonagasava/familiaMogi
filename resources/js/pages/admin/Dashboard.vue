<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title>Família Mogi - Dashboard</title>
        <meta name="description" content="Dashboard da Família Mogi" />
    </Head>
    <AuthLayout>
        <div class="flex min-h-screen justify-center px-4 py-12 text-black sm:px-6 lg:px-8">
            <div class="w-[1366px]">
                <h1>Dashboard</h1>
                <p>Bem-vindo ao painel da Família Mogi!</p>
            </div>
        </div>
    </AuthLayout>
</template>
