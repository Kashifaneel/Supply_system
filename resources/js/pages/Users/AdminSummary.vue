<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Users, UserPlus, Shield, User } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Props {
    userCount: number;
    adminCount?: number;
    regularUserCount?: number;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'User Summary', href: '/users-summary' },
];
</script>

<template>
    <Head title="User Summary" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">User Summary</h1>
                    <p class="text-muted-foreground">Overview of registered users in the system</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('users.create')">
                        <Button>
                            <UserPlus class="mr-2 h-4 w-4" />
                            Add User
                        </Button>
                    </Link>
                    <Link :href="route('users.index')">
                        <Button variant="outline">
                            <Users class="mr-2 h-4 w-4" />
                            Manage Users
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ userCount }}</div>
                        <p class="text-xs text-muted-foreground">
                            Registered users in the system
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Admin Users</CardTitle>
                        <Shield class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ adminCount || 'N/A' }}</div>
                        <p class="text-xs text-muted-foreground">
                            Users with admin privileges
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Regular Users</CardTitle>
                        <User class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ regularUserCount || 'N/A' }}</div>
                        <p class="text-xs text-muted-foreground">
                            Standard user accounts
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Quick Actions</CardTitle>
                    <CardDescription>
                        Common user management tasks
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-4 sm:flex-row">
                    <Link :href="route('users.create')" class="flex-1">
                        <Button class="w-full" variant="default">
                            <UserPlus class="mr-2 h-4 w-4" />
                            Create New User
                        </Button>
                    </Link>
                    <Link :href="route('users.index')" class="flex-1">
                        <Button class="w-full" variant="outline">
                            <Users class="mr-2 h-4 w-4" />
                            View All Users
                        </Button>
                    </Link>
                </CardContent>
            </Card>

            <!-- Recent Activity or Additional Info -->
            <Card>
                <CardHeader>
                    <CardTitle>User Management</CardTitle>
                    <CardDescription>
                        Manage user accounts and permissions
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="text-sm text-muted-foreground">
                        <p class="mb-2">
                            As an administrator, you can:
                        </p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Create new user accounts</li>
                            <li>Edit existing user information</li>
                            <li>Assign user roles (Admin/User)</li>
                            <li>View user registration details</li>
                        </ul>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
