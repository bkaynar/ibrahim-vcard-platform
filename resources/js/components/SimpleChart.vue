<template>
    <div class="chart-container w-full overflow-x-auto">
        <div class="mb-4 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Toplam: {{ totalVisits }}
            </div>
        </div>

        <div v-if="chartData.length === 0" class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-500 mb-2">
                <slot name="empty-icon">
                    <div
                        class="w-12 h-12 mx-auto mb-3 opacity-50 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <span class="text-gray-400">📊</span>
                    </div>
                </slot>
            </div>
            <p class="text-gray-500 dark:text-gray-400">{{ emptyMessage || 'Veri bulunamadı' }}</p>
        </div>

        <div v-else class="relative w-full min-w-[600px]">
            <!-- Y Axis Labels -->
            <div
                class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-500 dark:text-gray-400 pr-3 w-12 text-right">
                <span>{{ maxValue }}</span>
                <span>{{ Math.round(maxValue * 0.75) }}</span>
                <span>{{ Math.round(maxValue * 0.5) }}</span>
                <span>{{ Math.round(maxValue * 0.25) }}</span>
                <span>0</span>
            </div>

            <!-- Chart Area -->
            <div class="ml-12 w-full">
                <svg :width="chartWidth" :height="chartHeight + 32" viewBox="0 0 600 232"
                    preserveAspectRatio="xMidYMid meet"
                    class="border-l border-b border-gray-200 dark:border-gray-600 w-full min-w-[600px]">
                    <!-- Grid Lines -->
                    <g v-for="i in 5" :key="`grid-${i}`">
                        <line :x1="0" :y1="(chartHeight / 4) * (i - 1)" :x2="chartWidth"
                            :y2="(chartHeight / 4) * (i - 1)" stroke="currentColor" stroke-width="0.5"
                            class="text-gray-200 dark:text-gray-700" />
                    </g>

                    <!-- Bars -->
                    <g v-for="(point, index) in chartData" :key="`bar-${index}`">
                        <rect :x="(chartWidth / chartData.length) * index + (chartWidth / chartData.length) * 0.15"
                            :y="chartHeight - (point.value / maxValue) * chartHeight"
                            :width="(chartWidth / chartData.length) * 0.7"
                            :height="(point.value / maxValue) * chartHeight" :fill="barColor" :class="barClass" rx="2"
                            @mouseover="showTooltip(point, $event)" @mouseout="hideTooltip"
                            class="transition-all duration-200 hover:opacity-80 cursor-pointer" />
                    </g>

                    <!-- Line Chart (Optional) -->
                    <g v-if="showLine">
                        <path :d="linePath" fill="none" :stroke="lineColor" stroke-width="2"
                            class="transition-all duration-300" />
                        <!-- Points -->
                        <g v-for="(point, index) in chartData" :key="`point-${index}`">
                            <circle
                                :cx="(chartWidth / chartData.length) * index + (chartWidth / chartData.length) * 0.5"
                                :cy="chartHeight - (point.value / maxValue) * chartHeight" :r="3" :fill="lineColor"
                                class="transition-all duration-200 hover:r-5 cursor-pointer"
                                @mouseover="showTooltip(point, $event)" @mouseout="hideTooltip" />
                        </g>
                    </g>

                    <!-- X Axis Labels (SVG) -->
                    <g v-if="props.showXLabels">
                        <text v-for="label in xLabels" :key="`label-${label.index}`"
                            :x="(chartWidth / chartData.length) * label.index + (chartWidth / chartData.length) * 0.5"
                            :y="chartHeight + 18" text-anchor="middle" font-size="12" fill="#94a3b8"
                            :transform="`rotate(-45 ${(chartWidth / chartData.length) * label.index + (chartWidth / chartData.length) * 0.5},${chartHeight + 18})`">
                            {{ formatDate(label.date) }}
                        </text>
                    </g>
                </svg>

                <!-- Tooltip -->
                <div v-if="tooltip.show" :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }"
                    class="absolute bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 px-3 py-2 rounded-lg shadow-lg text-sm pointer-events-none z-10 transform -translate-x-1/2 -translate-y-full">
                    <div class="font-medium">{{ tooltip.date }}</div>
                    <div>{{ tooltip.value }} ziyaret</div>
                    <div
                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-gray-100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive } from 'vue'

interface ChartDataItem {
    date: string;
    count?: number;
    value?: number;
}

interface Props {
    data: ChartDataItem[];
    title?: string;
    emptyMessage?: string;
    chartWidth?: number;
    chartHeight?: number;
    barColor?: string;
    lineColor?: string;
    barClass?: string;
    showLine?: boolean;
    dateFormat?: 'short' | 'long' | 'day-month';
    showXLabels?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    data: () => [],
    title: 'Grafik',
    emptyMessage: 'Veri bulunamadı',
    chartWidth: 600,
    chartHeight: 200,
    barColor: '#3B82F6',
    lineColor: '#10B981',
    barClass: '',
    showLine: false,
    dateFormat: 'short',
    showXLabels: true
})

// Tooltip state
const tooltip = reactive({
    show: false,
    x: 0,
    y: 0,
    date: '',
    value: 0
})

// Computed properties
const chartData = computed(() => {
    return props.data.map(item => ({
        date: item.date,
        value: item.count || item.value || 0
    }))
})

const maxValue = computed(() => {
    if (chartData.value.length === 0) return 1
    const max = Math.max(...chartData.value.map(d => d.value))
    return max === 0 ? 1 : max
})

const totalVisits = computed(() => {
    return chartData.value.reduce((sum, item) => sum + item.value, 0)
})

const linePath = computed(() => {
    if (chartData.value.length === 0) return ''

    const points = chartData.value.map((point, index) => {
        const x = (props.chartWidth / chartData.value.length) * index + (props.chartWidth / chartData.value.length) * 0.5
        const y = props.chartHeight - (point.value / maxValue.value) * props.chartHeight
        return `${x},${y}`
    })

    return `M ${points.join(' L ')}`
})

// X ekseni için gösterilecek label indexlerini hesaplayan computed property
const xLabelIndexes = computed(() => {
    const arr = [];
    const len = chartData.value.length;
    if (len < 16) {
        for (let i = 0; i < len; i++) arr.push(i);
    } else {
        const step = Math.ceil(len / 8);
        for (let i = 0; i < len; i += step) arr.push(i);
    }
    return arr;
})

// X ekseni için gösterilecek label'ları döndüren computed property
const xLabels = computed(() => {
    return xLabelIndexes.value.map(i => ({
        index: i,
        date: chartData.value[i]?.date
    }));
})

// Methods
const formatDate = (dateStr: string): string => {
    const date = new Date(dateStr)

    switch (props.dateFormat) {
        case 'short':
            return date.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit' })
        case 'long':
            return date.toLocaleDateString('tr-TR', { day: '2-digit', month: 'short' })
        case 'day-month':
            return date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' })
        default:
            return dateStr.slice(-5) // Son 5 karakter (MM-DD)
    }
}

const showTooltip = (point: { date: string; value: number }, event: MouseEvent) => {
    tooltip.show = true
    tooltip.x = (event as any).layerX || event.offsetX
    tooltip.y = (event as any).layerY || event.offsetY
    tooltip.date = formatDate(point.date)
    tooltip.value = point.value
}

const hideTooltip = () => {
    tooltip.show = false
}
</script>

<style scoped>
.chart-container {
    width: 100%;
}
</style>
