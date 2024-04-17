<script setup>
import { SHOW_NOTIFICATION, emitter } from '@/event-bus';
import { onMounted, ref } from 'vue'

const show = ref(false);
const type = ref('success');
const message = ref('');

function close() {
    show.value = false;
    type.value = '';
    message.value = '';
}

onMounted(() => {
    let timeout; // notification toast timeout

    emitter.on(SHOW_NOTIFICATION, ({type: t, message: m}) => {
        show.value = true;
        type.value = t;
        message.value = m;

        // 지연 종료
        if(timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            close()
        }, 5000)
    })
})
</script>

<template>
    <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
    >
        <div v-if="show"
            class="fixed bottom-4 left-4 text-white py-2 px-4 rounded-lg shadow-md w-[200px]"
            :class="{
                'bg-emerald-500': type === 'success',
                'bg-red-500': type === 'error'
            }">
            {{ message }}
        </div>
    </transition>
</template>

<style scoped>

</style>