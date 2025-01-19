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
import { unshiftMed } from "../unshiftmodal";
import { debounce } from "lodash-es";
import LoadingModal from "@/Components/LoadingModal.vue";
import dayjs from "dayjs";

const props = defineProps({
    genericNames: {
        type: Array,
        default: () => [],
    },
    brandNames: {
        type: Array,
        default: () => [],
    },
    categoryNames: {
        type: Array,
        default: () => [],
    },
    batches: Array,
    user: Object,
});
const emit1 = defineEmits();

const date_now = new Date().toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    timeZone: "Asia/Manila",
});

// Define the form using useForm
const form = useForm({
    user_id: props.user.id,
    batch_id: "",
    barangay_id: props.user.barangay_id,
    generic_name_id: "",
    brand: "",
    quantity: "",
    provider: "",
    category_id: "",
    expiration_date: "",
    status: "",
    date_acquired: date_now,
});

const isLoading = ref(false);
const showBrandDropdown = ref(false);
const filteredBrands = ref([]);

// Watch the selected generic name
const selectedGenericName = ref(null);
watch(selectedGenericName, (newValue) => {
    if (newValue) {
        // Set the form's generic_name_id and category_id based on selected generic name
        form.generic_name_id = newValue.id;
        form.category_id = newValue.category_id;

        // Filter brandNames based on the selected generic_name_id
        filteredBrands.value = props.brandNames.filter(
            (brand) => brand.generic_name_id === newValue.id
        );

        selectedCategoryName.value = props.categoryNames.find(
            (category) => category.id === newValue.category_id
        );
    } else {
        form.generic_name_id = "";
        form.category_id = "";
        filteredBrands.value = props.brandNames;
    }
});

// Function to filter brands based on user input
function filterBrands() {
    const query = form.brand.toLowerCase();
    filteredBrands.value = filteredBrands.value.filter((brand) =>
        brand.brand.toLowerCase().includes(query)
    );
}

// Function to select a brand from the dropdown
function selectBrand(brandName) {
    form.brand = brandName;
    showBrandDropdown.value = false;
}

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
    const isClickInsideBrand = e.target.closest(".relative");
    if (!isClickInsideBrand) {
        showBrandDropdown.value = false;
    }
});

// Filter generic names based on user input
const genericQuery = ref("");
const genericNames = ref(props.genericNames);
const filteredGenericNames = computed(() => {
    return genericQuery.value === ""
        ? genericNames.value
        : genericNames.value.filter((genericName) =>
              genericName.generic_name
                  .toLowerCase()
                  .includes(genericQuery.value.toLowerCase())
          );
});

// Define reactive variables
const categoryQuery = ref("");
const categoryNames = ref(props.categoryNames);
const selectedCategoryName = ref(null);
watch(selectedCategoryName, (newValue) => {
    form.category_id = newValue ? newValue.id : "";
});
const filteredCategoryNames = computed(() => {
    return categoryQuery.value === ""
        ? categoryNames.value
        : categoryNames.value.filter((category) =>
              category.category
                  .toLowerCase()
                  .includes(categoryQuery.value.toLowerCase())
          );
});

const handleGenericInput = debounce(
    (e) => (genericQuery.value = e.target.value),
    300
);
const handleCategoryInput = debounce(
    (e) => (categoryQuery.value = e.target.value),
    300
);

function isExpiring(date) {
    // Parse the expiration date using dayjs
    const expirationDate = dayjs(date);

    // Get today's date
    const today = dayjs();

    // Check if the expiration date is within 3 months (90 days)
    return expirationDate.isBefore(today.add(3, 'months')) && expirationDate.isAfter(today);
}

// Initialize Flowbite (if needed)
onMounted(() => {
    initFlowbite();
});

const providerOptions = ref([
    { value: "DOH", label: "DOH" },
    { value: "LGO", label: "LGO" },
    { value: "Donation", label: "Donation" },
]);

const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

function resetForm() {
    emit1('close-modal');
    form.reset();
}

watch(form.errors, (newValue) => {
    isLoading.value = false;
});

function submit() {
    isLoading.value = true;
    form.post(route("medicines.store"), {
        onSuccess: () => {
            isLoading.value = false;
            unshiftMed(filteredBrands.value, form);
            resetForm();
        },
        onError: () => {
            isLoading.value = false;
        },
    });
}
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
                    Add New Medicine
                </h3>

                <button
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="medicine-crud-modal"
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
            <form @submit.prevent="submit" class="p-4 md:p-5">
                <div class="grid gap-4 mb-7 grid-cols-3">
                    <div class="col-span-2">
                        <InputLabel
                            for="generic_name_id"
                            value="Medicine Name"
                        />
                        <Combobox
                            v-model="selectedGenericName"
                            name="generic_name_id"
                            requeired=""
                        >
                            <div class="relative w-full">
                                <!-- Input Field -->
                                <ComboboxInput
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    @input="handleGenericInput"
                                    :displayValue="
                                        (genericName) =>
                                            genericName?.generic_name || ''
                                    "
                                    placeholder="Select medicine"
                                />
                                <!-- Dropdown Options -->
                                <ComboboxOptions
                                    class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                >
                                    <div
                                        v-if="
                                            filteredGenericNames.length === 0 &&
                                            genericQuery !== ''
                                        "
                                        class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                    >
                                        Nothing found.
                                        <a
                                            :href="route('generic_name.index')"
                                            :class="{
                                                'bg-gray-100':
                                                    $page.url ===
                                                    '/generic_name',
                                            }"
                                            class="ml-3 text-blue-500 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                        >
                                            Create new
                                        </a>
                                    </div>
                                    <ComboboxOption
                                        v-for="genericName in filteredGenericNames"
                                        :key="genericName.id"
                                        :value="genericName"
                                        class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                    >
                                        <span
                                            class="block truncate font-normal"
                                        >
                                            {{ genericName.generic_name }}
                                        </span>
                                    </ComboboxOption>
                                </ComboboxOptions>
                            </div>
                        </Combobox>
                        <p
                            v-if="form.errors.generic_name_id"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.generic_name_id }}
                        </p>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Brand</label>
<div class="relative">
    <input
        v-model="form.brand"
        @focus="showBrandDropdown = true"
        @input="filterBrands"
        placeholder="Select or type a Brand Name"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
    />
    <div v-show="showBrandDropdown && filteredBrands.length" class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10 dark:bg-gray-700 dark:border-gray-500">
        <ul class="max-h-48 overflow-y-auto">
            <li v-for="brand in filteredBrands" :key="brand.id" @click="selectBrand(brand.brand)" class="cursor-pointer px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white">
                {{ brand.brand }}
            </li>
        </ul>
    </div>
</div>

                        <p
                            v-if="form.errors.brand"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.brand }}
                        </p>
                    </div>
                </div>
                <div class="grid gap-4 mb-4 grid-cols-3">
                    <div class="col-span-1 sm:col-span-1">
                        <InputLabel for="batch_id" value="Batch number" />
                        <select
                            v-model="form.batch_id"
                            id="batch_id"
                            required=""
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="" selected disabled>
                                Select Batch ID
                            </option>
                            <option
                                v-for="batch in props.batches"
                                :key="batch.id"
                                :value="batch.id"
                            >
                                {{ batch.batch_number }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.batch_id"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.batch_id }}
                        </p>
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <InputLabel for="quantity" value="Quantity" />
                        <input
                            v-model="form.quantity"
                            type="number"
                            name="quantity"
                            id="quantity"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Input quantity"
                            required=""
                        />
                        <p
                            v-if="form.errors.quantity"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.quantity }}
                        </p>
                    </div>
                    <div class="col-span-1">
                        <InputLabel for="date_acquired" value="Received Date" />
                        <div class="relative max-w-sm">
                            <div
                                class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"
                                    />
                                </svg>
                            </div>
                            <vue-tailwind-datepicker
                                v-model="form.date_acquired"
                                :formatter="date_format"
                                id="date_acquired"
                                as-single
                                placeholder="Select date"
                                required=""
                            />
                        </div>
                    </div>
                    <div class="col-span-1">
                        <InputLabel
                            for="expiration_date"
                            value="Expiration Date"
                        />
                        <div class="relative max-w-sm">
                            <div
                                class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"
                                    />
                                </svg>
                            </div>
                            <vue-tailwind-datepicker
                                v-model="form.expiration_date"
                                :formatter="date_format"
                                id="expiration_date"
                                as-single
                                placeholder="Select date"
                                required=""
                            />
                        </div>
                        <p
                            v-if="isExpiring(form.expiration_date)"
                            class="text-yellow-500 text-sm"
                        >
                            Medicine is expiring/expired
                        </p>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <InputLabel for="category_id" value="Category" />
                        <Combobox
                            v-model="selectedCategoryName"
                            name="category_id"
                            requeired=""
                        >
                            <div class="relative w-full">
                                <!-- Input Field -->
                                <ComboboxInput
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    @input="handleCategoryInput"
                                    :displayValue="
                                        (categoryName) =>
                                            categoryName?.category || ''
                                    "
                                    placeholder="Select category"
                                />
                                <!-- Dropdown Options -->
                                <ComboboxOptions
                                    class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                >
                                    <div
                                        v-if="
                                            filteredCategoryNames.length ===
                                                0 && categoryQuery !== ''
                                        "
                                        class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                    >
                                        Nothing found.
                                        <a
                                            :href="
                                                route('medical_category.index')
                                            "
                                            :class="{
                                                'bg-gray-100':
                                                    $page.url ===
                                                    '/medical_category',
                                            }"
                                            class="ml-3 text-blue-500 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                        >
                                            Create new
                                        </a>
                                    </div>
                                    <ComboboxOption
                                        v-for="categoryName in filteredCategoryNames"
                                        :key="categoryName.id"
                                        :value="categoryName"
                                        class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                    >
                                        <span
                                            class="block truncate font-normal"
                                        >
                                            {{ categoryName.category }}
                                        </span>
                                    </ComboboxOption>
                                </ComboboxOptions>
                            </div>
                        </Combobox>
                        <p
                            v-if="form.errors.category_id"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.category_id }}
                        </p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <InputLabel for="provider" value="Provider" />
                        <select
                            v-model="form.provider"
                            required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="" disabled>Select Provider</option>
                            <option
                                v-for="option in providerOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
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
                            ></path>
                        </svg>
                        Add new medicine
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
