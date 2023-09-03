<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

import { Head, useForm } from '@inertiajs/vue3';

const {car} = defineProps(['car'])

const form = useForm({
    car_id: car.id,
    start_date: new Date().toDateString,
    end_date: '',
})

const submit = () => {
    form.post(route('cars.book'), {
        onSuccess:() => {
            alert('Book success')
            window.location = route('dashboard')
        },
        onError: (error) => {
            alert('Failed to book')
            form.reset()
            window.location.reload
        }
    })
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Booking You Favourite Car</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="max-w-2xl mx-auto">
                            <form class="" @submit.prevent="submit">
                                <div class="flex">
                                    <div class="w-2/5 flex-col justify-start">
                                        <!-- Car Details -->
                                        <h1 class="font-bold text-center">Car Detail</h1>
                                        <table class="">
                                            <tr>
                                                <td>Car Name:</td>
                                                <td>{{ car.name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Rent Price</td>
                                                <td>RM {{ car.rental_charge }} /Day</td>
                                            </tr>
                                            <tr>
                                                <td>Deposit</td>
                                                <td>RM {{ car.deposit }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="w-fill align-right">
                                        <!-- Renting details -->
                                        <div class="">
                                            <InputLabel for="start_date" value="Rent From" />
                                            <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required autofocus />
                                            <InputError class="mt-2" :message="form.errors.start_date" />
                                        </div>

                                        <div class="mt-4">
                                            <InputLabel for="end_date" value="Until" />
                                            <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" />
                                            <InputError class="mt-2" :message="form.errors.end_date" />
                                        </div>
                                    </div>
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
