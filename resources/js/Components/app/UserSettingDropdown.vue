<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";

import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const Menus = [
    {
        name: "Profile",
        route: "profile.edit",
        method: "get",
    },
    {
        name: "Logout",
        method: "post",
        route: "logout",
    },
];
</script>

<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-800 rounded-md hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
            >
                {{ $page.props.auth.user.name }}
                <ChevronDownIcon
                    class="w-5 h-5 ml-2 -mr-1 text-gray-800 hover:text-gray-100"
                    aria-hidden="true"
                />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute right-0 w-56 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
                <div class="px-1 py-1">
                    <MenuItem
                        v-for="menu in Menus"
                        :key="menu.name"
                        v-slot="{ active }"
                    >
                        <ResponsiveNavLink :href="route(menu.route)"
                                           :method="menu.method"
                                           :as="menu.method === 'get' ? 'a' : 'button'"
                                           :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']">
                            {{ menu.name }}
                        </ResponsiveNavLink>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
