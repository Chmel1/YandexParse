<script setup>
import { computed } from 'vue';

const props = defineProps({
    averageRating: {
        type: [String, Number],
        default: 0
    },
    total: {
        type: Number,
        default: 0
    }
});


const numericRating = computed(() => parseFloat(props.averageRating) || 0);
</script>

<template>
    <div class="w-80 sticky top-10 h-fit shrink-0">
        <div class="bg-white p-8 rounded-2xl border-2 border-slate-800 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="text-6xl font-black text-slate-900 tracking-tighter">
                    {{ numericRating.toFixed(1) }}
                </span>
                <div class="flex flex-col">
                    <div class="flex text-yellow-400 text-2xl">
                        <span v-for="i in 5" :key="i">
                            {{ i <= Math.round(numericRating) ? '★' : '☆' }}
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 uppercase font-bold tracking-widest">
                        Всего отзывов: {{ total }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>