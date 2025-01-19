<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import { usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";

const form = useForm({
    profile_picture: null,
});

const { props } = usePage();
const user = props.auth.user;

const newProfilePictureUrl = ref(null);
const confirmingPictureDeletion = ref(false); // State for the modal

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        newProfilePictureUrl.value = URL.createObjectURL(file);
        form.profile_picture = file;
    }
};

const updateProfilePicture = () => {
    form.post(route("profile.picture.update"), {
        onSuccess: () => {
            window.location.reload();
        },
    });
};

const openDeletePictureModal = () => {
    confirmingPictureDeletion.value = true;
};

const closeModal = () => {
    confirmingPictureDeletion.value = false;
};

const deleteProfilePicture = () => {
    form.delete(route("profile.picture.delete"), {
        onSuccess: () => {
            closeModal(); // Close modal on success
            window.location.reload();
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Update Profile Picture
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile picture.
            </p>
        </header>

        <div class="flex items-center p-5 md:p-1 md:mt-5 md:gap-3 gap-10">
            <!-- Profile Picture -->
            <div
                class="h-36 w-36 rounded-full overflow-hidden relative cursor-pointer group border-8 border-white shadow-2xl"
                @click="$refs.fileInput.click()"
            >
                <img
                    v-if="newProfilePictureUrl"
                    :src="newProfilePictureUrl"
                    alt="New Profile Picture Preview"
                    class="h-full w-full object-cover"
                    style="object-fit: cover"
                />
                <img
                    v-else-if="user.profile_picture"
                    :src="`/storage/${user.profile_picture}`"
                    alt="Current Profile Picture"
                    class="h-full w-full object-cover"
                    style="object-fit: cover"
                />
                <img
                    v-else
                    src='/storage/icons/general/default_profile.svg'
                    alt="Default Profile Picture"
                    class="h-full w-full object-cover"
                    style="object-fit: cover"
                />
                <input
                    type="file"
                    ref="fileInput"
                    @change="handleFileChange"
                    accept="image/*"
                    class="hidden"
                />
                <button
                    class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                >
                    <svg
                        class="w-8 h-8"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke="white"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 18V8a1 1 0 0 1 1-1h1.5l1.707-1.707A1 1 0 0 1 8.914 5h6.172a1 1 0 0 1 .707.293L17.5 7H19a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z"
                        />
                        <path
                            stroke="white"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                        />
                    </svg>
                </button>
            </div>

            <!-- Buttons on the Right -->
            <div class="flex flex-col items-end ml-4">
                <form @submit.prevent="updateProfilePicture" class="w-full">
                    <div>
                        <InputError
                            :message="form.errors.profile_picture"
                            class="mt-2"
                        />
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <button
                            :disabled="form.processing || !newProfilePictureUrl"
                            class="rounded-lg bg-gray-800 justify-center text-white hover:bg-gray-600 border border-gray-300 inline-flex items-center mt-3 px-5 py-2.5 text-sm font-medium focus:ring-4 focus:outline-none focus:ring-gray-300 disabled:cursor-not-allowed w-24"
                            :class="{
                                'opacity-70':
                                    form.processing || !newProfilePictureUrl,
                            }"
                        >
                            Save
                        </button>

                        <button
                            type="button"
                            class="rounded-lg text-gray-800 justify-center inline-flex items-center mt-3 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium text-sm px-5 py-2.5 w-24"
                            @click="openDeletePictureModal"
                        >
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirmation Modal for Deleting Profile Picture -->
        <Modal :show="confirmingPictureDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete your profile picture?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Once your profile picture is deleted, it will be permanently
                    removed.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <!-- Add gap between buttons -->
                    <button
                        @click="closeModal"
                        class="bg-white border border-gray-300 text-gray-800 font-semibold rounded-lg px-4 py-2 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Cancel
                    </button>
                    <button
                        class="bg-red-600 text-white font-semibold rounded-lg px-4 py-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteProfilePicture"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
