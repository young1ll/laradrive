<script setup>
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    show: Boolean,
    message: String
});
const emit = defineEmits(['cancel', 'confirm']);

</script>

<template>
    <Modal :show="show" max-width="md">
        <div class="p-6">
            <h2 class="mb-2 text-2xl font-semibold">Please Confirm</h2>
            <p>{{ message }}</p>

            <div class="flex justify-end mt-6">
                <SecondaryButton @click="$event => emit('cancel')">Cancel</SecondaryButton>
                <DangerButton class="ml-3" @click="$event => emit('confirm')">Confirm</DangerButton>
            </div>
        </div>
    </Modal>
</template>

<style scoped>

</style>