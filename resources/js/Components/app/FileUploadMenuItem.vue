<script setup>
import { MenuItem } from "@headlessui/vue";
</script>

<template>
    <MenuItem v-slot="{ active }">
        <a href="#" class="relative block px-4 py-2 text-sm text-gray-700">
            Upload Files
            <input
                type="file"
                class="absolute top-0 bottom-0 left-0 right-0 opacity-0 cursor-pointer"
                multiple
            />
        </a>
    </MenuItem>
</template>

<style lang="scss" scoped></style>
