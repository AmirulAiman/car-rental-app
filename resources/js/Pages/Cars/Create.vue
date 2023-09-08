<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const {brands, properties, statuses, rental_type} = defineProps(['brands','properties','statuses','rental_type']);

const form = useForm({
    name:'',
    plate_number:'',
    brand:'',
    deposit:'',
    rental_charge:'',
    rental_charge_type:'daily',
    status:'available',
    property:[],
    img:null
})

const onSubmit = () => {
    form.post(route('cars.store'),{
        onSuccess:(res) => {
            console.log('res',res);
            alert('Car registered.')
        },
        onError: (err) => {
            alert('Failed to register')
            console.error(err.message)
        },
        onFinish:() => {
            form.defaults()
        }
    })
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Register Your Car for Rental</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="onSubmit" class="max-w-7xl mx-auto p-5">
                        <div class="mb-6">
                            <InputLabel class="text-md font-semibold" for="name" value="Car Name" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div class="mb-6">
                            <InputLabel class="text-md font-semibold" for="plate_number" value="Car Plate Number" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.plate_number" required />
                            <InputError class="mt-2" :message="form.errors.plate_number" />
                        </div>
                        <div class="mb-6">
                            <InputLabel class="text-md font-semibold" for="brand" value="Car Brand" />
                            <select 
                                name="brand" 
                                id="brand" 
                                v-model="form.brand"
                                class="border-gray-300 w-full focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">-- Car Brands --</option>
                                <option 
                                    :key="brand.id" 
                                    :value="brand.value" 
                                    v-for="brand in brands"
                                    class=""
                                >{{ brand.label }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.brand" />
                        </div>
                        <div class="mb-6">
                            <InputLabel class="text-md font-semibold" for="deposit" value="Car Deposit" />
                            <TextInput id="deposit" type="number" class="mt-1 block w-full" step="0.10" v-model="form.deposit" required />
                            <InputError class="mt-2" :message="form.errors.deposit" />
                        </div>
                        <div class="mb-6">
                            <InputLabel class="text-md font-semibold" for="deposit" value="Car Rental Charge" />
                            <div class="flex items-center justify-between">
                                <TextInput id="deposit" type="number" class="mt-1 block w-full" step="0.10" v-model="form.rental_charge" required />
                                <select 
                                name="brand" 
                                id="brand" 
                                v-model="form.rental_charge_type"
                                class="border-gray-300 w-1/2 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">-- Rental Type --</option>
                                <option 
                                    :key="t.id" 
                                    :value="t.value" 
                                    v-for="t in rental_type"
                                    class=""
                                >{{ t.label }}</option>
                            </select>
                            </div>
                            <InputError class="mt-2" :message="form.errors.rental_charge" />
                            <InputError class="mt-2" :message="form.errors.rental_charge_type" />
                        </div>

                        <div class="mb-6">
                            <InputLabel class="text-md font-semibold" for="car-property" value="Car Properties"/>
                            <div class="grid gap-1 grid-cols-8">
                                <div class="relative flex items-start py-4 ml-2" v-for="property in properties" :key="property.id">
                                    <input :id="property.id" type="checkbox" class="hidden peer" v-model="form.property" :value="property.value">
                                    <label :for="property.id" class="inline-flex items-center justify-between w-auto p-2 font-medium tracking-tight border rounded-lg cursor-pointer bg-brand-light text-brand-black border-violet-500 peer-checked:border-violet-400 peer-checked:bg-violet-700 peer-checked:text-white peer-checked:font-semibold peer-checked:underline peer-checked:decoration-brand-dark decoration-2">
                                        <div class="flex items-center justify-center w-full">
                                            <div class="text-sm text-brand-black">{{ property.label }}</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.property" />
                        </div>

                        <div class="w-fill">
                            <InputLabel class="text-md font-semibold" for="car-property" value="Car Image"/>
                            <TextInput 
                                id="img" 
                                type="file" 
                                class="mt-1 block w-full" 
                                @input="form.img = $event.target.files[0]"
                            />
                            <InputError class="mt-2" :message="form.errors.img" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                :href="route('cars.index')">Cancel</a>
                            <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Submit
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
