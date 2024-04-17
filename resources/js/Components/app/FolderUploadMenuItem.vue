<script setup>
import { MenuItem } from "@headlessui/vue";
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus"

function onChange(event) {
    emitter.emit(FILE_UPLOAD_STARTED, event.target.files);
}
</script>

<template>
    <MenuItem v-slot="{ active }">
        <label class="relative block px-4 py-2 text-sm text-gray-700 cursor-pointer">
            Upload Folder
            <input
                type="file"
                class="absolute inset-0 opacity-0 sr-only"
                @change="onChange"
                multiple
                directory
                webkitdirectory
            />
        </label>
    </MenuItem>
</template>

<style lang="scss" scoped></style>
