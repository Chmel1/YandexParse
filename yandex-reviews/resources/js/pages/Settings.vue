<template>
    <AppLayout>
        <transition name="notification">
            <div v-if="showNotification" 
                 class="fixed bottom-8 right-8 z-[100] flex items-center gap-4 bg-slate-800 text-white p-2 pl-5 rounded-2xl shadow-2xl border border-slate-700 max-w-md">
                <div class="flex flex-col py-2">
                    <span class="font-bold text-[15px]">🎉 Отзывы загружены!</span>
                    <span class="text-xs text-slate-400">Парсер успешно собрал данные</span>
                </div>
                <button @click="goToReviews" 
                        class="px-5 py-3 bg-[#F33F31] hover:bg-[#d93529] text-white rounded-xl font-bold text-sm transition-colors">
                    Смотреть
                </button>
                <button @click="showNotification = false" class="pr-3 text-slate-500 hover:text-white transition-colors text-xl">
                    &times;
                </button>
            </div>
        </transition>

        <main class="flex-1 p-10">
            <div class="max-w-3xl">
                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                    <div v-if="isParsing" class="absolute top-0 left-0 h-1 bg-red-500 animate-pulse w-full"></div>

                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-[#FEEBEA] rounded-2xl flex items-center justify-center shrink-0 shadow-sm border border-red-100">
                            <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 2C10.477 2 6 6.477 6 12C6 19.5 16 30 16 30C16 30 26 19.5 26 12C26 6.477 21.523 2 16 2Z" fill="#F33F31"/>
                                <circle cx="16" cy="12" r="5" fill="white"/>
                                <circle cx="16" cy="12" r="3" fill="#F33F31"/>
                            </svg>
                        </div>
                        
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Подключить Яндекс.Карты</h2>
                            <p class="text-sm text-gray-400">Настройте источник для сбора отзывов</p>
                        </div>
                    </div>

                    <p class="text-[15px] text-slate-600 mb-6 leading-relaxed">
                        Укажите прямую ссылку на раздел с отзывами вашей организации. Например: <br>
                        <span class="text-red-500 font-medium bg-red-50 px-2 py-0.5 rounded mt-1 inline-block italic text-sm">
                            https://yandex.ru/maps/org/zotto/1332514733/reviews/
                        </span>
                    </p>

                    <form @submit.prevent="saveSettings" class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">
                                URL адрес организации
                            </label>
                            <input 
                                v-model="form.url" 
                                type="text" 
                                placeholder="https://yandex.ru/..." 
                                class="w-full px-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-slate-800 transition-all text-slate-700 placeholder:text-gray-300"
                                :class="{ 'ring-2 ring-red-500': form.errors.url }"
                            />
                            <p v-if="form.errors.url" class="text-red-500 text-xs mt-2 ml-1 font-medium">
                                {{ form.errors.url }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                            <div class="flex flex-col">
                                <p class="text-xs text-gray-400 max-w-[300px]">
                                    После сохранения парсер начнет обновлять данные.
                                </p>
                                <span v-if="isParsing" class="text-[10px] text-red-500 font-bold uppercase tracking-tighter animate-pulse">
                                    ● Сейчас идет поиск новых отзывов
                                </span>
                            </div>
                            
                            <button 
                                type="submit" 
                                class="px-8 py-4 bg-slate-800 text-white rounded-2xl font-bold hover:bg-slate-900 transition-all shadow-lg shadow-slate-200 disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Сохранение...' : 'Сохранить изменения' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<script setup>
import { ref, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    currentUrl: String,
    reviews_count: Number 
});

const form = useForm({
    url: props.currentUrl || '',
});

const isParsing = ref(false);
const showNotification = ref(false);
let pollInterval = null;

const startPolling = () => {
    isParsing.value = true;
    if (pollInterval) clearInterval(pollInterval);

    pollInterval = setInterval(() => {
        
        router.reload({
            only: ['reviews_count'],
            onSuccess: (page) => {
                
                if (page.props.reviews_count > 0) {
                    isParsing.value = false;
                    showNotification.value = true;
                    clearInterval(pollInterval);
                }
            }
        });
    }, 5000); 
};

const saveSettings = () => {
    showNotification.value = false;
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Настройки сохранены, запускаем опрос...');
            startPolling();
        },
    });
};

const goToReviews = () => {
    showNotification.value = false;
    router.visit(route('reviews.index'));
};

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.notification-enter-from {
  transform: translateY(100px) scale(0.5);
  opacity: 0;
}

.notification-leave-to {
  transform: translateX(100px);
  opacity: 0;
}
</style>