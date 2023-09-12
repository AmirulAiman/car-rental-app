<script setup>
const { car } = defineProps(['car'])
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-3 py-1">
        <img class="mb-6 h-auto w-full image-cover rounded-md bg-gray-300" :src="car.img_url" :alt="car.name">
        <div class="flex-col flex mx-auto my-3">
            <h1 class="text-center font-bold">{{ car.name }}</h1>
            <h1 class="inline-flex">Deposit: RM {{ car.deposit }}</h1>
            <h1 class="inline-flex">Rental Rate: RM {{ car.rental_charge }}/Day</h1>
        </div>
        <div class="inline-flex my-2 mx-auto">
            <a class="rounded-md mx-5 px-5 py-2 w-full bg-gray-500 text-gray-200 hover:bg-gray-200 hover:text-gray-900 transition-colors duration-300" :href="route('cars.edit', car.id)" v-if="$page.props.auth.user?.id == car.user_id">Update</a>
            <a class="rounded-md mx-5 px-5 py-2 w-1/2 bg-red-500 text-red-200 hover:bg-red-200 hover:text-red-900 transition-colors duration-300" :href="route('cars.destroy', car.id)" v-if="$page.props.auth.user?.id == car.user_id">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </a>
            <a class="rounded-md px-5 py-2 w-full bg-blue-500 text-blue-200 hover:bg-blue-200 hover:text-500 transition-all duration-300" :href="route('cars.rent', car.id)" v-if="$page.props.auth.user?.role == 'customer'">Rent</a>
        </div>
    </div>
</template>