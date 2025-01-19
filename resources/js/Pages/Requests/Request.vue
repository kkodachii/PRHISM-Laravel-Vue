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
import LoadingModal from "@/Components/LoadingModal.vue";

defineOptions({ layout: DashboardLayout });

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    genericNames: Array,
    MsupplyNames: Array,
    equipNames: Array,
    auth: Object,
});

const isLoading = ref(false);

const userName = ref(props.auth.user.name);
const userRHU = ref(props.auth.user.rhu_id);
const barangay_name = ref(props.auth.user.barangay_name);
const brgy_id = ref(props.auth.user.barangay_id);
const requestTo = ref("");

const rhuName = ref("");

if (props.auth.user.role_id == 2 && props.auth.user.rhu_id !== 1) {
    requestTo.value = 1;
    rhuName.value = "RHU Main";
} else {
    if (userRHU.value == 1) {
        rhuName.value = "RHU Main";
    } else if (userRHU.value == 2) {
        rhuName.value = "RHU 2";
    } else {
        rhuName.value = "RHU 3";
    }
}

if (props.auth.user.role_id == 2 && props.auth.user.rhu_id == 1) {
    router.push({ name: "dashboard" });
}

const genericNames = ref(props.genericNames);
const query = ref("");

// Watch for changes in props.genericNames
watch(
    () => props.genericNames,
    (newValue) => {
        genericNames.value = newValue;
    }
);

const filteredGenericNames = computed(() => {
    return query.value === ""
        ? genericNames.value
        : genericNames.value.filter((genericName) =>
              genericName.generic_name
                  .toLowerCase()
                  .includes(query.value.toLowerCase())
          );
});



const getAvailableStock = (genericNameId) => {
    // Check if genericNameId is an object or an id
    const id = typeof genericNameId === 'object' ? genericNameId.id : genericNameId;
    const selectedMedicine = genericNames.value.find(item => item.id === id);

    
    return selectedMedicine ? selectedMedicine.quantity : 0;
};

const getAvailableEquipmentStock = (equipmentName) => {
    // If equipmentName is an object, get the equipment_name
    const name = typeof equipmentName === 'object' ? equipmentName.equipment_name : equipmentName;
    const selectedEquipment = modalEquipments.value.find(item => item.equipment_name === name);
    const quantity = selectedEquipment ? selectedEquipment.quantity : 0;

    return quantity;
};

const isButtonDisabled = computed(() => {
    return !isQuantityValid.value || medicine.processing;
});

const getAvailableSupplyStock = (supplyName) => {
    // If supplyName is an object, get the med_sup_name
    const name = typeof supplyName === 'object' ? supplyName.med_sup_name : supplyName;
    const selectedSupply = modalMsupply.value.find(item => item.med_sup_name === name);
    const quantity = selectedSupply ? selectedSupply.quantity : 0;
    return quantity;
};

const isQuantityValid = computed(() => {
    return (
        medicalSupply.value.every((form) => {
            const availableStock = getAvailableSupplyStock(form.name);
            return form.quantity <= availableStock;
        }) &&
        equipment.value.every((form) => {
            const availableStock = getAvailableEquipmentStock(form.name);
            return form.quantity <= availableStock;
        }) &&
        medicine.value.every((form) => {
            const availableStock = getAvailableStock(form.generic_name_id);
            return form.quantity <= availableStock;
        })
    );
});


const modalEquipments = ref(props.equipNames);

const filteredEquip = computed(() => {
    const uniqueEquipments = new Set();

    return modalEquipments.value
        .filter((equip) => {
            const isDuplicate = uniqueEquipments.has(
                equip.equipment_name.toLowerCase()
            );

            if (!isDuplicate) {
                uniqueEquipments.add(equip.equipment_name.toLowerCase());
                return true;
            }

            return false;
        })
        .filter(
            (equip) =>
                query.value === "" ||
                equip.equipment_name
                    .toLowerCase()
                    .includes(query.value.toLowerCase())
        );
});

const modalMsupply = ref(props.MsupplyNames);

const filteredSupply = computed(() => {
    const uniqueMsupply = new Set();

    return modalMsupply.value
        .filter((supply) => {
            const isDuplicate = uniqueMsupply.has(
                supply.med_sup_name.toLowerCase()
            );

            if (!isDuplicate) {
                uniqueMsupply.add(supply.med_sup_name.toLowerCase());
                return true;
            }

            return false;
        })
        .filter(
            (supply) =>
                query.value === "" ||
                supply.med_sup_name
                    .toLowerCase()
                    .includes(query.value.toLowerCase())
        );
});

// Data for medicines, equipment, and medical supplies
const medicine = ref([
    {
        generic_name_id: "", // Ensure this field is correctly used
        quantity: "",
    },
]);

const equipment = ref([
    {
        name: "",
        quantity: "",
    },
]);

const medicalSupply = ref([
    {
        name: "",
        quantity: "",
    },
]);

// Methods for adding entries
const addMedicine = () => {
    medicine.value.push({
        generic_name_id: "",
        quantity: "",
    });
};

const addEquipment = () => {
    equipment.value.push({
        name: "",
        quantity: "",
    });
};

const addMedicalSupply = () => {
    medicalSupply.value.push({
        name: "",
        quantity: "",
    });
};

// Methods for removing entries
const removeMedicine = (index) => {
    medicine.value.splice(index, 1);
};

const removeEquipment = (index) => {
    equipment.value.splice(index, 1);
};

const removeMedicalSupply = (index) => {
    medicalSupply.value.splice(index, 1);
};

// Handle form submission
const submitForm = () => {
    isLoading.value = true;

    const formattedMedicine = medicine.value.map((item) => ({
        generic_name_id: item.generic_name_id.id,
        quantity: item.quantity,
    }));

    const formattedEquipment = equipment.value
        .filter((item) => item.name && item.quantity)
        .map((item) => ({
            name: item.name.equipment_name || item.name,
            quantity: item.quantity,
        }));

    const formattedMedicalSupply = medicalSupply.value
        .filter((item) => item.name && item.quantity)
        .map((item) => ({
            name: item.name.med_sup_name || item.name,
            quantity: item.quantity,
        }));

    useForm({
        barangay_id: brgy_id.value,
        medicine: formattedMedicine,
        equipment: formattedEquipment,
        medicalSupply: formattedMedicalSupply,
    }).post(route("requests.store"), {
        onSuccess: () => {
            isLoading.value = false;
            console.log("Formatted Medicine:", formattedMedicine);
            console.log("Formatted Equipment:", formattedEquipment);
            console.log("Formatted Medical Supply:", formattedMedicalSupply);
        },
        onError: () => {
            isLoading.value = false;
            console.log("Formatted Medicine:", formattedMedicine);
            console.log("Formatted Equipment:", formattedEquipment);
            console.log("Formatted Medical Supply:", formattedMedicalSupply);
        },
    });
};
</script>

<template>
    <Head title="Batch Medicine" />
    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg p-6"
    >
        <div class="mb-5">
            <h3
                class="text-lg font-semibold text-gray-900 dark:text-white mb-0"
            >
                Request for Medical Supplies
            </h3>
        </div>

        <LoadingModal :isLoading="isLoading" />
        <form @submit.prevent="submitForm">
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-2 mb-5"
            >
                <div class="col-span-1">
                    <label
                        for="batch"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Full Name
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="userName"
                            placeholder="Type full name"
                            required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />
                    </div>
                </div>
                <div v-if="barangay_name" class="col-span-1">
                    <label
                        for="barangay"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Barangay
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="barangay_name"
                            placeholder="Type Barangay name"
                            disabled
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />
                    </div>
                </div>
                <div class="col-span-1">
                    <label
                        for="rhu"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Request to RHU
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="rhuName"
                            placeholder="Select RHU"
                            disabled
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />
                    </div>
                </div>
            </div>

            <div class="relative bg-green-100 p-2">
                <span
                    class="text-gray-700 font-semibold dark:text-gray-400 mb-4"
                    >Medicine</span
                >

                <div
                    v-for="(form, index) in medicine"
                    :key="index"
                    :class="{
                        'border-t border-gray-200 dark:border-gray-600':
                            index !== 0,
                    }"
                    class=""
                >
                    <div class="flex items-center">
                        <span
                            class="text-gray-700 mr-4 font-semibold dark:text-gray-400"
                            >#{{ index + 1 }}</span
                        >
                        <div
                            class="grid grid-cols-2 gap-3 p-2 mb-1 lg:grid-cols-4"
                        >
                            <div class="col-span-2 lg:col-span-2">
                                <label
                                    for="generic_name_id"
                                    class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-400"
                                    >Medicine Name</label
                                >
                                <Combobox
                                    v-model="medicine[index].generic_name_id"
                                    name="generic_name_id"
                                >
                                    <div class="relative w-full">
                                        <!-- Input Field -->
                                        <ComboboxInput
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                            @input="query = $event.target.value"
                                            :displayValue="
                                                (genericName) =>
                                                    genericName?.generic_name ||
                                                    ''
                                            "
                                            placeholder="Select medicine"
                                        />
                                        <!-- Dropdown Options -->
                                        <ComboboxOptions
                                            class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                        >
                                            <div
                                                v-if="
                                                    filteredGenericNames.length ===
                                                        0 && query !== ''
                                                "
                                                class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                            >
                                                Nothing found.
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
                                                            form.generic_name_id,
                                                        'font-normal':
                                                            genericName.id !==
                                                            form.generic_name_id,
                                                    }"
                                                    class="block truncate"
                                                >
                                                    {{
                                                        genericName.generic_name
                                                    }}
                                                </span>
                                            </ComboboxOption>
                                        </ComboboxOptions>
                                    </div>
                                </Combobox>
                                <div v-if="medicine[index].generic_name_id" class="mt-1 font-semibold   ">
        <span class=" text-sm mt-1 text-gray-700">
        Available Stock:
        <span class="text-green-600">{{ getAvailableStock(medicine[index].generic_name_id) }}</span>
    </span>
        <div v-if="form.quantity > getAvailableStock(medicine[index].generic_name_id)" class="text-red-500 text-sm mt-1">
            Quantity cannot be more than available
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
                                    v-model="form.quantity"
                                    placeholder="Input quantity"
                                    required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                />
                            </div>
                        </div>
                        
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

                    


                </div>
                <button
                    type="button"
                    @click="addMedicine"
                    class="bg-white py-2 m-2 shadow-md px-4 rounded-md text-sm font-semibold text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    Add Medicine
                </button>
            </div>

            <div class="relative bg-blue-100 p-2 mt-4">
                <span
                    class="text-gray-700 font-semibold dark:text-gray-400 mb-4"
                    >Equipment</span
                >

                <div
                    v-for="(form, index) in equipment"
                    :key="index"
                    :class="{
                        'border-t border-gray-200 dark:border-gray-600':
                            index !== 0,
                    }"
                    class=""
                >
                    <div class="flex items-center">
                        <span
                            class="text-gray-700 mr-4 font-semibold dark:text-gray-400"
                            >#{{ index + 1 }}</span
                        >
                        <div
                            class="grid grid-cols-2 gap-3 p-2 mb-1 lg:grid-cols-4"
                        >
                            <div class="col-span-2 lg:col-span-2">
                                <label
                                    for="name"
                                    class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-400"
                                    >Equipment Name</label
                                >
                                <Combobox
                                    v-model="equipment[index].name"
                                    name="equipment_name"
                                >
                                    <div class="relative w-full">
                                        <!-- Input Field -->
                                        <ComboboxInput
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                            @input="query = $event.target.value"
                                            :displayValue="
                                                (equipName) =>
                                                    equipName?.equipment_name ||
                                                    ''
                                            "
                                            placeholder="Select equipment"
                                        />
                                        <!-- Dropdown Options -->
                                        <ComboboxOptions
                                            class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                        >
                                            <div
                                                v-if="
                                                    filteredEquip.length ===
                                                        0 && query !== ''
                                                "
                                                class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                            >
                                                Nothing found.
                                            </div>
                                            <ComboboxOption
                                                v-for="equip in filteredEquip"
                                                :key="equip.id"
                                                :value="equip"
                                                class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                            >
                                                <span
                                                    :class="{
                                                        'font-medium':
                                                            equip.id ===
                                                            form.name,
                                                        'font-normal':
                                                            equip.id !==
                                                            form.name,
                                                    }"
                                                    class="block truncate"
                                                >
                                                    {{ equip.equipment_name }}
                                                </span>
                                            </ComboboxOption>
                                        </ComboboxOptions>
                                    </div>
                                </Combobox>
                                <div v-if="equipment[index].name" class="mt-1 font-semibold   ">
        <span class=" text-sm mt-1 text-gray-700">
        Available Stock:
        <span class="text-green-600">{{ getAvailableEquipmentStock(equipment[index].name) }}</span>
    </span>
        <div v-if="form.quantity > getAvailableEquipmentStock(equipment[index].name)" class="text-red-500 text-sm mt-1">
            Quantity cannot be more than available
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
                                    v-model="form.quantity"
                                    placeholder="Input quantity"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                />
                            </div>
                        </div>
                        <button
                            @click.prevent="removeEquipment(index)"
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
                </div>
                <button
                    type="button"
                    @click="addEquipment"
                    class="bg-white py-2 m-2 shadow-md px-4 rounded-md text-sm font-semibold text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    Add Equipment
                </button>
            </div>

            <div class="relative bg-violet-100 p-2 mt-4">
                <span
                    class="text-gray-700 font-semibold dark:text-gray-400 mb-4"
                    >Medical Supply</span
                >

                <div
                    v-for="(form, index) in medicalSupply"
                    :key="index"
                    :class="{
                        'border-t border-gray-200 dark:border-gray-600':
                            index !== 0,
                    }"
                    class=""
                >
                    <div class="flex items-center">
                        <span
                            class="text-gray-700 mr-4 font-semibold dark:text-gray-400"
                            >#{{ index + 1 }}</span
                        >
                        <div
                            class="grid grid-cols-2 gap-3 p-2 mb-1 lg:grid-cols-4"
                        >
                            <div class="col-span-2 lg:col-span-2">
                                <label
                                    for="name"
                                    class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-400"
                                    >Supply Name</label
                                >
                                <Combobox
                                    v-model="medicalSupply[index].name"
                                    name="medical_supply_name"
                                >
                                    <div class="relative w-full">
                                        <!-- Input Field -->
                                        <ComboboxInput
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                            @input="query = $event.target.value"
                                            :displayValue="
                                                (supplyName) =>
                                                    supplyName?.med_sup_name ||
                                                    ''
                                            "
                                            placeholder="Select medical supply"
                                        />
                                        <!-- Dropdown Options -->
                                        <ComboboxOptions
                                            class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                        >
                                            <div
                                                v-if="
                                                    filteredSupply.length ===
                                                        0 && query !== ''
                                                "
                                                class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                            >
                                                Nothing found.
                                            </div>
                                            <ComboboxOption
                                                v-for="supply in filteredSupply"
                                                :key="supply.id"
                                                :value="supply"
                                                class="select-none relative py-2 pl-5 pr-4 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                            >
                                                <span
                                                    :class="{
                                                        'font-medium':
                                                            supply.id ===
                                                            form.name,
                                                        'font-normal':
                                                            supply.id !==
                                                            form.name,
                                                    }"
                                                    class="block truncate"
                                                >
                                                    {{ supply.med_sup_name }}
                                                </span>
                                            </ComboboxOption>
                                        </ComboboxOptions>
                                    </div>
                                </Combobox>
                                <div v-if="medicalSupply[index].name" class="mt-1 font-semibold   ">
        <span class=" text-sm mt-1 text-gray-700">
        Available Stock:
        <span class="text-green-600">{{ getAvailableSupplyStock(medicalSupply[index].name) }}</span>
    </span>
        <div v-if="form.quantity > getAvailableSupplyStock(medicalSupply[index].name)" class="text-red-500 text-sm mt-1">
            Quantity cannot be more than available
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
                                    v-model="form.quantity"
                                    placeholder="Input quantity"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                />
                            </div>
                        </div>
                        <button
                            @click.prevent="removeMedicalSupply(index)"
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
                </div>
                <button
                    type="button"
                    @click="addMedicalSupply"
                    class="bg-white py-2 m-2 shadow-md px-4 rounded-md text-sm font-semibold text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    Add Medical Supply
                </button>
            </div>


            <div class="mt-6 flex justify-end">
                <button
        type="submit"
        class="inline-flex justify-center bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-white dark:bg-blue-600 dark:focus:ring-blue-800"
        :class="{ 
            'opacity-35 cursor-not-allowed': isButtonDisabled, 
            'opacity-100 cursor-pointer hover:bg-blue-800': !isButtonDisabled  
        }"
        :disabled="isButtonDisabled"
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
        Send Request
    </button>
</div>

        </form>
    </div>
</template>
