<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

const props = defineProps<{
  slide: {
    id: number;
    title: string;
    image: string;
    porção: string[];
  };
  selectedPortions: Record<number | string, string>;
}>();

const emit = defineEmits(['selectPortion']);

const handleSelectPortion = (portion: string) => {
  emit('selectPortion', props.slide.id, portion);
  console.log(props.slide)
};

</script>

<template>
    <button
    v-for="(portion, index) in props.slide.porção"
    :key="index"
    @click="handleSelectPortion(portion)"
    :class="{
        'border': true,
        'border-gray-400': props.selectedPortions[props.slide.id] !== portion,
        'border-blue-500 bg-blue-100 text-blue-700': props.selectedPortions[props.slide.id] === portion,
        'rounded-full': true,
        'px-3': true,
        'py-1': true,
        'text-xs': true,
        'cursor-pointer': true
    }"
>
    {{ portion }}
</button>
</template>