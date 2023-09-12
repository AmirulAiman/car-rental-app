<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CarCard from './Components/CarCard.vue'
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
                <a :href="route('cars.create')" class="px-5 py-2 rounded bg-gray-900 text-gray-200 hover:bg-gray-200 hover:text-gray-900 transition-colors duration-300" v-if="($page.props.auth.user!= null && $page.props.auth.user.role == 'owner') ?? false">Register New Car</a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-between shadow-sm sm:rounded-lg px-3 py-1 mb-6">
                    <div class="">
                        <p class="font-semibold text-md">Total Cars Available: {{ total }}</p>
                    </div>
                    <div>
                        <select 
                            name="filterByBrands" 
                            id="filterByBrands" 
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
                <div class="grid grid-cols-4 gap-6" v-if="carList.length > 0">
                    <CarCard :car="car" v-for="car in carList" :key="car.id"/>
                </div>
                <div class="flex justify-center" v-else>
                    <h1>No Car Available</h1>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
