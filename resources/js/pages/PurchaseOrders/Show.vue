<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Edit, Download, /* Upload */ } from 'lucide-vue-next';

interface PurchaseOrder {
    id: number;
    po_number: string;
    po_date: string;
    po_image?: string;
    institution_name: string;
    institution_email?: string;
    institution_phone?: string;
    institution_address?: string;
    status: 'Pending' | 'Partially Supplied' | 'Fully Supplied';
    total_amount: number;
    user: {
        name: string;
        email: string;
    };
    items: Array<{
        id: number;
        name: string;
        price: number;
        quantity: number;
        supplied: number;
        batch_no?: string;
        mfg_date?: string;
        exp_date?: string;
        total_amount: number;
        remaining_quantity: number;
    }>;
    supplies?: Array<{
        id: number;
        supply_date: string;
        dc_pdf?: string;
        invoice_pdf?: string;
        user?: {
            name: string;
        };
        items: Array<{
            id?: number;
            quantity: number;
            po_item?: {
                id: number;
                name: string;
                price: number;
            };
        }>;
    }>;
}

interface Props {
    purchaseOrder: PurchaseOrder;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Purchase Orders', href: '/purchase-orders' },
    { title: 'View', href: '#' },
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
</script>

<template>
    <Head :title="`Purchase Order - ${purchaseOrder.po_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ purchaseOrder.po_number }}</h1>
                    <p class="text-muted-foreground">
                        Purchase Order Details
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge :class="getStatusColor(purchaseOrder.status)">
                        {{ purchaseOrder.status }}
                    </Badge>
                    <Link :href="route('purchase-orders.edit', purchaseOrder.id)">
                        <Button variant="outline">
                            <Edit class="mr-2 h-4 w-4" />
                            Edit
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- PO Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Purchase Order Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">PO Number</p>
                            <p class="text-lg font-semibold">{{ purchaseOrder.po_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">PO Date</p>
                            <p>{{ new Date(purchaseOrder.po_date).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Created By</p>
                            <p>{{ purchaseOrder.user.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ purchaseOrder.user.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Total Amount</p>
                            <p class="text-lg font-semibold">PKR {{ Number(purchaseOrder.total_amount || 0).toLocaleString() }}</p>
                        </div>
                        <div v-if="purchaseOrder.po_image">
                            <p class="text-sm font-medium text-muted-foreground">PO Image</p>
                            <a :href="`/storage/${purchaseOrder.po_image}`" target="_blank" class="text-blue-600 hover:underline">
                                View Image
                            </a>
                        </div>
                    </CardContent>
                </Card>

                <!-- Institution Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Institution Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Institution Name</p>
                            <p class="font-semibold">{{ purchaseOrder.institution_name }}</p>
                        </div>
                        <div v-if="purchaseOrder.institution_email">
                            <p class="text-sm font-medium text-muted-foreground">Email</p>
                            <p>{{ purchaseOrder.institution_email }}</p>
                        </div>
                        <div v-if="purchaseOrder.institution_phone">
                            <p class="text-sm font-medium text-muted-foreground">Phone</p>
                            <p>{{ purchaseOrder.institution_phone }}</p>
                        </div>
                        <div v-if="purchaseOrder.institution_address">
                            <p class="text-sm font-medium text-muted-foreground">Address</p>
                            <p class="whitespace-pre-line">{{ purchaseOrder.institution_address }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Items -->
            <Card>
                <CardHeader>
                    <CardTitle>Items</CardTitle>
                    <CardDescription>
                        List of items in this purchase order
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-2">Item Name</th>
                                    <th class="text-left p-2">Batch No</th>
                                    <th class="text-left p-2">Mfg Date</th>
                                    <th class="text-left p-2">Exp Date</th>
                                    <th class="text-right p-2">Price</th>
                                    <th class="text-right p-2">Quantity</th>
                                    <th class="text-right p-2">Supplied</th>
                                    <th class="text-right p-2">Remaining</th>
                                    <th class="text-right p-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in purchaseOrder.items" :key="item.id" class="border-b">
                                    <td class="p-2 font-medium">{{ item.name }}</td>
                                    <td class="p-2">{{ item.batch_no || '-' }}</td>
                                    <td class="p-2">{{ item.mfg_date ? new Date(item.mfg_date).toLocaleDateString() : '-' }}</td>
                                    <td class="p-2">{{ item.exp_date ? new Date(item.exp_date).toLocaleDateString() : '-' }}</td>
                                    <td class="p-2 text-right">PKR {{ Number(item.price).toLocaleString() }}</td>
                                    <td class="p-2 text-right">{{ item.quantity }}</td>
                                    <td class="p-2 text-right">{{ item.supplied }}</td>
                                    <td class="p-2 text-right">{{ item.remaining_quantity }}</td>
                                    <td class="p-2 text-right font-semibold">PKR {{ Number(item.total_amount).toLocaleString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Supplies -->
            <Card v-if="purchaseOrder.supplies && purchaseOrder.supplies.length > 0">
                <CardHeader>
                    <CardTitle>Supply History</CardTitle>
                    <CardDescription>
                        List of supplies made for this purchase order
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-for="supply in purchaseOrder.supplies" :key="supply.id" class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="font-semibold">Supply #{{ supply.id }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ new Date(supply.supply_date).toLocaleDateString() }}
                                    {{ supply.user?.name ? `by ${supply.user.name}` : '' }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a v-if="supply.dc_pdf" :href="`/storage/${supply.dc_pdf}`" target="_blank">
                                    <Button variant="outline" size="sm">
                                        <Download class="mr-2 h-4 w-4" />
                                        DC
                                    </Button>
                                </a>
                                <a v-if="supply.invoice_pdf" :href="`/storage/${supply.invoice_pdf}`" target="_blank">
                                    <Button variant="outline" size="sm">
                                        <Download class="mr-2 h-4 w-4" />
                                        Invoice
                                    </Button>
                                </a>
                            </div>
                        </div>
                        <div class="text-sm">
                            <p class="font-medium mb-2">Items Supplied:</p>
                            <ul class="space-y-1">
                                <li v-for="item in supply.items" :key="item.po_item?.id || item.id" class="flex justify-between">
                                    <span>{{ item.po_item?.name || 'Unknown Item' }}</span>
                                    <span>
                                        {{ item.quantity }} × PKR {{ Number(item.po_item?.price || 0).toLocaleString() }} = 
                                        PKR {{ Number((item.quantity || 0) * (item.po_item?.price || 0)).toLocaleString() }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Actions -->
            <div class="flex justify-end gap-4">
                <Link :href="route('purchase-orders.index')">
                    <Button variant="outline">
                        Back to List
                    </Button>
                </Link>
                <Link v-if="purchaseOrder.status !== 'Fully Supplied'" :href="route('supplies.create', { po: purchaseOrder.id })">
                    <Button>
                        Create Supply
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
