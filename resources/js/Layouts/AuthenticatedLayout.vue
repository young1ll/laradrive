<script setup>
import Navigation from "@/Components/app/Navigation.vue";
import SearchForm from "@/Components/app/SearchForm.vue";
import UserSettingDropdown from "@/Components/app/UserSettingDropdown.vue";
import FormProgress from "@/Components/app/FormProgress.vue";
import ErrorDialog from "@/Components/ErrorDialog.vue";
import { FILE_UPLOAD_STARTED, emitter, showErrorDialog, showSuccessNotification } from "@/event-bus";
import { ref, onMounted } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Notification from "@/Components/Notification.vue";

const page = usePage();
const fileUploadForm = useForm({
    files: [],
    relative_paths: [],
    parent_id: null
});

const dragOver = ref(false);

function handleDragOver () {
    dragOver.value = true;
}

function handleDragLeave () {
    dragOver.value = false;
}

function handleDrop (event) {
    dragOver.value = false;
    const files = event.dataTransfer.files;

    if(!files.length) {
        return;
    }

    uploadFiles(files);
}

function uploadFiles(files) {
    console.log(files);

    fileUploadForm.parent_id = page.props.folder.id
    fileUploadForm.files = files
    fileUploadForm.relative_paths = [...files].map(f => f.webkitRelativePath);

    fileUploadForm.post(route('file.store'), {
        onSuccess: () => {
            showSuccessNotification('${files.length} files have been uploaded.');
        },
        onError: (errors) => {
          let message = '';
          if(Object.keys(errors).length > 0) {
            message = errors(Object.keys.errors)[0];
          } else {
            message = 'Error during file upload. Please try again later.'
          }

          showErrorDialog(message);
        },
        onFinish: () => {
            fileUploadForm.clearErrors();
            fileUploadForm.reset();
        }
    });
}

onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles)
})
</script>

<template>
    <div class="flex w-full h-screen gap-4 bg-gray-100">
        <Navigation />
        
        <main
            @drop.prevent="handleDrop"
            @dragover.prevent="handleDragOver"
            @dragleave.prevent="handleDragLeave"
            class="flex flex-col flex-1 px-4 overflow-hidden"
            :class="dragOver ? 'w-full h-full text-gray-400 border-2 border-dashed border-gray-400 flex items-center justify-center' : ''"
        >
            <template v-if="dragOver" class="py-8 text-sm text-center text-gray-500">
                Drop files here to upload
            </template>

            <template v-else>
                <div class="flex items-center justify-between w-full">
                    <SearchForm />
                    <UserSettingDropdown />
                </div>
                
                <div class="flex flex-col flex-1 overflow-hidden">
                    <slot />
                </div>
            </template>
        </main>
    </div>

    <ErrorDialog />
    <FormProgress :form="fileUploadForm"/>
    <Notification /><!-- 성공, 실패 알림(toast) -->
</template>
