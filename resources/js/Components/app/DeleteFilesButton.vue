<script setup>
import { TrashIcon } from "@heroicons/vue/20/solid";
import ConfirmationDialog from "./ConfirmationDialog.vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { showErrorDialog, showSuccessNotification } from "@/event-bus";

const page = usePage();
// DestryFilesRequest의 rules를 통과할 수 있도록 키를 일치 시킴.
const deleteFilesForm = useForm({
    all: null,
    ids: [],
    parent_id: null // ParentIdBaseRequest
});

const props = defineProps({
    deleteAll: {
        type: Boolean,
        required: false,
        default: false
    },
    deleteIds: {
        type: Array,
        required: false,
    }
})

const emit = defineEmits(['delete']);

const showDeleteDialog = ref(false);

function handleDeleteClick() {
    if(!props.deleteAll && !props.deleteIds.length) { // 선택한 파일이 없을 때
        showErrorDialog('Please select at least one file to delete.');
        return;
    }
    showDeleteDialog.value = true;
}

function handleDeleteCancel() {
    showDeleteDialog.value = false;
}

function handleDeleteConfirm() {
    deleteFilesForm.parent_id = page.props.folder.id;
    if(props.deleteAll) {
        deleteFilesForm.all = true;
    } else {
        deleteFilesForm.all = false;
        deleteFilesForm.ids = props.deleteIds;
    }

    deleteFilesForm.delete(route('file.delete'), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            emit('delete');
            
            showSuccessNotification('Selected files have been deleted.');
        }
    });
    console.log("Delete", props.deleteAll, props.deleteIds);
}
</script>

<template>
    <button @click="handleDeleteClick" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
        <TrashIcon class="w-5 h-5" />
        Delete
    </button>

    <ConfirmationDialog
        :show="showDeleteDialog"
        message="Are you sure you want to delete these files?"
        @cancel="handleDeleteCancel"
        @confirm="handleDeleteConfirm"
    />
</template>

<style scoped>

</style>