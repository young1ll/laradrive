<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { onMounted } from "vue";

const { files } = defineProps({
    files: Object,
});

const fileTableHead = [
    {
        name: "Name",
    },
    {
        name: "Owner",
    },
    {
        name: "Last Modified",
    },
    {
        name: "Size",
    },
];

onMounted(() => {
    console.log(files.data);
});
</script>

<template>
    <AuthenticatedLayout>
        <table class="min-w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th
                        v-for="head in fileTableHead"
                        :key="head.name"
                        class="px-6 py-4 text-sm font-medium text-left text-gray-900"
                    >
                        {{ head.name }}
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="file in files.data"
                    :key="file.id"
                    class="transition duration-300 ease-in-out bg-white border-b hover:bg-gray-100"
                >
                    <td
                        class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                    >
                        {{ file.name }}
                    </td>
                    <td
                        class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                    >
                        {{ file.owner }}
                    </td>
                    <td
                        class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                    >
                        {{ file.updated_at }}
                    </td>
                    <td
                        class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                    >
                        {{ file.size }}
                    </td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>

<style lang="scss" scoped></style>
