<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

import { ref, computed } from 'vue'
import axios from 'axios';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const filter = ref('')
const formMode = ref('new')

const {data, groups} = defineProps(['data','groups']);

const libraries = computed(() => {
    if(filter.value){
        return data.filter((d) => d.group == filter.value)
    }
    return data
})

const showForm = ref(false)

const form = useForm({
    group:'',
    label:'',
    value:'',
    indicator:'',
    sort_index:0
})

const newItem = () => {
    formMode.value = 'new'
    showForm.value = true
}

const editItem = (id) => {
    axios.get(route('app.show', id))
    .then((res) => {
        form.defaults({
            id: res.data.item.id ?? null,
            group: res.data.item.group,
            label: res.data.item.label,
            value: res.data.item.value,
            indicator: res.data.item.indicator?? '',
            sort_index: res.data.item.sor_index ?? 0
        })
    })
    .catch((err) => {
        alert('Failed to fetch data')
        console.log(err)
    })
    .finally(() => {
        formMode.value = 'edit'
        showForm.value = true;
    })
}


const onSubmit = () => {
    if(formMode.value == 'edit'){
        form.post(route('app.update', form.id), {
            onSuccess: () => {
                alert('Update success!')
                form.reset()
                window.location.reload()
            },
            onError: (err) => {
                alert('Update failed')
                console.log(err.message)
            }
        })
    } else {
        form.post(route('app.store'),{
            onSuccess: () => {
                alert('Item created')
                form.reset()
                window.location.reload()
            },
            onError: (err) => {
                alert('Create new failed.')
                console.log(err)
            }
        })
    }
}

const closeForm = () => {
    showForm.value = false
    form.reset()
    formMode.value = 'new'
}

const deleteItem = (id) => {
    if(confirm('Delete the following item?')){
        axios.delete(route('app.delete', id))
        .then(res => {
            if(res.status == 200){
                alert(res.data.message)
            } else {
                alert('Delete failed')
            }
        })
        .catch(err => {
            console.log(err)
        })
    }
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
                    <div class="flex justify-between mb-6 w-fill">
                        <select 
                            name="filterByGroup" 
                            id="filterByGroup" 
                            v-model="filter"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Filter By Group --</option>
                            <option 
                                :key="group.id" 
                                :value="group.group" 
                                v-for="group in groups"
                                class="capitalize"
                            >{{ group.group.replaceAll('_',' ') }}</option>
                        </select>
                        <PrimaryButton class="" @click="newItem">Add New</PrimaryButton>
                    </div>
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
                                <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-center text-gray-700 uppercase dark:text-gray-400"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="py-3 cursor-pointer hover:bg-gray-200" v-for="d in libraries" :key="d.id" @dblclick="editItem(d.id)">
                                <td class="text-center text-md">#</td>
                                <td class="text-center text-md font-semibold capitalize">{{  d.group.replaceAll('_',' ') }}</td>
                                <td class="text-center text-md font-semibold capitalize">
                                    {{ d.label }}
                                </td>
                                <td class="text-right text-md font-semibold">
                                    {{ d.value }}
                                </td>
                                <td class="text-center text-md font-semibold">{{ d.sort_index }}</td>
                                <td class="text-center text-md font-semibold">{{ d.indicator }}</td>
                                <td class="text-center text-md font-semibold">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                                >
                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <button class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" @click="editItem(d.id)" type="button"> Update </button>
                                            <button class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" @click="deleteItem(d.id)" type="button"> Delete </button>
                                        </template>
                                    </Dropdown>    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Modal
                :show="showForm"
                @close="closeForm"
            >
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm">
                        <form @submit.prevent="onSubmit" class="max-w-7xl mx-auto p-5">
                            <div class="mb-6">
                                <InputLabel class="text-md font-semibold" for="group" value="Group" />
                                <TextInput id="group" type="text" class="mt-1 block w-full" v-model="form.group" required autofocus />
                                <InputError class="mt-2" :message="form.errors.group" />
                            </div>
                            <div class="mb-6">
                                <InputLabel class="text-md font-semibold" for="label" value="Label" />
                                <TextInput id="label" type="text" class="mt-1 block w-full" v-model="form.label" required />
                                <InputError class="mt-2" :message="form.errors.label" />
                            </div>
                            
                            <div class="mb-6">
                                <InputLabel class="text-md font-semibold" for="value" value="Value" />
                                <TextInput id="value" type="text" class="mt-1 block w-full" v-model="form.value" required />
                                <InputError class="mt-2" :message="form.errors.value" />
                            </div>
                            
                            <div class="mb-6">
                                <InputLabel class="text-md font-semibold" for="indicator" value="Indicator(Optional)" />
                                <TextInput id="indicator" type="text" class="mt-1 block w-full" v-model="form.indicator" />
                                <InputError class="mt-2" :message="form.errors.indicator" />
                            </div>
                            
                            <div class="mb-6">
                                <InputLabel class="text-md font-semibold" for="sort_index" value="Sort Index" />
                                <TextInput id="sort_index" type="number" class="mt-1 block w-full" v-model="form.sort_index" />
                                <InputError class="mt-2" :message="form.errors.sort_index" />
                            </div>
                            
                            <div class="flex items-center justify-end mt-4">
                                <SecondaryButton 
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                    @click="closeForm"
                                >Cancel</SecondaryButton>
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                                    Submit
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>
