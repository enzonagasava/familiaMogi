<script setup lang="ts">
import { ArcElement, Chart as ChartJS, Legend, Title, Tooltip } from 'chart.js';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Doughnut } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps<{
    data: Array<{ nome: string; quantidade: number }>;
    periodo: string;
}>();

const rootStyle = getComputedStyle(document.documentElement);
const primaryColor = rootStyle.getPropertyValue('--primary').trim() || '#512388';
const secondaryColor = rootStyle.getPropertyValue('--secondary').trim() || '#7769FA';
const accentColor = rootStyle.getPropertyValue('--accent').trim() || '#F97316';
const chart4Color = rootStyle.getPropertyValue('--color-chart-4').trim() || '#22C55E';
const chart5Color = rootStyle.getPropertyValue('--color-chart-5').trim() || '#06B6D4';
const palette = [primaryColor, secondaryColor, accentColor, chart4Color, chart5Color];
const themeVersion = ref(0);
let themeObserver: MutationObserver | null = null;

const chartData = computed(() => ({
    labels: props.data.map((item) => item.nome),
    datasets: [
        {
            label: 'Quantidade Vendida',
            backgroundColor: props.data.map((_, index) => palette[index % palette.length]),
            borderColor: rootStyle.getPropertyValue('--background').trim() || '#FFFFFF',
            borderWidth: 2,
            data: props.data.map((item) => item.quantidade),
        },
    ],
}));

const chartOptions = computed(() => {
    themeVersion.value;
    const styles = getComputedStyle(document.documentElement);
    const textColor = styles.getPropertyValue('--card-foreground').trim() || '#f8fafc';
    const tooltipBg = styles.getPropertyValue('--card').trim() || '#111827';
    const tooltipText = styles.getPropertyValue('--card-foreground').trim() || '#f8fafc';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom' as const,
                labels: {
                    color: textColor,
                },
            },
            title: {
                display: true,
                text: 'Produtos Vendidos',
                color: textColor,
                font: {
                    size: 16,
                    weight: 'bold' as const,
                },
            },
            tooltip: {
                backgroundColor: tooltipBg,
                titleColor: tooltipText,
                bodyColor: tooltipText,
                callbacks: {
                    label: function (context: any) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        return `${label}: ${value} unidades`;
                    },
                },
            },
        },
        cutout: '55%',
    };
});

onMounted(() => {
    themeObserver = new MutationObserver(() => {
        themeVersion.value += 1;
    });
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

onBeforeUnmount(() => {
    themeObserver?.disconnect();
});
</script>

<template>
    <div class="h-full w-full rounded-lg  p-4 shadow">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>
