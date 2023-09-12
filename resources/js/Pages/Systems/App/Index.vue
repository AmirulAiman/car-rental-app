<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue'

const openModel = ref(false);
const edit = ref(null);

const {data, groups} = defineProps(['data','groups']);

const formatLabel = (text) => text.replace('_',' ')

const toggleEdit = (e) => alert(e.target.value)

const updateData = () => {

}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">App Library</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">#</th>
                                <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">Group</th>
                                <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">Label</th>
                                <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-right text-gray-700 uppercase dark:text-gray-400">Value</th>
                                <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">Index</th>
                                <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400">Indicator</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="py-3 cursor-pointer hover:bg-gray-200" v-for="d in data" :key="d.id">
                                <td class="text-center text-md">#</td>
                                <td class="text-center text-md font-semibold capitalize">{{  d.group }}</td>
                                <td class="text-center text-md font-semibold capitalize" @dblclick="e => toggleEdit(e)">
                                    {{ formatLabel(d.label)}}
                                    <input type="text" class="hidden" :value="d.label">
                                </td>
                                <td class="text-right text-md font-semibold" @dblclick="e => toggleEdit(e)">
                                    {{ d.value }}
                                    <input type="text" class="hidden" :value="d.value">
                                </td>
                                <td class="text-center text-md font-semibold">{{ d.sort_index }}</td>
                                <td class="text-center text-md font-semibold" :class="d.indicator">{{ d.indicator }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
