<script setup lang="ts">
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { ref } from 'vue';

const testimonials = ref([
    {
        name: 'Igor S.',
        avatar: '/images/avatar-igor.jpg',
    },
    {
        name: 'Maria',
        avatar: '/images/avatar-maria.jpg',
    },
    {
        name: 'Rafael',
        avatar: '/images/avatar-rafael.jpg',
    },
    {
        name: 'Ana Paula',
        avatar: '/images/avatar-ana.jpg',
    },
    {
        name: 'Carlos',
        avatar: '/images/avatar-carlos.jpg',
    },
    {
        name: 'Juliana',
        avatar: '/images/avatar-juliana.jpg',
    },
]);

const activeModules = [Navigation, Pagination];
</script>

<template>
    <div class="flex w-full justify-center bg-[#f7f7f7] px-2 py-8">
        <div class="w-full max-w-[1366px]">
            <h2 class="mb-8 w-full text-[24px] font-bold lg:text-[36px]">Depoimentos</h2>
            <Swiper
                :modules="activeModules"
                :slides-per-view="3"
                :space-between="30"
                :navigation="true"
                :loop="true"
                :breakpoints="{
                    0: { slidesPerView: 1, spaceBetween: 30 },
                    640: { slidesPerView: 2, spaceBetween: 30 },
                    768: { slidesPerView: 3, spaceBetween: 30 },
                }"
                class="testimonials-swiper"
            >
                <SwiperSlide v-for="(testimonial, idx) in testimonials" :key="testimonial.name">
                    <div :class="['flex h-full w-full flex-col items-center rounded-2xl p-6', idx % 2 === 0 ? 'bg-[#8ee6c5]' : 'bg-[#c9a875]']">
                        <div class="mb-2 flex items-center gap-3">
                            <img :src="testimonial.avatar" alt="Avatar" class="h-14 w-14 rounded-full border-2 border-white object-cover" />
                            <div class="flex flex-col">
                                <span class="text-lg font-semibold text-black">{{ testimonial.name }}</span>
                                <div class="flex items-center gap-1">
                                    <span v-for="star in 5" :key="star" class="text-base text-yellow-400">★</span>
                                </div>
                            </div>
                        </div>
                        <hr class="my-2 w-4/5 border-t-2 border-gray-700" />
                        <!-- Espaço para depoimento futuro -->
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </div>
</template>

<style scoped>
.testimonials-swiper {
    width: 100%;
    padding-bottom: 40px;
    padding: 0 3rem !important;
}
.swiper-slide {
    display: flex;
    justify-content: center;
    align-items: stretch;
    height: 220px;
}
</style>
