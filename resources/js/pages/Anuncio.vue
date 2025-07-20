<script setup lang="ts">
import ProductDetails from '@/components/ProductDetails.vue';
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
            <ProductDetails> </ProductDetails>
            <SwiperThumbnail title="Veja Também"> </SwiperThumbnail>
        </AppLayout>
    </div>
</template>
