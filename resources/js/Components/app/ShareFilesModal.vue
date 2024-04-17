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

const emailInput = ref(null);
const props = defineProps({
    modelValue: Boolean,
    allSelected: Boolean,
    selectedIds: Array
});
const emit = defineEmits(["update:modelValue"]);

const form = useForm({
    all: false,
    email: [],
    ids: [],
    parent_id: null
});

const page = usePage();

function onShow() {
    nextTick(() => emailInput.value.focus()); // modal 내 show slot으로 전파?
}

function handleSubmit() {
    form.parent_id = page.props.folder.id;
    if(props.allSelected) {
        form.all = true;
        form.ids = [];
    } else {
        form.ids = props.selectedIds
    }
    const email = form.email

    form.post(route("file.share"), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();

            // 성공 알림 표시
            showSuccessNotification(`Selected files will be shared to "${email}" if the emails exists in the system`);
        },
        onError: () => emailInput.value.focus(), //error는 컴포넌트로 표시하고 Input 활성화
    });
}

function closeModal() {
    // modelValue.value = false; // 직접 modelValue의 속성을 변경할 수 없다. emit을 사용해야 함.
    emit("update:modelValue");

    // form 초기화
    form.clearErrors();
    form.reset();
}
</script>

<template>
    <modal :show="props.modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <pre>{{ {allSelected, selectedIds} }}</pre>
            <h2 class="text-lg font-medium text-gray-900">Share Files</h2>

            <div class="mt-6">
                <InputLabel
                    for="shareEmail"
                    value="Enter Email Addresses"
                    class="sr-only"
                />

                <TextInput
                    type="text"
                    ref="emailInput"
                    id="shareEmail"
                    v-model="form.email"
                    class="block w-full mt-1"
                    :class="
                        form.errors.email
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                            : ''
                    "
                    placeholder="Folder Email Addresses"
                    @keyup.enter="handleSubmit"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                <PrimaryButton
                    @click="handleSubmit"
                    :class="{ 'opacity-25': form.processing }"
                    :disable="form.processing"
                    >Submit
                </PrimaryButton>
            </div>
        </div>
    </modal>
</template>

<style lang="scss" scoped></style>
