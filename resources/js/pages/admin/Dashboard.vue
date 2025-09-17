<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user || null);

const userCargo = user.value.cargo_id;
</script>

<template>
    <Head>
        <title>Família Mogi - Dashboard</title>
        <meta name="description" content="Dashboard da Família Mogi" />
    </Head>
    
    <component :is="userCargo === 1 ? AuthLayout : userCargo === 2 ? AppLayout : 'div'">
        <div class="flex min-h-screen justify-center px-4 py-12 text-black sm:px-6 lg:px-8">
            <div class="w-[1366px]">
                <h1>Dashboard</h1>
                <p>Bem-vindo ao painel da Família Mogi!</p>
            </div>
        </div>
    </component>
</template>
