<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

// Check if the user is authenticated
const isAuthenticated = ref(!!user);

const originalEmail = ref(user.email);
const confirmingEmailChange = ref(false);

const saveProfile = () => {
    if (form.email !== originalEmail.value) {
        confirmingEmailChange.value = true;
    } else {
        form.patch(route('profile.update', { profile: user.id }));
    }
};

const confirmEmailChange = () => {
    form.patch(route('profile.update', { profile: user.id }));
    confirmingEmailChange.value = false;
};

const closeModal = () => {
    confirmingEmailChange.value = false;
};
</script>




<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="saveProfile" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Your email address is unverified.
                    <Link
                        v-if="isAuthenticated"
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Click here to re-send the verification email.
                    </Link>
                    <span v-else class="text-red-600">
                        Please log in to re-send the verification email.
                    </span>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>

        <!-- Modal for Email Change Confirmation -->
        <Modal :show="confirmingEmailChange" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Confirm Email Change
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    You have changed your email address. Do you want to proceed with this change?
                </p>

                <div class="mt-6 flex justify-end">
    <button
        class="text-gray-700 inline-flex items-center mt-3 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
        @click="closeModal"
    >
        Cancel
    </button>

    <PrimaryButton
        class="ms-3"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="confirmEmailChange"
    >
        Confirm
    </PrimaryButton>
</div>


            </div>
        </Modal>
    </section>
</template>



