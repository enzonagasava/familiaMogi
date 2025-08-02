<script setup lang="ts">
import CartDetails from '@/components/CartDetails.vue';
import SwiperThumbnail from '@/components/SwiperThumbnail.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <Head>
            <title>Família Mogi - Anúncio</title>
            <meta name="description" content="Anúncio da Família Mogi" />
        </Head>
        <AppLayout>
            <CartDetails> </CartDetails>
            <SwiperThumbnail title="Veja Também"> </SwiperThumbnail>
        </AppLayout>
    </div>
</template>
