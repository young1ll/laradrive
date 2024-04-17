<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { SHOW_ERROR_DIALOG, emitter } from '@/event-bus';
import { onMounted, ref } from 'vue';

const show = ref(false);
const message = ref('');

const emit = defineEmits(['close']);

function close() {
    show.value = false;
    message.value = '';
}

onMounted(() => {
    emitter.on(SHOW_ERROR_DIALOG, ({message:msg})=> {
        show.value = true;
        message.value = msg;
    })
})
</script>

<template>
    <Modal :show="show" max-width="md">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900">Error</h2>
            <p>{{ message }}</p>
            <div class="flex items-center justify-end">
                <PrimaryButton @click="close">OK</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>