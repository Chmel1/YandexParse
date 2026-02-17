<template>
    <AppLayout>
        <main class="flex-1 p-10 flex gap-8">
            <div class="flex-1">
                <div class="mb-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-full border border-gray-200 shadow-sm">
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Яндекс Карты</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div v-for="review in reviews.data" :key="review.id" 
                         class="p-7 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-[11px] text-gray-400 font-bold">
                                    <span>{{ formatDate(review.published_at) }}</span>
                                    <span class="flex items-center gap-1 text-red-500">
                                        📍 {{ review.branch_name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-slate-900 text-lg">{{ review.author_name }}</h4>
                                    <span class="text-[12px] text-gray-300 font-mono">{{ review.author_phone || '+7 900 540 40 40' }}</span>
                                </div>
                            </div>
                            <div class="flex text-yellow-400 text-sm gap-0.5">
                                <span v-for="i in 5" :key="i">{{ i <= review.rating ? '★' : '☆' }}</span>
                            </div>
                        </div>
                        <p class="text-[15px] text-slate-600 leading-relaxed font-medium">{{ review.text }}</p>
                    </div>
                </div>

                <div class="mt-12 flex justify-center items-center gap-2">
                    <Link v-for="(link, k) in reviews.links" :key="k"
                          :href="link.url || '#'"
                          v-html="link.label"
                          class="px-4 py-2 rounded-xl border text-sm font-bold transition-all"
                          :class="{ 
                              'bg-slate-800 text-white border-slate-800 shadow-md': link.active,
                              'bg-white text-gray-500 border-gray-100 hover:bg-gray-50': !link.active && link.url,
                              'text-gray-300 border-transparent cursor-not-allowed opacity-50': !link.url
                          }"
                          preserve-scroll />
                </div>
            </div>

<RatingCard :averageRating="averageRating" :total="totalYandexReviews" />
        </main>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RatingCard from '@/Components/RatingCard.vue';


const props = defineProps({
    reviews: Object,
    averageRating: [String, Number],
    totalYandexReviews: Number 
});

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleString('ru-RU', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};
</script>
