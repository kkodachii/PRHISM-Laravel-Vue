<script setup>
import { ref, watch, onMounted, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
} from "@headlessui/vue";
import LoadingModal from "@/Components/LoadingModal.vue";

// Define props for barangayNames and rhuBarangaysMap
const props = defineProps({
    barangayNames: {
        type: Array,
        default: () => [],
    },
    rhuBarangaysMap: {
        type: Object,
        default: () => ({}),
    },
    edit_user: Object,
});

const emit2 = defineEmits();

// Form references and flags
const barangayNames = ref(props.barangayNames);
const selectedBarangay = ref(null);
const query = ref("");
const isSuperAdmin = ref(false);
const isRhuHidden = ref(false);
const isAdmin = ref(false);
const isLoading = ref(false);

// Initialize form with user data and default values
const form = useForm({
    name: props.edit_user?.name ?? "",
    email: props.edit_user?.email ?? "",
    role_id: props.edit_user?.role_id ?? "",
    rhu_id: props.edit_user?.rhu_id ?? 1,
    barangay_id: props.edit_user?.barangay_id ?? 1,
    profile_picture: null,
    preview: props.edit_user?.profile_picture
        ? `/storage/${props.edit_user.profile_picture}`
        : "",
});

// Watch for changes in the selected RHU and update barangay names accordingly
watch(
    () => form.rhu_id,
    (newValue) => {
        if (newValue in props.rhuBarangaysMap) {
            barangayNames.value = props.rhuBarangaysMap[newValue];
            selectedBarangay.value = null;
            form.barangay_id = "";
        } else {
            barangayNames.value = props.barangayNames;
        }

        if (isAdmin.value) {
            form.barangay_id = newValue;
        }
    }
);

// Watch for changes in the selected barangay
watch(selectedBarangay, (newValue) => {
    form.barangay_id = newValue ? newValue.id : "";
});

// Watch for changes in role to update visibility and behavior
watch(
    () => form.role_id,
    (newValue) => {
        selectedBarangay.value = null; // Reset selected barangay when role changes

        if (newValue === "3") {
            isSuperAdmin.value = true;
            isRhuHidden.value = true;
            isAdmin.value = false;
            form.rhu_id = "";
            form.barangay_id = "";
        } else if (newValue === "2") {
            isAdmin.value = true;
            isSuperAdmin.value = false;
            isRhuHidden.value = false;
            form.rhu_id = "";
            form.barangay_id = "";
        } else {
            isSuperAdmin.value = false;
            isAdmin.value = false;
            isRhuHidden.value = false;

            form.rhu_id = "";
            form.barangay_id = "";
        }
    }
);

// Filter barangays based on search query
const filteredBarangayNames = computed(() => {
    return query.value === ""
        ? barangayNames.value
        : barangayNames.value.filter((barangay) =>
              barangay.barangay_name
                  .toLowerCase()
                  .includes(query.value.toLowerCase())
          );
});

watch(
    () => props.edit_user,
    (newUser) => {
        if (newUser) {
            form.name = newUser.name;
            form.email = newUser.email;
            form.role_id = newUser.role_id;
            form.rhu_id = newUser.rhu_id;
            form.barangay_id = newUser.barangay_id;
            form.preview = newUser.profile_picture
                ? `/storage/${newUser.profile_picture}`
                : "";
        }
    },
    { immediate: true }
);

// Reset form to original values
function resetForm() {
    emit2('close-modal');
    form.reset();
}

// File change handler for profile picture
function change(event) {
    const file = event.target.files[0];
    if (file) {
        form.profile_picture = file;
        form.preview = URL.createObjectURL(file);
    }
}

// Submit form with form data
function submit() {
    isLoading.value = true;

    form.transform((data) => {
        const formData = new FormData();
        formData.append("_method", "put");
        formData.append("name", data.name);
        formData.append("email", data.email);
        formData.append("role_id", data.role_id);

        // Explicitly check and append barangay_id to formData
        if (data.barangay_id) {
            formData.append("barangay_id", data.barangay_id);
        } else {
            console.warn(
                "Barangay ID is missing or invalid:",
                data.barangay_id
            );
        }

        formData.append("rhu_id", data.rhu_id);

        if (data.profile_picture) {
            formData.append("profile_picture", data.profile_picture);
        }

        return formData;
    });

    form.post(route("users.update", props.edit_user.id), {
        forceFormData: true,
        onSuccess: () => {
            isLoading.value = false;
            resetForm();
        },
        onError: (errors) => {
            isLoading.value = false;
            resetForm();
        },
    });
}

onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <LoadingModal :isLoading="isLoading" />
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div
                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Edit
                </h3>
                <button
                    type="button"
                    @click="resetForm()"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                >
                    <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <!--Change Profile Method-->
            <form @submit.prevent="submit" class="p-4 md:p-5">
                <!-- Label -->

                <div class="col-span-1 sm:col-span-1">
                    <label for="profile_picture">Upload Avatar</label>
                </div>

                <div class="flex items-center space-x-6">
                    <!-- Avatar Image -->
                    <div
                        class="relative w-24 h-24 rounded-full overflow-hidden border border-slate-300"
                    >
                        <img
                            class="object-cover w-full h-full"
                            :src="
                                form.preview
                                    ? form.preview
                                    : '/storage/icons/general/default_profile.svg'
                            "
                            alt="User Avatar"
                        />
                    </div>

                    <!-- File Input -->
                    <div class="flex flex-col space-y-2 mt-4">
                        <input
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            type="file"
                            id="profile_picture"
                            @input="change"
                        />
                        <p
                            v-if="form.errors.profile_picture"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.profile_picture }}
                        </p>
                        <p class="text-gray-500 text-xs">
                            SVG, PNG, JPG, or GIF (MAX. 800x400px).
                        </p>
                    </div>
                </div>

                <!-- Error Message for form submission -->
                <p
                    v-if="form.errors.profile_picture"
                    class="text-red-600 text-sm mt-2 ml-5"
                >
                    {{ form.errors.profile_picture }}
                </p>
            </form>

            <!--Change Profile Method-->
            <form @submit.prevent="submit" class="p-4 md:p-5">
                <div class="grid gap-4 mb-3 grid-cols-2">
                    <div class="col-span-1">
                        <InputLabel for="name" value="Name" />
                        <input
                            v-model="form.name"
                            type="text"
                            name="name"
                            id="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Type user name"
                            required
                        />
                    </div>
                    <div class="col-span-1">
                        <InputLabel for="email" value="Email" />
                        <input
                            v-model="form.email"
                            type="email"
                            name="email"
                            id="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Type email address"
                            required
                        />
                    </div>
                </div>
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-1">
                        <InputLabel for="role_id" value="Role" />
                        <select
                            required
                            v-model="form.role_id"
                            id="role_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="" disabled>Select role</option>
                            <option value="1">Midwife</option>
                            <option value="2">Admin</option>
                            <option value="3">SuperAdmin</option>
                        </select>
                    </div>
                </div>
                <div class="grid gap-4 mb-7 grid-cols-2">
                    <div class="col-span-1" v-if="!isRhuHidden">
                        <InputLabel for="rhu_id" value="RHU" />
                        <select
                            v-model="form.rhu_id"
                            id="rhu_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="" disabled>Select RHU</option>
                            <option value="1">Rhu Main</option>
                            <option value="2">Rhu 2</option>
                            <option value="3">Rhu 3</option>
                        </select>
                    </div>
                    <div class="col-span-1" v-if="!isAdmin && !isSuperAdmin">
                        <InputLabel for="barangay_id" value="Barangay Name" />
                        <Combobox v-model="selectedBarangay" name="barangay_id">
                            <div class="relative w-full">
                                <!-- Input Field -->
                                <ComboboxInput
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    @input="query = $event.target.value"
                                    :displayValue="
                                        (barangay) =>
                                            barangay?.barangay_name || ''
                                    "
                                    placeholder="Select barangay"
                                />
                                <!-- Dropdown Options -->
                                <ComboboxOptions
                                    class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                >
                                    <div
                                        v-if="
                                            filteredBarangayNames.length ===
                                                0 && query !== ''
                                        "
                                        class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                    >
                                        Nothing found.
                                        <a
                                            :href="route('barangay.index')"
                                            :class="{
                                                'bg-gray-100':
                                                    $page.url === '/barangay',
                                            }"
                                            class="ml-3 text-blue-500 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                        >
                                            Create new
                                        </a>
                                    </div>
                                    <ComboboxOption
                                        v-for="barangay in filteredBarangayNames"
                                        :key="barangay.id"
                                        :value="barangay"
                                        class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                    >
                                        <span
                                            class="block truncate font-normal"
                                        >
                                            {{ barangay.barangay_name }}
                                        </span>
                                    </ComboboxOption>
                                </ComboboxOptions>
                            </div>
                        </Combobox>
                    </div>
                </div>

                <div class="flex justify-end items-center space-x-4">
                    <PrimaryButton
                        type="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <svg
                            class="me-1 -ms-1 w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Update user
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
