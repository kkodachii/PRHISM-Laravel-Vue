import { reactive } from 'vue';

const state = reactive({
    items: []
});

export default {
    get items() {
        return state.items;
    },
    add(toast) {
        state.items.push({
            ...toast,
            key: Date.now() // Ensure unique key
        });
    },
    remove(index) {
        state.items.splice(index, 1);
    }
};
