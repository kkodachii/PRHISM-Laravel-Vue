<script setup>
import { ref, watch, onMounted, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
} from "@headlessui/vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import dayjs from 'dayjs';

defineOptions({ layout: DashboardLayout });

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    genericNames: Array,
    categoryNames: Array,
    auth: Object,
    batches: Object,
    brandNames: Array,
    medicine: Object,
});

const date_now = new Date().toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    timeZone: "Asia/Manila",
});

const userID = ref(props.auth.user.id);
const barangayID = ref(props.auth.user.barangay_id);
const dateAcquired = ref(date_now);
const selectedBatch = ref('New');

const medicine = ref([
    {
        generic_name_id: "",
        brand: "",
        category_id: "",
        expiration_date: "",
        quantity: "",
        provider: "",
        date_acquired: dateAcquired.value,
        showBrandDropdown: false, // For each item’s dropdown state
        filteredBrands: [], // For each item’s filtered list of brands
    },
]);

const form = useForm({
    batch_id: "",
    date_acquired: dateAcquired.value,
    medicine: medicine.value,
});

const addMedicine = () => {
    medicine.value.push({
        generic_name_id: "",
        brand: "",
        category_id: "",
        expiration_date: "",
        quantity: "",
        provider: "",
        date_acquired: dateAcquired.value,
        showBrandDropdown: false,
        filteredBrands: [],
    });
};

// Function to remove a medicine entry
const removeMedicine = (index) => {
    medicine.value.splice(index, 1);
};

const debounceTimeout = ref(null);

const debounce = (func, delay) => {
    return function (...args) {
        clearTimeout(debounceTimeout.value);
        debounceTimeout.value = setTimeout(() => {
            func(...args);
        }, delay);
    };
};

// Function to filter brand names based on input
const filterBrands = debounce((index) => {
    const query = medicine.value[index].brand.toLowerCase();

    // Early exit if input is empty
    if (!query) {
        medicine.value[index].filteredBrands = [];
        return;
    }

    medicine.value[index].filteredBrands = props.brandNames.filter((brand) =>
        brand.brand.toLowerCase().includes(query)
    );

    // Show dropdown if matches are found, otherwise hide it
    medicine.value[index].showBrandDropdown =
        medicine.value[index].filteredBrands.length > 0;
}, 800); // Adjust delay as needed

// Function to select a brand name from the dropdown
const selectBrand = (index, brandName) => {
    medicine.value[index].brand = brandName;
    medicine.value[index].showBrandDropdown = false; // Close dropdown after selection
};

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
    const isClickInside = e.target.closest(".relative");
    if (!isClickInside) {
        medicine.value.forEach((item) => (item.showBrandDropdown = false));
    }
});

const genericQuery = ref("");
const categoryQuery = ref("");

const genericNames = ref(props.genericNames);
watch(
    () => props.genericNames,
    (newValue) => {
        genericNames.value = newValue;
    }
);
const filteredGenericNames = computed(() => {
    return genericQuery.value === ""
        ? genericNames.value
        : genericNames.value.filter((genericName) =>
              genericName.generic_name
                  .toLowerCase()
                  .includes(genericQuery.value.toLowerCase())
          );
});

const categoryNames = ref(props.categoryNames);
const selectedCategoryName = ref(null);
watch(
    () => props.categoryNames,
    (newValue) => {
        categoryNames.value = newValue;
    }
);
watch(selectedCategoryName, (newValue) => {
    form.category_id = newValue ? newValue.id : "";
});
const filteredCategoryNames = computed(() => {
    return categoryQuery.value === ""
        ? categoryNames.value
        : categoryNames.value.filter((categoryName) =>
              categoryName.category
                  .toLowerCase()
                  .includes(categoryQuery.value.toLowerCase())
          );
});

const handleGenericInput = debounce(
    (e) => (genericQuery.value = e.target.value),
    800
);
const handleCategoryInput = debounce(
    (e) => (categoryQuery.value = e.target.value),
    800
);

const providerOptions = ref([
    { value: "DOH", label: "DOH" },
    { value: "LGO", label: "LGO" },
    { value: "Donation", label: "Donation" },
]);

const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

function isExpiring(date) {
    // Parse the expiration date using dayjs
    const expirationDate = dayjs(date);

    // Get today's date
    const today = dayjs();

    // Check if the expiration date is within 3 months (90 days)
    return expirationDate.isBefore(today.add(3, 'months')) && expirationDate.isAfter(today);
}

const submitForm = () => {
    form.medicine = medicine.value.map((item) => ({
        user_id: userID.value,
        batch_id: selectedBatch.value,
        barangay_id: barangayID.value,
        generic_name_id: item.generic_name_id.id,
        brand: item.brand || "", // Use item.brand directly
        category_id: item.category_id.id,
        expiration_date: item.expiration_date,
        quantity: item.quantity,
        provider: item.provider,
        date_acquired: dateAcquired.value,
    }));

    form.post(route("batch-medicine.store"), {
        onSuccess: () => {},
        onError: () => {},
    });
};
</script>

<template>
    <Head title="Batch Medicine" />
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg p-6">
        <div class="mb-5">
            <h3
                class="text-lg font-semibold text-gray-900 dark:text-white mb-0"
            >
                Add New Medicine by Batch
            </h3>
        </div>

        <form @submit.prevent="submitForm">
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-2 mb-5"
            >
                <div class="col-span-1">
                    <label
                        for="batch"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Batch no.
                    </label>
                    <div class="relative">
                        <select
                            v-model="selectedBatch"
                            id="batch_id"
                            required=""
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="New" selected>
                                Create new Batch
                            </option>
                            <option
                                v-for="batch in props.batches"
                                :key="batch.id"
                                :value="batch.id"
                            >
                                {{ batch.batch_number }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-span-1">
                    <label
                        for="date_acquired"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Received Date
                    </label>
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
                            v-model="dateAcquired"
                            :formatter="date_format"
                            id="date_acquired"
                            as-single
                            placeholder="Select date"
                            required
                        />
                    </div>
                </div>
            </div>

            <div
                v-for="(item, index) in medicine"
                :key="index"
                :class="{
                    'border-t border-gray-200 dark:border-gray-600 pt-6':
                        index !== 0,
                }"
                class="grid grid-cols-2 gap-6 mb-8 p-2 lg:grid-cols-4"
            >
                <div
                    class="col-span-2 lg:col-span-4 flex flex-row justify-between"
                >
                    <span class="text-gray-700 font-semibold dark:text-gray-400"
                        >Medicine no. {{ index + 1 }}</span
                    >
                    <button
                        @click.prevent="removeMedicine(index)"
                        class="text-red-500 hover:text-red-700 focus:outline-none ms-4"
                    >
                        <svg
                            class="w-6 h-6 text-gray-800 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6"
                            />
                        </svg>
                    </button>
                </div>

                <div class="col-span-2 lg:col-span-2">
                    <label
                        for="generic_name_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Medicine Name</label
                    >
                    <Combobox
                        v-model="medicine[index].generic_name_id"
                        name="generic_name_id"
                    >
                        <!-- @change="handleGenericNameSelection(index, $event)" -->
                        <div class="relative w-full">
                            <!-- Input Field -->
                            <ComboboxInput
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                @input="handleGenericInput"
                                placeholder="Type medicine name"
                                required
                                :displayValue="
                                    (genericName) =>
                                        genericName?.generic_name || ''
                                "
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
                                                $page.url === '/generic_name',
                                        }"
                                        class="ml-3 text-blue-500 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                        >Create new</a
                                    >
                                </div>
                                <ComboboxOption
                                    v-for="genericName in filteredGenericNames"
                                    :key="genericName.id"
                                    :value="genericName"
                                    class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                >
                                    <span
                                        :class="{
                                            'font-medium':
                                                genericName.id ===
                                                item.generic_name_id,
                                            'font-normal':
                                                genericName.id !==
                                                item.generic_name_id,
                                        }"
                                        class="block truncate"
                                    >
                                        {{ genericName.generic_name }}
                                    </span>
                                </ComboboxOption>
                            </ComboboxOptions>
                        </div>
                    </Combobox>
                </div>
                <div class="col-span-1">
                    <label
                        for="brand"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Brand</label
                    >
                    <div class="relative">
                        <input
                            v-model="item.brand"
                            @focus="item.showBrandDropdown = true"
                            @input="filterBrands(index)"
                            placeholder="Select or type a Brand Name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />

                        <!-- Dropdown Menu for Brand Suggestions -->
                        <div
                            v-show="
                                item.showBrandDropdown &&
                                item.filteredBrands.length
                            "
                            class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10 dark:bg-gray-700 dark:border-gray-500"
                        >
                            <ul class="max-h-48 overflow-y-auto">
                                <li
                                    v-for="filteredBrand in item.filteredBrands"
                                    :key="filteredBrand"
                                    @click="
                                        selectBrand(index, filteredBrand.brand)
                                    "
                                    class="cursor-pointer px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white"
                                >
                                    {{ filteredBrand.brand }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-span-1">
                    <label
                        for="quantity"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Quantity</label
                    >
                    <input
                        type="number"
                        v-model="item.quantity"
                        placeholder="Input quantity"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    />
                </div>
                <div class="col-span-1">
                    <label
                        for="category_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Category</label
                    >
                    <Combobox
                        v-model="medicine[index].category_id"
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
                                required
                            />
                            <!-- Dropdown Options -->
                            <ComboboxOptions
                                class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                            >
                                <div
                                    v-if="
                                        filteredCategoryNames.length === 0 &&
                                        categoryQuery !== ''
                                    "
                                    class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                >
                                    Nothing found.
                                    <a
                                        :href="route('medical_category.index')"
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
                                    <span class="block truncate font-normal">
                                        {{ categoryName.category }}
                                    </span>
                                </ComboboxOption>
                            </ComboboxOptions>
                        </div>
                    </Combobox>
                </div>

                <div class="col-span-1">
                    <label
                        for="expiration_date"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Expiration Date
                    </label>
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
                            v-model="item.expiration_date"
                            :formatter="date_format"
                            id="expiration_date"
                            as-single
                            placeholder="Select date"
                            required
                        />
                    </div>
                    <p
                        v-if="isExpiring(item.expiration_date)"
                        class="text-yellow-500 text-sm"
                    >
                        Medicine is expiring/expired
                    </p>
                </div>
                <div class="col-span-1">
                    <label
                        for="provider"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Provider</label
                    >
                    <select
                        v-model="item.provider"
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
            <!-- buttons -->
            <div
                class="flex flex-col justify-between sm:flex-row gap-4 p-2 mt-6"
            >
                <button
                    type="button"
                    @click="addMedicine"
                    class="bg-gray-200 py-2 px-4 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Add another
                </button>
                <button
                    type="submit"
                    class="inline-flex justify-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-white dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    <svg
                        class="w-5 h-5 me-2"
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
                    Add Medicine
                </button>
            </div>
        </form>
    </div>
</template>
