<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
</script>

<template>
    <Head>
        <title>Família Mogi - Dashboard</title>
        <meta name="description" content="Dashboard da Família Mogi" />
    </Head>
    <AuthLayout>
        <div class="min-h-screen flex justify-center py-12 px-4 sm:px-6 lg:px-8 text-black">
            <div class="w-[1366px]">
                <h1>Dashboard</h1>
                <p>Bem-vindo ao painel da Família Mogi!</p>
            </div>
        </div>
    </AuthLayout>
</template>