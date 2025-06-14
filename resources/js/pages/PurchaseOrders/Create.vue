<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Purchase Orders', href: '/purchase-orders' },
    { title: 'Create', href: '/purchase-orders/create' },
];

interface FormErrors {
    po_number?: string;
    po_date?: string;
    po_image?: string;
    institution_name?: string;
    institution_email?: string;
    institution_phone?: string;
    institution_address?: string;
    items?: string[];
    [key: `items.${number}.name`]: string;
    [key: `items.${number}.price`]: string;
    [key: `items.${number}.quantity`]: string;
}

interface Item {
    name: string;
    price: number;
    quantity: number;
    batch_no: string;
    mfg_date: string;
    exp_date: string;
}

interface FormData {
    po_number: string;
    po_date: string;
    po_image: File | null;
    institution_name: string;
    institution_email: string;
    institution_phone: string;
    institution_address: string;
    items: Item[];
}

const form = useForm({
    po_number: '',
    po_date: new Date().toISOString().split('T')[0],
    po_image: null as File | null,
    institution_name: '',
    institution_email: '',
    institution_phone: '',
    institution_address: '',
    items: [
        {
            name: '',
            price: 0,
            quantity: 1,
            batch_no: '',
            mfg_date: '',
            exp_date: '',
        }
    ]
} as any);

const addItem = () => {
    if (Array.isArray(form.items)) {
        form.items.push({
            name: '',
            price: 0,
            quantity: 1,
            batch_no: '',
            mfg_date: '',
            exp_date: '',
        });
    }
};

const removeItem = (index: number) => {
    if (Array.isArray(form.items) && form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const calculateTotal = (item: Item) => {
    return (item.price * item.quantity).toFixed(2);
};

const calculateGrandTotal = () => {
    if (Array.isArray(form.items)) {
        return form.items.reduce((total: number, item: Item) => total + (item.price * item.quantity), 0).toFixed(2);
    }
    return '0.00';
};

const submit = () => {
    const formData = new FormData();
    formData.append('po_number', form.po_number);
    formData.append('po_date', form.po_date);
    if (form.po_image) {
        formData.append('po_image', form.po_image);
    }
    formData.append('institution_name', form.institution_name);
    formData.append('institution_email', form.institution_email);
    formData.append('institution_phone', form.institution_phone);
    formData.append('institution_address', form.institution_address);
    if (Array.isArray(form.items)) {
        form.items.forEach((item: Item, index: number) => {
            formData.append(`items[${index}][name]`, item.name);
            formData.append(`items[${index}][price]`, item.price.toString());
            formData.append(`items[${index}][quantity]`, item.quantity.toString());
            formData.append(`items[${index}][batch_no]`, item.batch_no);
            formData.append(`items[${index}][mfg_date]`, item.mfg_date);
            formData.append(`items[${index}][exp_date]`, item.exp_date);
        });
    }
    form.post(route('purchase-orders.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Handle success if needed
        },
        onError: () => {
            // Handle error if needed
        }
    });
};
</script>

<template>
    <Head title="Create Purchase Order" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Add Purchase Order</h1>
                <p class="text-muted-foreground">
                    Add a new purchase order with items and institution details
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Institution Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Institution Details</CardTitle>
                        <CardDescription>
                            Enter the details of the institution/hospital
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="po_number">PO Number</Label>
                                <Input
                                    id="po_number"
                                    type="text"
                                    v-model="form.po_number"
                                    placeholder="Enter PO number"
                                    :class="{ 'border-red-500': form.errors.po_number }"
                                />
                                <p v-if="form.errors.po_number" class="text-sm text-red-500">
                                    {{ form.errors.po_number }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="po_date">PO Date</Label>
                                <Input
                                    id="po_date"
                                    type="date"
                                    v-model="form.po_date"
                                    :class="{ 'border-red-500': form.errors.po_date }"
                                />
                                <p v-if="form.errors.po_date" class="text-sm text-red-500">
                                    {{ form.errors.po_date }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="po_image">PO Image</Label>
                                <Input
                                    id="po_image"
                                    type="file"
                                    accept="image/*"
                                    @input="form.po_image = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.po_image }"
                                />
                                <p v-if="form.errors.po_image" class="text-sm text-red-500">
                                    {{ form.errors.po_image }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="institution_name">Institution Name</Label>
                            <Input
                                id="institution_name"
                                v-model="form.institution_name"
                                placeholder="Enter institution/hospital name"
                                :class="{ 'border-red-500': form.errors.institution_name }"
                            />
                            <p v-if="form.errors.institution_name" class="text-sm text-red-500">
                                {{ form.errors.institution_name }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="institution_email">Email</Label>
                                <Input
                                    id="institution_email"
                                    type="email"
                                    v-model="form.institution_email"
                                    placeholder="institution@example.com"
                                    :class="{ 'border-red-500': form.errors.institution_email }"
                                />
                                <p v-if="form.errors.institution_email" class="text-sm text-red-500">
                                    {{ form.errors.institution_email }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="institution_phone">Phone</Label>
                                <Input
                                    id="institution_phone"
                                    v-model="form.institution_phone"
                                    placeholder="Phone number"
                                    :class="{ 'border-red-500': form.errors.institution_phone }"
                                />
                                <p v-if="form.errors.institution_phone" class="text-sm text-red-500">
                                    {{ form.errors.institution_phone }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="institution_address">Address</Label>
                            <Textarea
                                id="institution_address"
                                v-model="form.institution_address"
                                placeholder="Enter full address"
                                :class="form.errors.institution_address ? 'border-red-500' : ''"
                            />
                            <p v-if="form.errors.institution_address" class="text-sm text-red-500">
                                {{ form.errors.institution_address }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Items -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Items</CardTitle>
                                <CardDescription>
                                    Add items to this purchase order
                                </CardDescription>
                            </div>
                            <Button type="button" @click="addItem" variant="outline">
                                <Plus class="mr-2 h-4 w-4" />
                                Add Item
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-for="(item, index) in form.items" :key="index" class="border rounded-lg p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium">Item {{ index + 1 }}</h4>
                                <Button
                                    v-if="form.items.length > 1"
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="removeItem(index)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <Label>Item Name</Label>
                                    <Input
                                        v-model="item.name"
                                        placeholder="Enter item name"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.name`] }"
                                    />
<p v-if="form.errors && form.errors[`items.${index}.name`]" class="text-sm text-red-500">
                                    {{ form.errors[`items.${index}.name`] }}
                                </p>
                                </div>

                                <div class="space-y-2">
                                    <Label>Price</Label>
                                    <Input
                                        type="number"
                                        step="0.01"
                                        v-model="item.price"
                                        placeholder="0.00"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.price`] }"
                                    />
                                    <p v-if="form.errors && form.errors[`items.${index}.price`]" class="text-sm text-red-500">
                                        {{ form.errors[`items.${index}.price`] }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label>Quantity</Label>
                                    <Input
                                        type="number"
                                        v-model="item.quantity"
                                        placeholder="1"
                                        :class="{ 'border-red-500': form.errors[`items.${index}.quantity`] }"
                                    />
                                    <p v-if="form.errors && form.errors[`items.${index}.quantity`]" class="text-sm text-red-500">
                                        {{ form.errors[`items.${index}.quantity`] }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <Label>Batch No</Label>
                                    <Input
                                        v-model="item.batch_no"
                                        placeholder="Batch number"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label>Mfg Date</Label>
                                    <Input
                                        type="date"
                                        v-model="item.mfg_date"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label>Exp Date</Label>
                                    <Input
                                        type="date"
                                        v-model="item.exp_date"
                                    />
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-sm text-muted-foreground">
                                    Total: ₹{{ calculateTotal(item) }}
                                </p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="text-right">
                                <p class="text-lg font-semibold">
                                    Grand Total: ₹{{ calculateGrandTotal() }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" @click="$inertia.visit(route('purchase-orders.index'))">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Add Purchase Order' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
