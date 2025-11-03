<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DashboardClient from '@/components/app/dashboard/DashboardClient.vue';
import DashboardAdmin from '@/components/admin/dashboard/DashboardAdmin.vue';

const page = usePage();

const user = computed(() => page.props.auth?.user || null);

const userCargo = user.value.cargo_id;
console.log(userCargo);
</script>

<template>
    <Head>
        <title>Família Mogi - Dashboard</title>
        <meta name="description" content="Dashboard da Família Mogi" />
    </Head>
    
    <component :is="userCargo === 1 ? AuthLayout : userCargo === 2 ? AppLayout : 'div'">
        <div class="flex min-h-screen justify-center px-4 py-12 text-black sm:px-6 lg:px-8">
            <div class="w-full">
                <DashboardAdmin v-if="userCargo === 1" />
                <DashboardClient v-else-if="userCargo === 2" />
                <div v-else>Usuário sem dashboard definido.</div>            
            </div>
        </div>
    </component>
</template>
