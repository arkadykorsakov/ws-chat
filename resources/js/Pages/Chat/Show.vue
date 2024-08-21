<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";
const page = usePage();
const paginationPage = ref(1);
const props = defineProps({
    chat: Object,
    users: Array,
    messages: Array,
    isLastPage: Boolean,
});
const body = ref("");
const userIds = computed(() =>
    props.users
        .map((user) => user.id)
        .filter((userId) => userId !== page.props.auth.user.id)
);
function store() {
    axios
        .post(
            "/messages",
            {
                body: body.value,
                user_ids: userIds.value,
                chat_id: props.chat.id,
            },
            {
                headers: {
                    "X-Socket-Id": window.Echo.socketId(),
                },
            }
        )
        .then((res) => {
            body.value = "";
            props.messages.unshift(res.data);
        });
}
function getMessages() {
    axios
        .get(`/chats/${props.chat.id}?page=${++paginationPage.value}`)
        .then((res) => {
            props.messages.push(...res.data.messages);
            page.props.isLastPage = res.data.is_last_page;
        });
}
window.Echo.channel(`store-message.${props.chat.id}`).listen(
    ".store-message",
    (res) => {
        props.messages.unshift(res.message);
        if (page.url === `/chats/${props.chat.id}`) {
            axios
                .patch(
                    "/message_statuses",
                    {
                        user_id: page.props.auth.user.id,
                        message_id: res.message.id,
                    },
                    {
                        headers: {
                            "X-Socket-Id": window.Echo.socketId(),
                        },
                    }
                )
                .catch((error) => {
                    console.log(error);
                });
        }
    }
);
</script>

<template>
    <Head title="Chat" />
    <MainLayout class="w-3/4 mx-auto">
        <div class="flex gap-4 items-start">
            <div class="w-3/4 p-4 bg-white border-gray-200 border">
                <h3 class="text-gray-700 mb-4 text-lg">
                    {{ chat.title }}
                </h3>
                <div class="mb-4" v-if="messages">
                    <div class="text-center mb-2" v-if="!isLastPage">
                        <button
                            type="button"
                            @click="getMessages"
                            class="inline-block bg-sky-600 text-white text-xs px-3 py-2 rounded-lg self-start"
                        >
                            Load more
                        </button>
                    </div>
                    <div
                        v-for="(message, idx) in messages.slice().reverse()"
                        :key="idx"
                        :class="[
                            'mb-4 last:mb-0',
                            message.is_owner ? 'text-right' : 'text-left',
                        ]"
                    >
                        <div
                            :class="[
                                'p-4 inline-block',
                                message.is_owner
                                    ? 'bg-green-50 border border-green-100 '
                                    : 'bg-sky-50 border border-sky-100 ',
                            ]"
                        >
                            <p class="text-sm">{{ message.user_name }}</p>
                            <p>{{ message.body }}</p>
                            <p class="text-xs italic">{{ message.time }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-gray-700 mb-4 text-lg">Send message</h3>
                    <form class="flex flex-col gap-4" @submit.prevent="store">
                        <input
                            type="text"
                            v-model="body"
                            class="rounded-full border border-gray-400"
                        />
                        <button
                            type="submit"
                            class="inline-block bg-sky-400 text-white text-xs px-3 py-2 rounded-lg self-start"
                        >
                            Send
                        </button>
                    </form>
                </div>
            </div>
            <div class="w-1/4 p-4 bg-white border-gray-200 border">
                <h3 class="text-gray-700 mb-4 text-lg">Users</h3>
                <div v-if="users.length">
                    <div
                        v-for="(user, idx) in users"
                        :key="idx"
                        class="flex items-center gap-4 mb-4 pb-4 last:mb-0 border-b-[1px] border-gray-300 border-solid"
                    >
                        <p>{{ user.id }}</p>
                        <p>{{ user.name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
