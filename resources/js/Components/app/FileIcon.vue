<script setup>
import { isImage, isPDF, isAudio, isVideo, isWord, isExcel, isZip, isText } from '@/Helpers/file-helper';

const {file} = defineProps({
    file: Object
})

</script>

<template>
    <span class="inline-flex items-center justify-center w-8 h-8 mr-2">
        <div v-if="file.is_folder">
            <img :src="'/images/icons/folder.png'" alt="" />
        </div>
        <template v-else>
            <img v-if="isImage(file)" :src="'/images/icons/photo.png'" alt="" />
            <img v-else-if="isPDF(file)" :src="'/images/icons/pdf.png'" alt="" />
            <img v-else-if="isAudio(file)" :src="'/images/icons/audio.png'" alt="" />
            <img v-else-if="isVideo(file)" :src="'/images/icons/video.png'" alt="" />
            <img v-else-if="isWord(file)" :src="'/images/icons/word.png'" alt="" />
            <img v-else-if="isExcel(file)" :src="'/images/icons/excel.png'" alt="" />
            <img v-else-if="isZip(file)" :src="'/images/icons/zip.png'" alt="" />
            <img v-else-if="isText(file)" :src="'/images/icons/text.png'" alt="" />
            <img v-else :src="'/images/icons/attach-file.png'" alt="" />
        </template>
    </span>
</template>