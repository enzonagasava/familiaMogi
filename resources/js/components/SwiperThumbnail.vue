<script setup lang="ts">
import { ref } from 'vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Thumbs, Pagination } from 'swiper/modules';

const activeModules = [Navigation, Thumbs, Pagination];

const props = defineProps<{
    title?: string;
}>();

const slides = ref([
    { id: 1, title: 'Cogumelo Shitake', image:'/images/capa1.jpg'},
    { id: 2, title: 'Cogumelo Shitake', image:'/images/capa1.jpg'},
    { id: 3, title: 'Cogumelo Shitake', image:'/images/capa1.jpg'},
    { id: 4, title: 'Cogumelo Shitake', image:'/images/capa1.jpg'},
    { id: 5, title: 'Cogumelo Paris', image:'/images/capa2.jpg'},
    { id: 6, title: 'Cogumelo Portobello', image:'/images/capa3.jpg'},
    { id: 7, title: 'Cogumelo Eryngui', image:'/images/capa4.jpg'},
    { id: 8, title: 'Cogumelo Hiratake', image:'/images/capa5.jpg'},
]);

</script>

<template>
    <div class="flex flex-col items-center w-full bg-[#f5f5f5] py-6 px-2 rounded-lg">
        <div class="relative w-full max-w-[1366px]">
            <h2 class="text-[24px] lg:text-[36px] font-bold mb-4 w-full">{{ props.title }}</h2>
            <Swiper
                :modules="activeModules"
                :slides-per-view="4"
                :navigation="true"
                :loop="true"
                :space-between="20"
                class="my-custom-swiper"
                :breakpoints="{
                    0: { slidesPerView: 1, spaceBetween: 30 },
                    640: { slidesPerView: 2, spaceBetween: 30 },
                    768: { slidesPerView: 3, spaceBetween: 30 },
                    1024: { slidesPerView: 4, spaceBetween: 58 },
                }"         
                >
                <SwiperSlide v-for="slide in slides" :key="slide.id">
                    <div class="flex flex-col items-center bg-white">
                        <img
                            :src="slide.image"
                            :title="slide.title"
                            alt="Produto"
                            class="min-w-[300px] h-[300px] object-cover rounded-[8px] mb-2 border-4 cursor-pointer hover:brightness-[1.10]"
                            @click="$inertia.visit(route('anuncio'))"
                        />
                        <div class="font-semibold text-lg mt-2 mb-1">
                            <h4 class="cursor-pointer" @click="$inertia.visit(route('anuncio'))">{{ slide.title }}</h4>
                        </div>
                        <div class="text-xs text-gray-600 mb-2">selecione a porção</div>
                        <div class="flex gap-2 mb-3">
                            <button class="border border-gray-400 rounded-full px-3 py-1 text-xs cursor-pointer">200g</button>
                            <button class="border border-gray-400 rounded-full px-3 py-1 text-xs cursor-pointer">1kg</button>
                        </div>
                        <button class="bg-[#6aab9c] text-white rounded-full px-4 py-2 text-sm font-semibold mb-2 hover:bg-[#77bdad] transition cursor-pointer">
                            Adicionar no carrinho
                        </button>
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </div>
</template>


<style scoped>
.my-custom-swiper {
    width: 100%;
    height: 370px;
    padding: 0 3rem !important;
}
.swiper-slide {
    display: flex;
    justify-content: center;
    align-items: stretch;
    position: relative;
}
</style>