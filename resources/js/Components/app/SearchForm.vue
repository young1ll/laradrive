<script setup>
import { onMounted, ref } from "vue";
import TextInput from "@/Components/TextInput.vue";

let params = "";
let search = ref("");

onMounted(() => {
    params = new URLSearchParams(window.location.search);
    search.value = params.get("search") ?? "";
});
</script>

<template>
    <form class="w-[600px] h-[80px] flex items-center">
        <TextInput
            type="text"
            class="block w-full mr-2"
            v-model="search"
            autocomplete
            placeholder="Search for files and folders"
        />
    </form>
</template>
