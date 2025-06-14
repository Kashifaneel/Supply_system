<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Eye, Edit, Trash2 } from 'lucide-vue-next';

interface PurchaseOrder {
    id: number;
    po_number: string;
    po_date: string;
    institution_name: string;
    status: 'Pending' | 'Partially Supplied' | 'Fully Supplied';
    total_amount: number;
    user: {
        name: string;
    };
    items_count?: number;
}

interface Props {
    purchaseOrders: {
        data: PurchaseOrder[];
        links: any[];
        meta: any;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Purchase Orders', href: '/purchase-orders' },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'Pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'Partially Supplied':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'Fully Supplied':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
};

const deletePO = (id: number) => {
    if (confirm('Are you sure you want to delete this purchase order?')) {
        router.delete(route('purchase-orders.destroy', id));
    }
};
</script>

<template>
    <Head title="Purchase Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Purchase Orders</h1>
                    <p class="text-muted-foreground">
                        Manage your purchase orders and track their status
                    </p>
                </div>
                <Link :href="route('purchase-orders.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add PO
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4">
                <Card v-for="po in purchaseOrders.data" :key="po.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="text-lg">{{ po.po_number }}</CardTitle>
                                <CardDescription>
                                    {{ po.institution_name }} • {{ new Date(po.po_date).toLocaleDateString() }}
                                </CardDescription>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge :class="getStatusColor(po.status)">
                                    {{ po.status }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-muted-foreground">
                                <p>Created by: {{ po.user.name }}</p>
                                <p>Total Amount: PKR {{ Number(po.total_amount || 0).toLocaleString() }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link :href="route('purchase-orders.show', po.id)">
                                    <Button variant="outline" size="sm">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Link :href="route('purchase-orders.edit', po.id)">
                                    <Button variant="outline" size="sm">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Button variant="outline" size="sm" @click="deletePO(po.id)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div v-if="purchaseOrders.data.length === 0" class="text-center py-12">
                    <p class="text-muted-foreground">No purchase orders found.</p>
                    <Link :href="route('purchase-orders.create')" class="mt-4">
                        <Button>Add your first PO</Button>
                    </Link>
                </div>
            </div>

            <!-- Pagination would go here -->
        </div>
    </AppLayout>
</template>
