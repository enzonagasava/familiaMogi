<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ProductDetails from '@/components/ProductDetails.vue';
import SwiperThumbnail from '@/components/SwiperThumbnail.vue';

</script>

<template>
    <div>
        <Head>
            <title>Família Mogi - Anúncio</title>
            <meta name="description" content="Anúncio da Família Mogi" />
        </Head>
        <AppLayout>
        <ProductDetails>
        </ProductDetails>
        <SwiperThumbnail
            title="Veja Também"
        >
        </SwiperThumbnail>
        </AppLayout>    
    </div>
</template>