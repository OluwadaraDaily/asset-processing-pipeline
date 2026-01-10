<script setup lang="ts">
import { destroy } from '@/actions/App/Http/Controllers/Auth/LogoutController';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { router, usePage } from '@inertiajs/vue3';
import { LogOut, User } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user);

const initials = computed(() => {
    if (!user.value) return '';
    const firstName = user.value.first_name || '';
    const lastName = user.value.last_name || '';
    return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
});

const fullName = computed(() => {
    if (!user.value) return '';
    return `${user.value.first_name || ''} ${user.value.last_name || ''}`.trim();
});

function handleLogout() {
    router.post(
        destroy.url(),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // Redirect will be handled by the controller
            },
        },
    );
}
</script>

<template>
    <DropdownMenu v-if="user">
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                class="relative size-10 rounded-full"
            >
                <Avatar class="size-10">
                    <AvatarFallback class="bg-gradient-to-br from-blue-500 to-purple-600 text-white">
                        {{ initials }}
                    </AvatarFallback>
                </Avatar>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
            class="w-56"
            align="end"
        >
            <DropdownMenuLabel class="font-normal">
                <div class="flex flex-col gap-1">
                    <p class="text-sm leading-none font-medium">
                        {{ fullName }}
                    </p>
                    <p class="text-xs leading-none text-gray-500">
                        {{ user.email }}
                    </p>
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem
                class="cursor-pointer"
                @click="handleLogout"
            >
                <LogOut class="mr-2 size-4" />
                <span>Logout</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
    <div v-else>
        <Button
            variant="ghost"
            class="relative size-10 rounded-full"
            as-child
        >
            <a href="/login">
                <User class="size-5" />
            </a>
        </Button>
    </div>
</template>
