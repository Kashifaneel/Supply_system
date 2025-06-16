<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, /* SidebarGroup, SidebarGroupLabel, */ SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Folder, LayoutGrid, ShoppingCart, Truck, CreditCard, Package, Users, UserCog } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = page.props.auth?.user;

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Purchase Orders',
        href: '/purchase-orders',
        icon: ShoppingCart,
    },
    {
        title: 'Supplies',
        href: '/supplies',
        icon: Truck,
    },
    {
        title: 'Payments',
        href: '/payments',
        icon: CreditCard,
    },
    {
        title: 'User Summary',
        href: '/users-summary',
        icon: Users,
    },
    {
        title: 'Manage Users',
        href: '/users',
        icon: UserCog,
    },
];

const mainNavItems: NavItem[] = user && user.role === 'Admin' 
    ? allNavItems 
    : allNavItems.filter(item => item.title !== 'User Summary' && item.title !== 'Manage Users');

const footerNavItems: NavItem[] = [
    {
        title: 'Supply Management',
        href: '#',
        icon: Package,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
