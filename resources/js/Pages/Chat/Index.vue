<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
const page = usePage();
const props = defineProps({
    users: Array,
    chats: Array,
});
const title = ref("");
const isGroup = ref(false);
const userIds = ref([]);
function storeGroup(ids) {
    if (userIds.value.length < 2) return;
    router.post("/chats", {
        users: ids,
        title: title.value ? title.value : null,
    });
}
function store(id) {
    router.post("/chats", {
        users: [id],
        title: null,
    });
}
window.Echo.private(`users.${page.props.auth.user.id}`).listen(
    ".store-message-status",
    (res) => {
        props.chats.map((chat) => {
            if (chat.id === res.chat_id) {
                chat.unreadable_count = res.count;
                chat.last_message = res.message;
            }
        });
    }
);
</script>

<template>
    <Head title="Chats" />
    <MainLayout class="w-3/4 mx-auto">
        <div class="flex gap-4 items-start">
            <div class="w-1/2 p-4 bg-white border-gray-200 border">
                <h3 class="text-gray-700 mb-4 text-lg">Users</h3>
                <div v-if="users.length">
                    <Link
                        v-for="(chat, idx) in chats"
                        :href="route('chats.show', chat.id)"
                        :key="idx"
                        class="flex items-center gap-4 mb-4 pb-4 last:mb-0 border-b-[1px] border-gray-300 border-solid w-full"
                    >
                        <div class="w-full">
                            <div class="flex items-center gap-4 mb-2">
                                <p>{{ chat.id }}</p>
                                <p>{{ chat.title }}</p>
                            </div>
                            <div
                                class="flex justify-between gap-2 items-center w-full p-2"
                                :class="[
                                    chat.unreadable_count ? 'bg-sky-50' : '',
                                ]"
                            >
                                <div class="text-sm">
                                    <p class="text-gray-600">
                                        {{ chat.last_message.user_name }}
                                    </p>
                                    <p class="text-gray-500">
                                        {{ chat.last_message.body }}
                                    </p>
                                    <p class="italic text-gray-400">
                                        {{ chat.last_message.time }}
                                    </p>
                                </div>
                                <p
                                    class="text-xs rounded-full bg-sky-500 text-white px-2 py-1"
                                    v-if="chat.unreadable_count"
                                >
                                    {{ chat.unreadable_count }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
            <div class="w-1/2 p-4 bg-white border-gray-200 border">
                <div class="flex items-center justify-between gap-5 mb-4">
                    <h3 class="text-gray-700 text-lg">Users</h3>
                    <button
                        @click="() => (isGroup = true)"
                        v-if="!isGroup"
                        class="inline-block bg-indigo-600 text-white text-xs px-3 py-2 rounded-lg"
                    >
                        Make Group
                    </button>
                    <div class="flex gap-4" v-else>
                        <input
                            type="text"
                            placeholder="title group"
                            v-model="title"
                            class="h-8 border border-gray-300 rounded-full"
                        />
                        <button
                            class="inline-block bg-green-600 text-white text-xs px-3 py-2 rounded-lg disabled:bg-green-100"
                            @click="storeGroup(userIds)"
                            :disabled="userIds.length < 2"
                        >
                            Go Chat
                        </button>
                        <button
                            @click="
                                () => {
                                    userIds = [];
                                    isGroup = false;
                                }
                            "
                            class="inline-block bg-red-600 text-white text-xs px-3 py-2 rounded-lg"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
                <div v-if="users.length">
                    <div
                        v-for="(user, idx) in users"
                        :key="idx"
                        class="flex items-center justify-between gap-4 mb-4 pb-4 last:mb-0 border-b-[1px] border-gray-300 border-solid"
                    >
                        <div class="flex items-center gap-4">
                            <p>{{ user.id }}</p>
                            <p>{{ user.name }}</p>
                            <button
                                @click="store(user.id)"
                                class="inline-block bg-sky-400 text-white text-xs px-3 py-2 rounded-lg"
                            >
                                Message
                            </button>
                        </div>
                        <div v-if="isGroup">
                            <input
                                type="checkbox"
                                v-model="userIds"
                                name="users"
                                :value="user.id"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
