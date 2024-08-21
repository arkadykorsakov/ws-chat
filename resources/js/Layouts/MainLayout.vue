<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="p-4">
        <div class="mb-4">
            <div>
                <Link :href="route('chats.index')">Chats</Link>
            </div>
        </div>
        <slot />
    </div>
</template>
