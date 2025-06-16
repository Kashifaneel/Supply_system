<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { /* Badge */ } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Eye, Download } from 'lucide-vue-next';

interface Supply {
    id: number;
    supply_date: string;
    dc_pdf?: string;
    invoice_pdf?: string;
    total_amount?: number | null;
    purchase_order: {
        po_number: string;
        institution_name: string;
        status: string;
    };
    user: {
        name: string;
    };
    items: Array<{
        quantity: number;
        po_item: {
            name: string;
            price: number;
        };
    }>;
}

interface Props {
    supplies: {
        data: Supply[];
        links: any[];
        meta: any;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Supplies', href: '/supplies' },
];
</script>

<template>
    <Head title="Supplies" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Supplies</h1>
                    <p class="text-muted-foreground">
                        Manage your supplies and track deliveries
                    </p>
                </div>
                <Link :href="route('supplies.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Create Supply
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4">
                <Card v-for="supply in supplies.data" :key="supply.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="text-lg">Supply #{{ supply.id }}</CardTitle>
                                <CardDescription>
                                    {{ supply.purchase_order.po_number }} • {{ supply.purchase_order.institution_name }}
                                </CardDescription>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-muted-foreground">{{ new Date(supply.supply_date).toLocaleDateString() }}</p>
                                <p class="font-semibold">PKR {{ Number(supply.total_amount || 0).toLocaleString() }}</p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-muted-foreground">
                                <p>Supplied by: {{ supply.user.name }}</p>
                                <p>Items: {{ supply.items.length }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link :href="route('supplies.show', supply.id)">
                                    <Button variant="outline" size="sm">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
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
                    </CardContent>
                </Card>

                <div v-if="supplies.data.length === 0" class="text-center py-12">
                    <p class="text-muted-foreground">No supplies found.</p>
                    <Link :href="route('supplies.create')" class="mt-4">
                        <Button>Create your first supply</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
