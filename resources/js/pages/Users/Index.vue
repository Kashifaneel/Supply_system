<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Plus, Edit, /* Trash2 */ } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface User {
    id: number;
    name: string;
    email: string;
    role: 'Admin' | 'User';
    created_at: string;
}

interface Props {
    users: User[];
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/users' },
];

const getRoleColor = (role: User['role']) => {
    switch (role) {
        case 'Admin':
            return 'bg-red-100 text-red-800 hover:bg-red-200';
        case 'User':
            return 'bg-blue-100 text-blue-800 hover:bg-blue-200';
        default:
            return 'bg-gray-100 text-gray-800 hover:bg-gray-200';
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">User Management</h1>
                    <p class="text-muted-foreground">Manage system users and their roles</p>
                </div>
                <Link :href="route('users.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add User
                    </Button>
                </Link>
            </div>

            <div class="rounded-lg border bg-card">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="px-6 py-3 text-left text-sm font-medium">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium">
                                Joined
                            </th>
                            <th class="px-6 py-3 text-right text-sm font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-b">
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ user.name }}</div>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4">
                                <Badge :class="getRoleColor(user.role)">
                                    {{ user.role }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('users.edit', user.id)" class="inline-block">
                                    <Button variant="outline" size="sm">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="users.length === 0" class="text-center py-12">
                <p class="text-muted-foreground">No users found.</p>
                <Link :href="route('users.create')" class="mt-4 inline-block">
                    <Button>Add your first user</Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
