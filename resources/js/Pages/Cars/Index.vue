<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const search = ref('');
const filter = ref('');
const total = ref(0);

const {cars, brands, isAdmin, canBook} = defineProps(['cars','brands', 'isAdmin','canBook']);
const carList = computed(() => {
    if(search.value){
        return cars.filter((c) => c.name.includes(search.value))
    }
    if(filter.value){
        return cars.filter((c) => c.brand == filter.value)
    }
    return cars
})

total.value = computed(() => cars.length)
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cars</h2>
                <a :href="route('cars.create')" class="px-5 py-2 rounded bg-gray-900 text-gray-200 hover:bg-gray-200 hover:text-gray-900 transition-colors duration-300" v-if="$page.props.auth.user.role == 'owner' ?? false">Register New Car</a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-between shadow-sm sm:rounded-lg px-3 py-1 mb-6">
                    <div class="item-center">
                            <p class="font-semibold text-md">Total Cars Available: {{ total }}</p>
                        </div>
                        <div>
                            <select 
                                name="roleFilter" 
                                id="roleFilter" 
                                v-model="filter"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">-- Filter By Brands --</option>
                                <option 
                                    :key="brand.id" 
                                    :value="brand.value" 
                                    v-for="brand in brands"
                                    class=""
                                >{{ brand.label }}</option>
                            </select>
                        </div>
                        <div class="inline-flex">
                            <TextInput
                                id="searchFilter"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="search"
                                placeholder="Search Name..."
                            />
                        </div>
                </div>
                <div class="grid grid-cols-4 gap-2" v-if="carList.length > 0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-3 py-1" v-for="car in carList" :key="car.id">
                        <div class="p-6 text-gray-900 text-center font-bold">{{ car.name }}</div>
                        <div class="flex justify-between">
                            <div class="px-2 text-gray-900 rounded-md bg-gray-300 w-lg-1/2 w-md-1/3 w-sm-full">
                                <h1 class="font-semibold text-center">Deposit</h1>
                                <h1 class="text-center">RM {{ car.deposit }}</h1>
                            </div>
                            <div class="px-2 text-gray-900 rounded-md bg-gray-300 w-fill w-sm-full">
                                <h1 class="font-semibold text-center">Rental Charge</h1>
                                <h1 class="text-center">RM {{ car.rental_charge }}/Daily</h1>
                            </div>
                        </div>
                        <div class="flex justify-end p-3">
                            <a :href="route('cars.rent', car.id)" class="rounded px-3 py-1 bg-blue-800 text-blue-500 hover:bg-blue-500 hover:text-blue-800 mr-2" v-if="$page.props.auth.user.role == 'customer' && canBook">
                                <i class="fa-regular fa-bookmark"></i>
                                Rent
                            </a>
                            <a :href="route('cars.edit', car.id)" class="rounded px-3 py-1 bg-orange-900 text-orange-300 hover:bg-orange-300 hover:text-orange-900 mr-2" v-if="$page.props.auth.user.role == 'owner'">
                                <i class="fa-solid fa-pen-nib"></i>
                                Edit
                            </a>
                            <a :href="route('cars.destroy', car.id)" class="rounded px-3 py-1 bg-red-500 text-red-200 mr-2 hover:bg-red-500 hover:text-red-800 transition-all duration-300" v-if="$page.props.auth.user.role == 'owner'">
                                <i class="fa-solid fa-trash"></i>
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center" v-else>
                    <h1>No Car Available</h1>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
