<script setup>
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import CreateNewDropdown from "@/Components/app/CreateNewDropdown.vue";
import NavLink from "@/Components/NavLink.vue";

const menuList = [
    {
        name: "My Files",
        route: "myFiles",
    },
    // {
    //     name: "Shared with me",
    //     route: "file.sharedWithMe",
    // },
    // {
    //     name: "Shared by me",
    //     route: "file.sharedByMe",
    // },
    // {
    //     name: "Trash",
    //     route: "trash",
    // },
];
</script>

<template>
    <nav class="min-w-[200px]">
        <Link
            :href="route('myFiles')"
            class="h-[80px] px-3 flex items-center gap-3"
        >
            <ApplicationLogo
                class="block w-auto text-gray-800 fill-current h-9"
            />
            LaraDrive
        </Link>

        <div class="px-3">
            <CreateNewDropdown />

            <div class="py-3">
                <NavLink
                    v-for="menu in menuList"
                    :key="menu.name"
                    :href="route(menu.route)"
                    :class="{
                        'text-blue-500': $page.props.route_name === menu.route,
                    }"
                >
                    {{ menu.name }}
                </NavLink>
            </div>
        </div>
    </nav>
</template>
