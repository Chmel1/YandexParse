<template>
    <div class="flex min-h-screen bg-[#F9FAFB] font-sans text-slate-900">
        <aside class="w-64 bg-white border-r border-gray-100 flex flex-col sticky top-0 h-screen shrink-0">
            <div class="p-6">
                <div class="flex items-center mb-1">
                    <img src="/images/logo.svg" 
                        alt="Daily Grow" 
                        class="w-40 h-auto block">
                </div>
                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider truncate">
                    {{ $page.props.auth.user.name }}
                </p>
            </div>

            <nav class="mt-4 flex-1">
    <div class="px-4 space-y-1">
        <Link href="/reviews" 
              :class="route().current('reviews.index') ? 'bg-gray-50 text-slate-800' : 'text-gray-400 hover:bg-gray-50 transition-colors'"
              class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium group">
            
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
            
            <span>Отзывы</span>
        </Link>
        
        <div class="pl-12 space-y-2 mt-2">
            
            
            <Link href="/settings" 
                  :class="route().current('settings.edit') ? 'text-slate-800 font-bold' : 'text-gray-400'"
                  class="flex items-center gap-2 text-sm hover:text-slate-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.094c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.797.939.143.061.28.13.41.205.384.223.89.252 1.286.04l.797-.427c.483-.258 1.086-.112 1.39.358l.547.947c.304.47.168 1.091-.307 1.383l-.797.502c-.359.227-.512.671-.353 1.063.03.072.056.146.079.22.13.417.52.695.952.695h.89c.55 0 1 .45 1 1v1.094c0 .55-.45 1-1 1h-.89c-.432 0-.822.278-.952.695a3.34 3.34 0 0 1-.079.22c-.159.392-.006.836.353 1.063l.797.502c.475.292.611.913.307 1.383l-.547.947c-.304.47-.907.616-1.39.358l-.797-.427c-.396-.212-.902-.183-1.286.04-.13.075-.267.144-.41.205-.413.175-.727.515-.797.939l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.02-.398-1.11-.94l-.149-.894c-.07-.424-.384-.764-.797-.939-.143-.061-.28-.13-.41-.205-.384-.223-.89-.252-1.286-.04l-.797.427c-.483.258-1.086.112-1.39-.358l-.547-.947c-.304-.47-.168-1.091.307-1.383l.797-.502c.359-.227.512-.671.353-1.063a3.34 3.34 0 0 1-.079-.22c-.13-.417-.52-.695-.952-.695h-.89c-.55 0-1-.45-1-1v-1.094c0-.55.45-1 1-1h.89c.432 0 .822-.278.952-.695.023-.074.049-.148.079-.22.159-.392.006-.836-.353-1.063l-.797-.502c-.475-.292-.611-.913-.307-1.383l.547-.947c.304-.47.907-.616 1.39-.358l.797.427c.396.212.902.183 1.286-.04.13-.075.267-.144.41-.205.413-.175.727-.515.797-.939l.149-.894ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                </svg>
                Настройка
            </Link>
        </div>
    </div>
</nav>

            </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-10 sticky top-0 z-10">
                <h2 class="text-lg font-bold text-slate-800">
                    {{ pageTitle }}
                </h2>
                
                <Link :href="route('logout')" 
                      method="post" 
                      as="button" 
                      class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </Link>
            </header>

            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

// Определяем заголовок в зависимости от текущего маршрута
const pageTitle = computed(() => {
    if (route().current('reviews.index')) return 'Отзывы';
    if (route().current('settings.edit')) return 'Настройка';
    return 'Панель управления';
});
</script>