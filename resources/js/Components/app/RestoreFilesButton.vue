<script setup>
import { ArrowLeftStartOnRectangleIcon } from "@heroicons/vue/20/solid";
import ConfirmationDialog from "./ConfirmationDialog.vue";
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

function handleRestoreClick() {
    if(!props.allSelected && !props.selectedIds.length) { // 선택한 파일이 없을 때
        showErrorDialog('Please select at least one file to restore.');
        return;
    }
    showConfirmationDialog.value = true;
}

function handleCancel() {
    showConfirmationDialog.value = false;
}

function handleConfirm() {
    if(props.allSelected) {
        form.all = true;
    } else {
        form.all = false;
        form.ids = props.selectedIds;
    }

    form.post(route('file.restore'), {
        onSuccess: () => {
            showConfirmationDialog.value = false;
            emit('restore');
            
            showSuccessNotification('Selected files have been restored.');
        }
    });
}
</script>

<template>
    <button @click="handleRestoreClick" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
        <ArrowLeftStartOnRectangleIcon class="w-5 h-5" />
        Restore
    </button>

    <ConfirmationDialog
        :show="showConfirmationDialog"
        message="Are you sure you want to restore these files?"
        @cancel="handleCancel"
        @confirm="handleConfirm"
    />
</template>

<style scoped>

</style>