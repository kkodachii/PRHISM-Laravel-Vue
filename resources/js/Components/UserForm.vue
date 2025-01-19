<script setup>
import { ref, onMounted, computed, watch } from "vue";
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
});

const emit1 = defineEmits();

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role_id: "",
    rhu_id: "1",
    profile_picture: null,
    preview: null,
    barangay_id: "1",
});

// Reference for barangayNames and selected barangay
const barangayNames = ref(props.barangayNames);
const selectedBarangay = ref(null);
const query = ref("");
const isSuperAdmin = ref(false);
const isRhuHidden = ref(false);
const isAdmin = ref(false);
const isLoading = ref(false);

// Watch for changes in the rhu_id to filter barangays accordingly
watch(
    () => form.rhu_id,
    (newValue) => {
        if (newValue in props.rhuBarangaysMap) {
            // Filter barangays based on selected RHU
            barangayNames.value = props.rhuBarangaysMap[newValue];
            selectedBarangay.value = null;
            form.barangay_id = "";
        } else {
            barangayNames.value = props.barangayNames;
        }

        // Set barangay_id based on rhu_id if user is admin
        if (isAdmin.value) {
            form.barangay_id = newValue; // Assuming rhu_id corresponds to barangay_id
        }
    }
);

// Watch for changes in the selected barangay
watch(selectedBarangay, (newValue) => {
    console.log("Selected Barangay:", newValue);
    form.barangay_id = newValue ? newValue.id : "";
    console.log("Form barangay_id:", form.barangay_id);
});

// Watch for changes in role_id to set IDs and toggle visibility
watch(
    () => form.role_id,
    (newValue) => {
        if (newValue === "3") {
            // SuperAdmin
            selectedBarangay.value = null;
            isSuperAdmin.value = true;
            isRhuHidden.value = true; // Hide RHU selection
            isAdmin.value = false; // Ensure isAdmin is false
        } else if (newValue === "2") {
            // Admin
            selectedBarangay.value = null;
            isAdmin.value = true; // Set isAdmin to true
            isSuperAdmin.value = false;
            isRhuHidden.value = false;
            form.barangay_id = form.rhu_id; // Set barangay_id based on rhu_id
        } else {
            isSuperAdmin.value = false;
            isAdmin.value = false;
            isRhuHidden.value = false;
        }
    }
);

const filteredBarangayNames = computed(() => {
    return query.value === ""
        ? barangayNames.value
        : barangayNames.value.filter((barangay) =>
              barangay.barangay_name
                  .toLowerCase()
                  .includes(query.value.toLowerCase())
          );
});

// Initialize Flowbite on component mount
onMounted(() => {
    initFlowbite();
});

// Reset the form
function resetForm() {
    emit1('close-modal');
    form.reset();
}

// Submit form data and reset form upon success
function submit() {
    isLoading.value = true;
    form.post(route("users.store"), {
        onSuccess: () => {
            isLoading.value = false;
            resetForm();
            selectedBarangay.value = null; // Reset selected barangay

        },
        onError: (errors) => {
            isLoading.value = false;
            resetForm();
        },
    });
}

// Handle profile picture change
const change = (e) => {
    form.profile_picture = e.target.files[0];
    form.preview = URL.createObjectURL(e.target.files[0]);
};
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
                    Add New User
                </h3>
                <button
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="user-crud-modal"
                    @click="resetForm"
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
                                form.preview ??
                                '/storage/icons/general/default_profile.svg'
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
                        />
                        <p v-if="form.errors.name" class="text-red-600 text-sm">
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div class="col-span-1">
                        <InputLabel for="email" value="Email" />
                        <input
                            v-model="form.email"
                            type="email"
                            name="email"
                            id="email"
                            autocomplete="off"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Type email address"
                            required
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>
                </div>
                <div class="grid gap-4 mb-3 grid-cols-2">
                    <div class="col-span-1">
                        <InputLabel for="password" value="Password" />
                        <input
                            v-model="form.password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            id="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Type password"
                            required
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>
                    <div class="col-span-1">
                        <InputLabel
                            for="password_confirmation"
                            value="Confirm Password"
                        />
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Confirm password"
                            required
                        />
                        <p
                            v-if="form.errors.password_confirmation"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <div class="col-span-1">
                        <InputLabel for="role_id" value="Role" />
                        <select
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
                        Add new user
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
