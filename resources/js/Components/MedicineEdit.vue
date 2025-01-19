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
import { debounce } from "lodash";
import LoadingModal from "@/Components/LoadingModal.vue";

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
    medicine: Object,
});

const emit2 = defineEmits();

// Initialize form with medicine data
const form = useForm({
    id: props.medicine?.id ?? "",
    user_id: props.medicine?.user_id ?? "1",
    barangay_id: props.medicine?.barangay_id ?? "1",
    batch_id: props.medicine?.batch_id ?? "",
    generic_name_id: props.medicine?.generic_name_id ?? "",
    brand: "",
    quantity: props.medicine?.quantity ?? "",
    provider: props.medicine?.provider ?? "",
    category_id: props.medicine?.category_id ?? "",
    date_acquired: props.medicine?.rawDateAcquired ?? "",
    expiration_date: props.medicine?.expiration_date ?? "",
    status: props.medicine?.status ?? "",
});

function resetForm() {
    emit2('close-modal');
    form.reset();
}

const showBrandDropdown = ref(false);
const filteredBrands = ref(props.brandNames);
const isLoading = ref(false);

// Function to filter equipment based on input
function filterBrands() {
    const query = form.brand.toLowerCase();
    filteredBrands.value = props.brandNames.filter((brand) =>
        brand.brand.toLowerCase().includes(query)
    );
}

// Function to select an equipment name from the dropdown
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

const genericQuery = ref("");
const categoryQuery = ref("");

// Initialize selectedGenericName
const selectedGenericName = ref(null);
watch(
    () => form.generic_name_id,
    (newGenericNameId) => {
        selectedGenericName.value =
            props.genericNames.find((gn) => gn.id === newGenericNameId) || null;
    },
    { immediate: true }
);
watch(selectedGenericName, (newVal) => {
    form.generic_name_id = newVal ? newVal.id : "";
});
const filteredGenericNames = computed(() => {
    return genericQuery.value === ""
        ? props.genericNames
        : props.genericNames.filter((genericName) =>
              genericName.generic_name
                  .toLowerCase()
                  .includes(genericQuery.value.toLowerCase())
          );
});

// Watch for selectedCategoryName
const selectedCategoryName = ref(null);
watch(
    () => form.category_id,
    (newCategoryNameId) => {
        selectedCategoryName.value =
            props.categoryNames.find((gn) => gn.id === newCategoryNameId) ||
            null;
    },
    { immediate: true }
);
watch(selectedCategoryName, (newVal) => {
    form.category_id = newVal ? newVal.id : "";
});
const filteredCategoryNames = computed(() => {
    return categoryQuery.value === ""
        ? props.categoryNames
        : props.categoryNames.filter((categoryName) =>
              categoryName.category
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

// Watch for changes in medicine to update form fields
watch(
    () => props.medicine,
    (newVal) => {
        if (newVal) {
            form.user_id = newVal.user_id || "";
            form.barangay_id = newVal.barangay_id || "";
            form.batch_id = newVal.batch_id || "";
            form.generic_name_id = newVal.generic_name_id || "";
            form.brand = newVal.brand || null;
            form.quantity = newVal.quantity || "";
            form.provider = newVal.provider || "";
            form.category_id = newVal.category_id || "";
            form.date_acquired = newVal.rawDateAcquired ? formatDate(newVal.rawDateAcquired) : "";
            form.expiration_date = newVal.expiration_date ? formatDate(newVal.expiration_date) : "";

            form.status = newVal.status || "";
        }
    }
);


const formatDate = (dateString) => {
    if (!dateString) return "";
    const options = { year: 'numeric', month: 'short', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-GB', options).replace(',', '');
};



// Provider options
const providerOptions = ref([
    { value: "DOH", label: "DOH" },
    { value: "LGO", label: "LGO" },
    { value: "Donation", label: "Donation" },
]);

const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

// Initialize Flowbite on mounted
onMounted(() => {
    initFlowbite();
});

// Submit the form
function submit() {
    isLoading.value = true;

    form.put(route("medicines.update", props.medicine.id), {
        onSuccess: () => {
            isLoading.value = false;
            window.location.reload();
            unshiftMed(filteredBrands.value, form);
            resetForm();
        },
        onError: () => {
            isLoading.value = false;
            resetForm();
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
                    Edit Medicine
                </h3>
                <button
                    type="button"
                    @click="resetForm"
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
                                            :href="route('brand.index')"
                                            :class="{
                                                'bg-gray-100':
                                                    $page.url === '/brand',
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
                        <label
                            for="brand"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Brand</label
                        >
                        <div class="relative">
                            <input
                                v-model="form.brand"
                                @focus="showBrandDropdown = true"
                                @input="filterBrands"
                                placeholder="Select or type a Brand Name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            />
                            <div
                                v-show="
                                    showBrandDropdown && filteredBrands.length
                                "
                                class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10 dark:bg-gray-700 dark:border-gray-500"
                            >
                                <ul class="max-h-48 overflow-y-auto">
                                    <li
                                        v-for="brand in filteredBrands"
                                        :key="brand.id"
                                        @click="selectBrand(brand.brand)"
                                        class="cursor-pointer px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white"
                                    >
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
                                :formatter="date_format"
                                v-model="form.date_acquired"
                                id="date_acquired"
                                as-single
                                placeholder="Select date"
                                required
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
                                :formatter="date_format"
                                v-model="form.expiration_date"
                                id="expiration_date"
                                as-single
                                placeholder="Select date"
                                required
                            />
                        </div>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <InputLabel for="category_id" value="Category" />
                        <Combobox
                            v-model="selectedCategoryName"
                            name="category_id"
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
                    <button
                        type="button"
                        @click="resetForm"
                        class="text-gray-500 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-3 py-2.5 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
