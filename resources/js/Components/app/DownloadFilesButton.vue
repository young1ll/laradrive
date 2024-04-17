<script setup>
import { ArrowDownTrayIcon } from "@heroicons/vue/20/solid";
import { usePage, useForm } from "@inertiajs/vue3";
import PrimaryButton from "../PrimaryButton.vue";
import { httpGet } from "@/Helpers/http-helper";
import SharedWithMe from "@/Pages/SharedWithMe.vue";

const page = usePage();
// DestryFilesRequest의 rules를 통과할 수 있도록 키를 일치 시킴.
const form = useForm({
    all: null,
    ids: [],
    parent_id: null // ParentIdBaseRequest
});

const props = defineProps({
    all: {
        type: Boolean,
        required: false,
        default: false
    },
    ids: {
        type: Array,
        required: false,
    },
    sharedWithMe: false,
    sharedByMe: false
})

function handleDownloadClick() {
    if(!props.all && props.ids.length === 0) { // 선택한 자원이 없을 때
        return;
    }

    const p = new URLSearchParams();
    if(page.props.folder?.id) {
        p.append('parent_id', page.props.folder?.id);
    }

    if(props.all) { // 모든 자원을 선택했을 때
        p.append('all', props.all ? true : false); // true | false
    } else { // 특정 자원만 선택했을 때
        for(let id of props.ids) {
            p.append('ids[]', id) // 특정 자원을 선택한 경우에는 true | false 가 아닌 자원의 id 가 필요 
        }
    }

    // web.php route 확인, 자원의 정확한 경로 지정
    let url = route('file.download');
    if(props.sharedWithMe) {
        url = route('file.downloadSharedWithMe');
    } else if(props.sharedByMe) {
        url = route('file.downloadSharedByMe');
    }
    httpGet(url + '?' + p.toString())
        .then(res => {
            // debugger;
            console.log(res);
            if(!res.url) return;

            const a = document.createElement('a'); // dom에서 click으로 파일 다운로드 실행
            a.download = res.filename;
            a.href = res.url;
            a.click();
    })
}

</script>

<template>
    <PrimaryButton @click="handleDownloadClick" >
        <ArrowDownTrayIcon />
        Download
    </PrimaryButton>
</template>

<style scoped>

</style>