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
        <div class="inline-flex my-2 mx-auto relative bottom-0">
            <a :href="route('cars.edit', car.id)">Update</a>
            <a :href="route('cars.destroy', car.id)">Delete</a>
            <a :href="route('cars.rent', car.id)">Rent</a>
        </div>
    </div>
</template>