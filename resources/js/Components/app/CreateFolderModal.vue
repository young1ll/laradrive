<script setup>
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";
import { showSuccessNotification } from "@/event-bus";

const folderNameInput = ref(null);
const { modelValue } = defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(["update:modelValue"]);

const form = useForm({
    name: "",
    parent_id: null,
});

const page = usePage();

function onShow() {
    nextTick(() => folderNameInput.value.focus()); // modal 내 show slot으로 전파?
}

function createFolder() {
    form.parent_id = page.props.folder.id;
    const name = form.name;

    form.post(route("folder.create"), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();

            // 성공 알림 표시
            showSuccessNotification(`The folder "${name}" has been created.`);
            form.reset();
        },
        onError: () => folderNameInput.value.focus(), //error는 컴포넌트로 표시
    });
}

function closeModal() {
    // modelValue.value = false; // 직접 modelValue의 속성을 변경할 수 없다. emit을 사용해야 함.
    emit("update:modelValue", false);

    // form 초기화
    form.clearErrors();
    form.reset();
}
</script>

<template>
    <modal :show="modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Create New Folder</h2>

            <div class="mt-6">
                <InputLabel
                    for="folderName"
                    value="Folder Name"
                    class="sr-only"
                />

                <TextInput
                    type="text"
                    id="folderName"
                    ref="folderNameInput"
                    v-model="form.name"
                    class="block w-full mt-1"
                    :class="
                        form.errors.name
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                            : ''
                    "
                    placeholder="Folder Name"
                    @keyup.enter="createFolder"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                <PrimaryButton
                    @click="createFolder"
                    :class="{ 'opacity-25': form.processing }"
                    :disable="form.processing"
                    >Submit</PrimaryButton
                >
            </div>
        </div>
    </modal>
</template>

<style lang="scss" scoped></style>
, usePage, usePage
