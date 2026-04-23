<script setup lang="ts">
import { BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Bar } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps<{
    data: Array<{ label: string | number; total: number; tipo?: string }>;
    periodo: string;
}>();

const rootStyle = getComputedStyle(document.documentElement);
const primaryColor = rootStyle.getPropertyValue('--primary').trim() || '#512388';
const secondaryColor = rootStyle.getPropertyValue('--secondary').trim() || '#7769FA';
const themeVersion = ref(0);
let themeObserver: MutationObserver | null = null;

const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

const chartData = computed(() => ({
    labels: props.data.map((item) => {
        if (item.tipo === 'mes' && typeof item.label === 'number') {
            return meses[item.label - 1];
        }
        return item.label;
    }),
    datasets: [
        {
            label: 'Novos Leads',
            backgroundColor: primaryColor,
            borderColor: secondaryColor,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
            maxBarThickness: 40,
            data: props.data.map((item) => item.total),
        }
    ],
}));

const chartOptions = computed(() => {
    themeVersion.value;
    const styles = getComputedStyle(document.documentElement);
    const textColor = styles.getPropertyValue('--card-foreground').trim() || '#f8fafc';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top' as const,
                labels: {
                    color: textColor,
                },
            },
            title: {
                display: true,
                text: 'Novos Leads',
                color: textColor,
                font: {
                    size: 16,
                    weight: 'bold' as const,
                },
            },
        },
        scales: {
            x: {
                grid: {
                    display: false,
                },
                ticks: {
                    color: textColor,
                },
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(148, 163, 184, 0.2)',
                },
                ticks: {
                    color: textColor,
                    stepSize: 1,
                },
            },
        },
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
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
