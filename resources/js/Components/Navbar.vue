<script setup>
import { computed , onMounted, ref, watch } from 'vue'
import { usePage} from '@inertiajs/vue3'


const props = defineProps({
    notifications: Array,
});

const notifs = ref([...props.notifications]);

const page = usePage()

const user = computed(() => page.props.auth.user)

onMounted(() => {
    initFlowbite();
   
});

window.Echo.private(`App.Models.User.${user.id}`)
        .listen('RequestReceived', (event) => {

            console.log(event);
            console.log(event.request);
            console.log(event.Userid);
            
            notifs.value.unshift({
            id: 1,
            data: {message:"hello"},
            created_at: "2024-09-15 08:52:02",
        });

        });

function addNotif() {
    notifs.value.unshift({
            id: 1,  // Use the appropriate data structure
            data: {message:"hello"},
            created_at: "2024-09-15 08:52:02",
});
};

watch(notifs, (newVal) => {
    console.log("may bago", notifs);
});

</script>
<template>
    
</template>