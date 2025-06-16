<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { /* SidebarMenu, SidebarMenuItem */ } from '@/components/ui/sidebar';
import { type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';

const page = usePage();
const user = page.props.auth.user as User;
// Avoid using useSidebar to prevent context issues
const isMobile = false;
// eslint-disable-next-line @typescript-eslint/no-unused-vars
// const state = 'expanded';
</script>

<template>
    <div>
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button size="lg" class="w-full justify-start">
                    <UserInfo :user="user" />
                    <ChevronsUpDown class="ml-auto size-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent
                class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                :side="isMobile ? 'bottom' : 'bottom'"
                align="end"
                :side-offset="4"
            >
                <UserMenuContent :user="user" />
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
