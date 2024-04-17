<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Checkbox from "@/Components/Checkbox.vue";
import DeleteFilesButton from "@/Components/app/DeleteFilesButton.vue";
import RestoreFilesButton from "@/Components/app/RestoreFilesButton.vue";
import DeleteForeverButton from "@/Components/app/DeleteForeverButton.vue";
import DownloadFilesButton from "@/Components/app/DownloadFilesButton.vue";
import FileIcon from "@/Components/app/FileIcon.vue";
import { ChevronRightIcon, HomeIcon } from '@heroicons/vue/16/solid';
import { ref, onMounted, onUpdated, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import { httpGet } from "@/Helpers/http-helper";

const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object
});

const fileTableHead = [
    {
        id: 'checkbox',
        name: '',
    },
    {
        id: 'name',
        name: "Name",
    },
    {
        id: 'path',
        name: "Path",
    },
];

const loadMoreIntersect = ref(null);
const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next
});
const allSelected = ref(false);
const selected = ref({});

/**
 * { // a
 *   1: true,
 *   2: false
 * } => [ // b
 *   [1, true]
 * ] => [1]
 */
const selectedIds = computed(() => 
    Object.entries(selected.value)
        .filter(a => a[1])
        .map(b => b[0])
    );

function openFolder(file) {
    if (!file.is_folder) {
        return;
    }

    router.visit(route("myFiles", { folder: file.path }));
}

function loadMore () {
    console.log('load more');
    console.log(allFiles.value.next);

    if(allFiles.value.next === null) return;

    httpGet(allFiles.value.next).then(
        res => {
            allFiles.value.data = [...allFiles.value.data, ...res.data];
            allFiles.value.next = res.links.next;
        }
    )

    // 아래 코드를 사용하면 allFiles.value.next의 url로 방문하며,
    // files props의 상태를 확인해 새로운 data로 교환할 수 있습니다.
    // 그러나 url 변경을 피할 수는 없습니다.
    // router.visit(allFiles.value.next, {
    //     only: ['files'],
    //     preserveState: true,
    //     preserveScroll: true,
    //     onSuccess: (page) => {
    //         // allFiles.value.data = [...allFiles.value.data, ...res.data];
    //         allFiles.value.data.push(...page.props.files.data);
    //         allFiles.value.next = page.props.files.links.next;
    //     }
    // })
}

function handleAllSelectChange() {
    allFiles.value.data.forEach((file) => {
        selected.value[file.id] = allSelected.value;
    })
}

function toggleFileSelect(file) {
    selected.value[file.id] = !selected.value[file.id];
    handleSelectCheckboxChange(file);
}

/**
 * allFiles의 길이와 selected.value[file.id]: true의 길이를 비교해 allSelected의 상태를 변경
 * @param File file
 */
function handleSelectCheckboxChange(file) {
    if(!selected.value[file.id]) {
        allSelected.value = false;
    } else {
        let checked = true;

        for(let file of allFiles.value.data) {
            if(!selected.value[file.id]) {
                checked = false;
                break;
            }
        }

        allSelected.value = checked;
    }
}

// 삭제 후 선택 상태 초기화
function resetForm() {
    allSelected.value = false;
    selected.value = {};
}

onUpdated(() => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next
    }
})

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            entry.isIntersecting && loadMore();
        })
    }, {
        rootMargin: "-250px 0px 0px 0px"
    })

    observer.observe(loadMoreIntersect.value)
});
</script>

<template>
    <AuthenticatedLayout>
        <nav class="flex items-center justify-end p-1 mb-3">
            <div class="inline-flex items-center gap-4">
                <DownloadFilesButton :all="allSelected" :ids="selectedIds" :shared-with-me="true" />
            </div>
        </nav>

        <div class="flex-1 overflow-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th
                            v-for="head in fileTableHead"
                            :key="head.name"
                            class="px-6 py-4 text-sm font-medium text-left text-gray-900"
                            :class="{
                                'w-[30px]': head.id === 'checkbox',
                                'w-max-[30px]': head.id === 'checkbox',
                                'pr-0': head.id === 'checkbox',
                                'w-full': head.id !== 'checkbox'
                            }"
                        >
                        <template v-if="head.id === 'checkbox'">
                            <Checkbox @change="handleAllSelectChange" v-model:checked="allSelected" />
                        </template>
                        <template v-else>
                            {{ head.name }}
                        </template>
                        </th>
                    </tr>
                </thead>
    
                <tbody>
                    <!-- TODO: 컴포넌트화 FileTableRow -->
                    <tr
                        v-for="(file, index) in allFiles.data"
                        :key="file.id"
                        @click="toggleFileSelect(file)"
                        @dblclick="openFolder(file)"
                        class="transition duration-300 ease-in-out bg-white border-b cursor-pointer hover:bg-blue-100"
                        :class="(selected[file.id] || allSelected) ? 'bg-blue-50' : 'bg-white'"
                    >
                        <td class="px-6 py-4 pr-0 text-sm font-medium text-gray-900 whitespace-nowrap w-[30px]">
                            <Checkbox @change="handleSelectCheckboxChange(file)" v-model="selected[file.id]" :checked="selected[file.id] || allSelected" />
                        </td>
                        <td
                            class="flex items-center px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                        >
                        {{ index + 1 }}
                        <FileIcon :file="file" />
                            {{ file.name }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                        >
                            {{ file.path }}
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div v-if="!allFiles.data.length" class="py-8 text-sm text-center text-gray-400">
                No files found in this folder.
            </div>
            <div ref="loadMoreIntersect"></div>
        </div>
        
    </AuthenticatedLayout>
</template>

<style lang="scss" scoped></style>
