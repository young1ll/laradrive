<script setup>
import { ShareIcon } from "@heroicons/vue/20/solid";
import ShareFilesModal from "./ShareFilesModal.vue";

import { usePage, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { showErrorDialog, showSuccessNotification } from "@/event-bus";

const page = usePage();
// DestryFilesRequest의 rules를 통과할 수 있도록 키를 일치 시킴.
const form = useForm({
    all: null,
    ids: [],
    parent_id: null // ParentIdBaseRequest
});

const props = defineProps({
    allSelected: {
        type: Boolean,
        required: false,
        default: false
    },
    selectedIds: {
        type: Array,
        required: false,
    }
})

const emit = defineEmits(['restore']);

const showConfirmationDialog = ref(false);

function handleClick() {
    if(!props.allSelected && !props.selectedIds.length) { // 선택한 파일이 없을 때
        showErrorDialog('Please select at least one file to share.');
        return;
    }
    showConfirmationDialog.value = true;
}

</script>

<template>
    <button @click="handleClick" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
        <ShareIcon class="w-5 h-5" />
        Share
    </button>

    <ShareFilesModal v-model="showConfirmationDialog" :allSelected="allSelected" :selectedIds="selectedIds" />
</template>

<style scoped>

</style>