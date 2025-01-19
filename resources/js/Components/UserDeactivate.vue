<script setup>
import { ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "./DangerButton.vue";
import TextInput from "./TextInput.vue";
import InputError from "./InputError.vue";
import SecondaryButton from "./SecondaryButton.vue";

const props = defineProps({
    deac_user: Object,
});

const emit4 = defineEmits();

const form = useForm({
    password: "",
});

onMounted(() => {
    initFlowbite();
});

const passwordInput = ref(null);

// Reset form function
function resetForm() {
    emit4('close-modal');
    form.reset();
}

// Submit form function
const deleteUser = () => {
    form.put(route("users.status.update", props.deac_user.id), {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
            window.location.reload();
        },

        onError: () => passwordInput.value.focus(),
        onFinish: () => resetForm(),
    });
};
</script>

<template>
    <div class="relative p-4 max-w-screen-sm max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Are you sure you want to deactivate this account?
                </h3>
                <button
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="user-crud-modal3"
                    @click="resetForm()"
                >
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->

            <form @submit.prevent="submit">
                <div class="px-10 py-5">
                    <p class="mt-1 text-sm text-gray-600">
                        Deactivating this account will disable the user's access and
                        restrict all associated data. Please enter your password to
                        confirm you would like to permanently delete your account.
                    </p>

                    <div class="mt-6">
                        <InputLabel
                            for="password"
                            value="Password"
                            class="sr-only"
                        />

                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="resetForm()">
                            Cancel
                        </SecondaryButton>

                        <DangerButton
                            class="ms-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="deleteUser"
                        >
                            Deactivate Account
                        </DangerButton>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
