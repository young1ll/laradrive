<script setup>

const props = defineProps({
    form: Object
})
</script>

<template>
    <div v-if="form.progress" class="absolute border border-gray-900 right-8 bottom-4 bg-white shadow-lg rounded flex flex-col w-[240px] p-2">
        <div class="mb-2 text-xs">Uploading {{ form.files.length }} files</div>
        <div class="h-3 bg-gray-100 rounded-md">
            <div class="h-full transition-all bg-gray-900 rounded-md" :style="{width: form.progress.percentage + '%'}">
            </div>
        </div>
    </div>
</template>