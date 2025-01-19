<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
import { initFlowbite } from "flowbite";
import Navbar from "../Components/Navbar.vue";
import Sidebar from "../Components/Sidebar.vue";
import Echo from "laravel-echo";
import ToastList from "@/Components/ToastList.vue";
import toast from "../Stores/toast.js";
import formattedDate from "@/Components/formattedDate.vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    notifications: Array,
    users: Object,
    auth: Object,
});

const searchInput = ref("");
const searchResults = ref([]);

// Item routes based on role_id
const itemRoutes = {
    1: [
        { name: "Dashboard", route: "/dashboard" },
        { name: "Profile", route: "/profile" },
        { name: "Inventory", route: "/inventory" },
        { name: "Dispense", route: "/dispense" },
        { name: "Dispense History", route: "/dispense_history" },
    ],
    2: [
        { name: "Dashboard", route: "/dashboard" },
        { name: "Batch add (Equipment)", route: "/batch-equipment" },
        { name: "Batch add (Medical Supply)", route: "/batch-msupply" },
        { name: "Batch add (Medicine)", route: "/batch-medicine" },
        { name: "Equipment", route: "/equipments" },
        { name: "Medical Supply", route: "/medical_supplies" },
        { name: "Medicine", route: "/medicines" },
        { name: "Delivery", route: "/delivery" },
        { name: "Profile", route: "/profile" },
        { name: "Reports", route: "/reports" },
        { name: "Add/Edit Category", route: "/generic_name" },
        { name: "Batches", route: "/batches" },
        { name: "Midwife Inventory", route: "/barangay_list" },
        { name: "Activity log", route: "/activitylog" },
        { name: "Usage log (Equipment)", route: "/equipmentlog" },
        { name: "Usage log (Medical Supply)", route: "/medicineusagelog" },
        { name: "Usage log (Medicine)", route: "/medicalsupplylog" },
        { name: "Reports", route: "/reports" },
    ],
    3: [
        { name: "Dashboard", route: "/dashboard" },
        { name: "Batch add (Equipment)", route: "/batch-equipment" },
        { name: "Batch add (Medical Supply)", route: "/batch-msupply" },
        { name: "Batch add (Medicine)", route: "/batch-medicine" },
        { name: "Equipment", route: "/equipments" },
        { name: "Medical Supply", route: "/medical_supplies" },
        { name: "Medicine", route: "/medicines" },
        { name: "Delivery", route: "/delivery" },
        { name: "Profile", route: "/profile" },
        { name: "Reports", route: "/reports" },
        { name: "Add/Edit Category", route: "/generic_name" },
        { name: "Batches", route: "/batches" },
        { name: "Midwife Inventory", route: "/barangay_list" },
        { name: "Backups", route: "/backups" },
        { name: "Activity log", route: "/activitylog" },
        { name: "Usage log (Equipment)", route: "/equipmentlog" },
        { name: "Usage log (Medical Supply)", route: "/medicineusagelog" },
        { name: "Usage log (Medicine)", route: "/medicalsupplylog" },
        { name: "Reports", route: "/reports" },
    ],
};

// Watch for changes in the search input to filter results
watch(searchInput, (newValue) => {
    if (newValue && newValue.length >= 2) {
        const userRoleRoutes = itemRoutes[props.auth.user.role_id] || [];
        searchResults.value = userRoleRoutes.filter(({ name }) =>
            name.toLowerCase().includes(newValue.toLowerCase())
        );
    } else {
        searchResults.value = [];
    }
});

const user = usePage().props.auth.user;

const notifs = ref(props.notifications);
const unreadCount = ref(
    props.notifications.filter((notification) => !notification.read_at).length
);
const users = props.users;

const markNotificationAsRead = (id) => {
    Inertia.post(
        `/notifications/${id}/mark-read`,
        {},
        {
            preserveScroll: true, // Keeps the current scroll position
            onSuccess: () => {
                const index = notifications.findIndex((n) => n.id === id);
                if (index !== -1) {
                    notifications[index].read_at = new Date().toISOString(); // Mark as read locally
                }
            },
        }
    );
};

const markAllNotificationsAsRead = () => {
    Inertia.post(
        "/notifications/mark-all-as-read",
        {},
        {
            onSuccess: () => {},
        }
    );
};

function getUserProfilePicture(userId) {
    const user = users[userId - 1]; // Access user directly using the userId

    return user && user.profile_picture
        ? `/storage/${user.profile_picture}`
        : "/storage/icons/general/default_profile.svg";
}

const addNotification = (event, message, type) => {
    notifs.value.unshift({
        notifiable_id: event.id,
        data: {
            user_id: event.user_id || null,
            action: event.action,
            title: event.title,
            message: event.message,
        },
        created_at: new Date(),
        read_at: null,
    });

    unreadCount.value += 1;

    toast.add({
        message,
        type,
    });
};

onMounted(() => {
    initFlowbite();

    window.Echo.private(`App.Models.User.${user.id}`)
        .listen("RequestReceived", (event) => {
            addNotification(event, "You have new medicine request!", "success");
        })
        .listen("MedicineLowAlertEvent", (event) => {
            addNotification(event, "Medicine is low on stock!", "warning");
        })
        .listen("MedicineExpiringAlertEvent", (event) => {
            addNotification(event, "Medicine is expiring soon!", "danger");
        })
        .listen("RequestRejectEvent", (event) => {
            addNotification(event,"Supply Request has been Rejected!","danger");
        })
        .listen("RequestApprovedEvent", (event) => {
            addNotification(event,"Supply Request has been Approved!","success");
        })
        .listen("RequestDeliveredEvent", (event) => {
            addNotification(event,"Supplies has been Delivered Successfully!","success");
        })
        .listen("DatabaseBackupEvent", (event) => {
            addNotification(event, "Database Backup Successfully!", "success");
        })
        .listen("DatabaseRestoreEvent", (event) => {
            addNotification(event, "Database Restored Successfully!", "success");
        })
        .listen("DeliveryReportEvent", (event) => {
            addNotification(event,"Supplies has been Delivered with Reported Issue.","success");
        })
        .listen("DeliveryReturnEvent", (event) => {
            addNotification(event,"Supplies has been Requested for Return.","danger");
        })
        .listen("RequestClaimedEvent", (event) => {
            addNotification(event,"Supplies has been claimed","success");
        });
});


</script>

<template>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        <!-- Navbar -->
        <nav
            class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50"
        >
            <div class="flex flex-wrap justify-between items-center">
                <div class="flex justify-start items-center">
                    <a
                        :href="route('dashboard')"
                        class="flex items-center justify-between mr-4"
                    >
                        <img
                            src="/storage/prhism/logo.svg"
                            class="mr-2 h-10"
                            alt="PRHISM Logo"
                        />
                        <span
                            class="self-center hidden md:block text-2xl font-semibold whitespace-nowrap dark:text-white"
                            >PRHISM</span
                        >
                    </a>
                    <form
                        action="#"
                        method="GET"
                        class="hidden md:block md:pl-2"
                    >
                        <label for="topbar-search" class="sr-only"
                            >Search</label
                        >
                        <div class="relative sm:w-64 md:w-96">
                            <div
                                class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
                            >
                                <svg
                                    class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    ></path>
                                </svg>
                            </div>
                            <input
                                type="text"
                                v-model="searchInput"
                                id="topbar-search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Search"
                            />
                            <!-- Dropdown for search results -->
                            <div
                                v-if="searchInput.length >= 2"
                                class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 w-full shadow-lg"
                            >
                                <ul v-if="searchResults.length > 0">
                                    <li
                                        v-for="(result, index) in searchResults"
                                        :key="index"
                                        class="p-1 pl-10 hover:bg-gray-100 cursor-pointer"
                                    >
                                        <a
                                            :href="result.route"
                                            class="text-gray-600 block w-full"
                                            >{{ result.name }}</a
                                        >
                                    </li>
                                </ul>
                                <!-- Show "Nothing found" if no results are returned -->
                                <div
                                    v-else
                                    class="p-2 pl-10 text-gray-700 text-left"
                                >
                                    <p>Nothing found</p>
                                    <p class="text-gray-500">
                                        We couldn't find any matches for "{{
                                            searchInput
                                        }}".
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex items-center lg:order-2">
                    <Link
                        :href="route('mobile.search')"
                        class="p-2 mr-1 text-gray-500 rounded-lg md:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                    >
                        <span class="sr-only">Toggle search</span>
                        <svg
                            aria-hidden="true"
                            class="w-6 h-6"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            ></path>
                        </svg>
                    </Link>
                    <!-- Notifications -->
                    <button
                        type="button"
                        data-dropdown-toggle="notification-dropdown"
                        class="p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                    >
                        <span class="sr-only">View notifications</span>
                        <!-- Bell icon -->
                        <div
                            v-if="unreadCount > 0"
                            class="absolute inline-flex items-center justify-center top-1 w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full dark:border-gray-900"
                        >
                            {{ unreadCount }}
                        </div>
                        <svg
                            aria-hidden="true"
                            class="w-6 h-6"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                            ></path>
                        </svg>
                    </button>
                    <div
                        class="hidden overflow-hidden z-50 my-4 max-w- text-base list-none bg-white divide-y divide-gray-100 shadow-lg dark:divide-gray-600 dark:bg-gray-700 rounded-xl"
                        id="notification-dropdown"
                    >
                        <div
                            class="flex justify-between py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-600 dark:text-gray-300"
                        >
                            <p class="text-lg font-bold">Notifications</p>
                            <button
                                @click="markAllNotificationsAsRead"
                                class="text-sm text-blue-500"
                            >
                                Mark All as Read
                            </button>
                        </div>
                        <div class="overflow-y-auto max-h-60 mr-2">
                            <a
                                v-for="notification in notifs"
                                :key="notification.id"
                                @click="markNotificationAsRead(notification.id)"
                                class="flex py-3 px-4 border-b cursor-pointer hover:bg-gray-100"
                            >
                                <div
                                    v-if="notification.data.user_id"
                                    class="flex-shrink-0"
                                >
                                    <img
                                        class="w-11 h-11 rounded-full"
                                        :src="
                                            getUserProfilePicture(
                                                notification?.data?.user_id
                                            )
                                        "
                                        alt="Notification Avatar"
                                    />
                                </div>
                                <div
                                    v-if="
                                        notification.data.action ===
                                        'Medicine Alert'
                                    "
                                    class="flex items-center"
                                >
                                    <img
                                        class="rounded-full bg-red-100 max-w-11 p-1"
                                        src="/storage/icons/notifs/medicine.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div
                                    v-if="
                                        !notification.data.user_id &&
                                        notification.data.action.includes(
                                            'Request'
                                        )
                                    "
                                    class="flex items-center"
                                >
                                    <img
                                        class="rounded-full bg-orange-100 max-w-11 p-1"
                                        src="/storage/icons/notifs/request.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div
                                    v-if="
                                        !notification.data.user_id &&
                                        notification.data.action.includes(
                                            'Delivery'
                                        )
                                    "
                                    class="flex items-center"
                                >
                                    <img
                                        class="rounded-full bg-green-100 max-w-11 p-1"
                                        src="/storage/icons/notifs/delivery.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div
                                    v-if="
                                        !notification.data.user_id &&
                                        notification.data.action.includes(
                                            'Database'
                                        )
                                    "
                                    class="flex items-center"
                                >
                                    <img
                                        class="rounded-full bg-blue-100 max-w-11 p-1"
                                        src="/storage/icons/notifs/database.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>

                                <div class="pl-3 max-w-72">
                                    <div
                                        v-if="notification?.data?.action"
                                        class="text-gray-800 font-semibold text-md mb-1.5 break-words"
                                    >
                                        <p class="text-gray-700 font-semibold">
                                            {{ notification?.data?.title }}
                                        </p>
                                        <p class="text-gray-500 font-normal">
                                            {{ notification?.data?.message }}
                                        </p>
                                    </div>
                                    <div
                                        class="text-xs font-medium text-primary-600"
                                    >
                                        <formattedDate
                                            :date="`${notification.created_at}`"
                                        ></formattedDate>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div
                            v-if="notifs.length === 0"
                            class="flex flex-col items-center py-10"
                        >
                            <div class="flex justify-center mx-6">
                                <img
                                    class="rounded-full w-1/2"
                                    src="/storage/icons/no-notif.svg"
                                    alt="Notification Avatar"
                                />
                            </div>
                            <div class="text-gray-700 font-normal text-center">
                                <p class="text-gray-900 font-bold text-lg mb-2">
                                    You're all caught up
                                </p>
                                <p>You have seen all new notifications.</p>
                                <p>Come back later.</p>
                            </div>
                        </div>
                        <a
                            href="/notifications"
                            class="block py-2 text-md font-medium text-center text-gray-900 bg-gray-50 hover:bg-gray-100 dark:bg-gray-600 dark:text-white dark:hover:underline"
                        >
                            <div class="inline-flex items-center">
                                <svg
                                    aria-hidden="true"
                                    class="mr-2 w-4 h-4 text-gray-500 dark:text-gray-400"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M10 12a2 2 0 100-4 2 2 0 000 4z"
                                    ></path>
                                    <path
                                        fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                View all
                            </div>
                        </a>
                    </div>
                    <button
                        type="button"
                        class="flex mx-3 text-sm rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                        id="user-menu-button"
                        aria-expanded="false"
                        data-dropdown-toggle="dropdown"
                    >
                        <span class="sr-only">Open user menu</span>
                        <img
                            class="w-8 h-8 rounded-full"
                            :src="
                                user.profile_picture
                                    ? `/storage/${user.profile_picture}`
                                    : '/storage/icons/general/default_profile.svg'
                            "
                            alt="User Profile Picture"
                        />
                    </button>
                    <div
                        class="hidden z-50 my-4 w-56 text-base list-none bg-white divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
                        id="dropdown"
                    >
                        <div class="py-3 px-4">
                            <span
                                class="block text-sm font-semibold text-gray-900 dark:text-white"
                                >{{ user.name }}</span
                            >
                            <span
                                class="block text-sm text-gray-900 truncate dark:text-white"
                                >{{ user.email }}</span
                            >
                        </div>
                        <ul
                            class="py-1 text-gray-700 dark:text-gray-300"
                            aria-labelledby="dropdown"
                        >
                            <li>
                                <Link
                                    :href="route('profile.index')"
                                    as="button"
                                    type="button"
                                    class="w-full text-left py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    >My Profile</Link
                                >
                            </li>
                        </ul>
                        <ul
                            class="py-1 text-gray-700 dark:text-gray-300"
                            aria-labelledby="dropdown"
                        >
                            <li>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    type="button"
                                    class="w-full text-left py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    >Sign out</Link
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Sidebar -->
        <Sidebar />
        <!--Main-->
        <main class="p-4 md:ml-64 mb-20 md:mb-0 h-auto pt-20">
            <ToastList />
            <slot />
        </main>
    </div>
</template>
