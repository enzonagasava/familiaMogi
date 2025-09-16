<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import PaymentDetails from '@/components/PaymentDetails.vue';
</script>

<template>
    <Head>
        <title>Família Mogi - Pagamento</title>
        <meta name="description" content="Produtos da Família Mogi" />
    </Head>
    <AppLayout>
        <PaymentDetails></PaymentDetails>
    </AppLayout>
</template>
