<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import PaymentDetails from '@/components/payment/PaymentDetails.vue';

interface CartProps {
  cart: Record<string, any>;
}

const page = usePage<CartProps>();
const cart = page.props.cart;
</script>

<template>
    <Head>
        <title>Família Mogi - Pagamento</title>
        <meta name="description" content="Produtos da Família Mogi" />
    </Head>
    <AppLayout>
        <PaymentDetails :cart="cart"/>
    </AppLayout>
</template>
