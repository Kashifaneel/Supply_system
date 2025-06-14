<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';

interface PurchaseOrder {
    id: number;
    po_number: string;
    institution_name: string;
    items: Array<{
        id: number;
        name: string;
        price: number;
        quantity: number;
        supplied: number;
        remaining_quantity: number;
        batch_no?: string;
        mfg_date?: string;
        exp_date?: string;
    }>;
}

interface Props {
    purchaseOrders: PurchaseOrder[];
    selectedPO?: number;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Supplies', href: '/supplies' },
    { title: 'Create', href: '/supplies/create' },
];

const selectedPOId = ref<number | null>(props.selectedPO || null);
const selectedPO = computed(() => {
    return props.purchaseOrders.find(po => po.id === selectedPOId.value) || null;
});

const form = useForm({
    purchase_order_id: null as number | null,
    supply_date: new Date().toISOString().split('T')[0],
    items: [] as Array<{
        po_item_id: number;
        quantity: number;
        max_quantity: number;
        item_name: string;
        price: number;
    }>
});

watch(selectedPOId, (newPOId) => {
    if (newPOId && selectedPO.value) {
        form.purchase_order_id = newPOId;
        form.items = selectedPO.value.items
            .filter(item => item.remaining_quantity > 0)
            .map(item => ({
                po_item_id: item.id,
                quantity: 1, // Set minimum quantity to 1
                max_quantity: item.remaining_quantity,
                item_name: item.name,
                price: item.price,
            }));
    } else {
        form.items = [];
    }
});

const updateItemQuantity = (index: number, quantity: number) => {
    const item = form.items[index];
    if (quantity > item.max_quantity) {
        form.items[index].quantity = item.max_quantity;
    } else if (quantity < 1) { // Minimum quantity is 1
        form.items[index].quantity = 1;
    } else {
        form.items[index].quantity = quantity;
    }
};

const calculateTotal = () => {
    return form.items.reduce((total, item) => total + (item.quantity * item.price), 0);
};

const getSupplyItems = () => {
    return form.items.filter(item => item.quantity > 0);
};

const submit = () => {
    const supplyItems = getSupplyItems();
    if (supplyItems.length === 0) {
        alert('Please select at least one item to supply.');
        return;
    }

    form.items = supplyItems;
    form.post(route('supplies.store'));
};

// Initialize form if PO is pre-selected
onMounted(() => {
    if (props.selectedPO && props.purchaseOrders.length > 0) {
        const preSelectedPO = props.purchaseOrders.find(po => po.id === props.selectedPO);
        if (preSelectedPO) {
            selectedPOId.value = props.selectedPO;
        }
    }
});
</script>

<template>
    <Head title="Create Supply" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Create Supply</h1>
                <p class="text-muted-foreground">
                    Create a new supply for an existing purchase order
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Supply Details</CardTitle>
                        <CardDescription>
                            Select the purchase order and supply date
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="purchase_order">Purchase Order</Label>
                                <select
                                    v-model="selectedPOId"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': form.errors.purchase_order_id }"
                                >
                                    <option value="">Select Purchase Order</option>
                                    <option v-for="po in purchaseOrders" :key="po.id" :value="po.id">
                                        {{ po.po_number }} - {{ po.institution_name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.purchase_order_id" class="text-sm text-red-500">
                                    {{ form.errors.purchase_order_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="supply_date">Supply Date</Label>
                                <Input
                                    id="supply_date"
                                    type="date"
                                    v-model="form.supply_date"
                                    :class="{ 'border-red-500': form.errors.supply_date }"
                                />
                                <p v-if="form.errors.supply_date" class="text-sm text-red-500">
                                    {{ form.errors.supply_date }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="selectedPO">
                    <CardHeader>
                        <CardTitle>Items to Supply</CardTitle>
                        <CardDescription>
                            Select quantities for items from {{ selectedPO.po_number }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="(item, index) in form.items" :key="item.po_item_id" class="border rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                    <div>
                                        <Label>Item Name</Label>
                                        <p class="font-medium">{{ item.item_name }}</p>
                                    </div>

                                    <div>
                                        <Label>Price</Label>
                                        <p>PKR {{ Number(item.price || 0).toLocaleString() }}</p>
                                    </div>

                                    <div>
                                        <Label>Available</Label>
                                        <p class="text-sm text-muted-foreground">{{ item.max_quantity || 0 }} remaining</p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label>Supply Quantity</Label>
                                        <Input
                                            type="number"
                                            :min="1"
                                            :max="item.max_quantity"
                                            :value="item.quantity"
                                            @input="updateItemQuantity(index, parseInt($event.target.value) || 1)"
                                            placeholder="1"
                                        />
                                    </div>
                                </div>                                    <div v-if="item.quantity > 0" class="mt-4 pt-4 border-t">
                                    <div class="text-right">
                                        <p class="text-sm text-muted-foreground">
                                            Subtotal: PKR {{ Number((item.quantity || 0) * (item.price || 0)).toLocaleString() }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="getSupplyItems().length > 0" class="border-t pt-4">
                                <div class="text-right">
                                    <p class="text-lg font-semibold">
                                        Total Supply Amount: PKR {{ Number(calculateTotal() || 0).toLocaleString() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div v-if="!selectedPO && purchaseOrders.length === 0" class="text-center py-12">
                    <p class="text-muted-foreground">No purchase orders available for supply.</p>
                    <p class="text-sm text-muted-foreground mt-2">
                        All purchase orders are either fully supplied or no orders exist.
                    </p>
                </div>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="$inertia.visit(route('supplies.index'))">
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :disabled="form.processing || !selectedPO || getSupplyItems().length === 0"
                    >
                        {{ form.processing ? 'Creating...' : 'Create Supply' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
