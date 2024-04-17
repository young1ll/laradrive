<script setup>
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus";
import { MenuItem } from "@headlessui/vue";

function onChange(event) {
    emitter.emit(FILE_UPLOAD_STARTED, event.target.files);
}
</script>

<template>
    <MenuItem v-slot="{ active }">
        <label for="file-upload" class="relative block px-4 py-2 text-sm text-gray-700 cursor-pointer">
            Upload Files
            <input
                id="file-upload"
                type="file"
                @change="onChange"
                class="absolute inset-0 opacity-0 sr-only"
                multiple
            />
        </label>
    </MenuItem>
</template>

<style lang="scss" scoped></style>
