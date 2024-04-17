<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ShareFilesButton from "@/Components/app/ShareFilesButton.vue";
import DeleteFilesButton from "@/Components/app/DeleteFilesButton.vue";
import DownloadFilesButton from "@/Components/app/DownloadFilesButton.vue";
import FileIcon from "@/Components/app/FileIcon.vue";
import { StarIcon } from '@heroicons/vue/24/outline'
import { StarIcon as CheckedStarIcon, ChevronRightIcon, HomeIcon } from '@heroicons/vue/24/solid'
import { ref, onMounted, onUpdated, computed } from "vue";
import { router, Link, useForm, usePage } from "@inertiajs/vue3";
import { httpGet, httpPost } from "@/Helpers/http-helper";
import { showSuccessNotification } from "@/event-bus";

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
        id: 'star',
        name: '',
    },
    {
        id: 'name',
        name: "Name",
    },
    {
        id: 'owner',
        name: "Owner",
    },
    {
        id: 'updated_at',
        name: "Last Modified",
    },
    {
        id: 'size',
        name: "Size",
    },
];

const page = usePage();

const loadMoreIntersect = ref(null);
const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next
});
const allSelected = ref(false);
const selected = ref({});
const onlyFavorites = ref(false);

let params = null;

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
function handleDelete() {
    allSelected.value = false;
    selected.value = {};
}

function addRemoveFavorite(file) {
    // redirect, refresh 없이 상태 변화를 감지하기 위해 fetch를 사용합니다.
    httpPost(route('file.addToFavorites', file.id), page.props._csrfToken, {id:file.id})
        .then(() => {
            showSuccessNotification("File " + (file.is_favorite ? "unfavorited" : "favorited") + " successfully!");
            file.is_favorite = !file.is_favorite
        }).catch(async (err) => {
            console.log(err.error.message);
        })
}

function showOnlyFavorites() {
    onlyFavorites.value = !onlyFavorites.value // change onlyFavorites value

    if (onlyFavorites.value) {
        params.set('favorites', 1)
    } else {
        params.delete('favorites')
    }
    const favoriteUrl = window.location.pathname+'?'+params.toString()
    console.log(favoriteUrl);
    router.get(favoriteUrl)
}

onUpdated(() => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next
    }
})

onMounted(() => {
    params = new URLSearchParams(window.location.search);
    onlyFavorites.value = params.get("favorites") === "1";

    console.log(onlyFavorites.value);

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
        <nav class="flex items-center justify-between p-1 mb-3">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li v-for="ans of ancestors.data" :key="ans.id" class="inline-flex items-center">
                    <Link v-if="!ans.parent_id" :href="route('myFiles')" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600">
                        <HomeIcon class="w-4 h-4" />
                        My Files
                    </Link>
                    <div v-else class="flex items-center">
                        <ChevronRightIcon class="w-5 h-5" />
                        <Link :href="route('myFiles', { folder: ans.path })" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-600">
                            {{ ans.name }}
                        </Link> 
                    </div>
                </li>
            </ol>

            <div class="inline-flex items-center gap-4">
                <div @click="showOnlyFavorites">
                    <CheckedStarIcon class="w-8 h-8 cursor-pointer" :class="onlyFavorites ? 'text-blue-600' : 'text-gray-400'" />
                </div>
                <ShareFilesButton :all-selected="allSelected" :selected-ids="selectedIds"/>
                <DownloadFilesButton :all="allSelected" :ids="selectedIds" />
                <DeleteFilesButton :delete-all="allSelected" :delete-ids="selectedIds" @delete="handleDelete" />
            </div>
        </nav>

        <div class="flex-1 overflow-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th
                            v-for="(head, index) in fileTableHead"
                            :key="head.name"
                            class="px-6 py-4 text-sm font-medium text-left text-gray-900"
                            :class="{
                                'w-[30px] w-max-[30px] pr-0': index === 0 || index === 1,
                                'w-full': index !== 0 && index !== 1
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
                            <!-- <pre>{{ file.id }}</pre> -->
                            <Checkbox @change="handleSelectCheckboxChange(file)" v-model="selected[file.id]" :checked="selected[file.id] || allSelected" />
                        </td>
                        <td class="px-6 py-4 pr-0 text-sm font-medium text-gray-900 whitespace-nowrap w-[30px]">
                            <div @click.stop.prevent="addRemoveFavorite(file)">
                                <!-- <pre>{{ file.is_favorite }}</pre> -->
                                <StarIcon v-if="!file.is_favorite" class="w-6 h-6 text-blue-400" />
                                <CheckedStarIcon v-else class="w-6 h-6 text-blue-600" />
                            </div>
                        </td>
                        <td
                            class="flex items-center px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"
                        >
                        <!-- {{ index + 1 }} -->
                        <FileIcon :file="file" />
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
            
            <div v-if="!allFiles.data.length" class="py-8 text-sm text-center text-gray-400">
                No files found in this folder.
            </div>
            <div ref="loadMoreIntersect"></div>
        </div>
        
    </AuthenticatedLayout>
</template>

<style lang="scss" scoped></style>
