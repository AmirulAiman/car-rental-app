<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

const totalCharge = ref(0);
const processing = ref(false);
const { data, history } = defineProps(['data','history']);

const formPayment = useForm({
    receipt:''
})

const submitPayment = () => {
    formPayment.post(route('cars.payment', data.id), {
        onSuccess: () => {
            alert('Upload success, wait for update')
            window.location.reload()
        },
        onError: (error) => {
            alert('Upload failed, please retry')
            console.log(error);
            // window.location.reload()
        }
    })
}

const statusLabel = computed(() => data.status.replace('_',' '));
const statusClass = computed(() => {
    switch (data.status) {
        case 'approve':
            return 'bg-green-500 text-green-900';
            break;
        case 'processing_request':
            return 'bg-yellow-500 text-yellow-900';
            break;
        case 'vehicle_received':
            return 'bg-green-500 text-green-900';
            break;
        case 'vehicle_returned':
            return 'bg-green-500 text-green-900';
            break;
        case 'rejected':
            return 'bg-red-500 text-red-900';
            break;
        case 'completed':
            return 'bg-gray-900 text-gray-200';
            break;
        default:
            return 'bg-orange-500 text-orange-900';
            break;
    }
})
const totalDayBooked = computed(() => {
    let day1 = dayjs(data.start_date)
    let day2 = dayjs(data.end_date)
    let diff = day2.diff(day1,'day')

    totalCharge.value = data.car.rental_charge * diff
    return diff;
})

const updateStatus = (id, car_id,status) => {
    processing.value = true
    axios.post(route('app.respond', id),{
        'id': id,
        'car_id': car_id,
        'status': status
    })
    .then(() => {
        alert('Respond send.')
        processing.value = false
        window.location.reload
    })
    .catch((err) => {
        alert('Failed to update')
        console.log(err)
        processing.value = false
        window.location.reload
    })
    .final(() => {
        processing.value = false
    })
}

const formatText = (text) => text.replace('_',' ');

const sendTestEmail = () => {
    axios.post(route('app.send-test-email'))
    .then(res => {
        alert('Test email send.');
    })
    .catch(errors => {
        console.log(errors)
    })
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" v-if="$page.props.auth.user.role == 'admin'">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-start">
                            <button class="px-5 py-2 rounded-md bg-gray-900 text-gray-200" @click="sendTestEmail">Send Test Email</button>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" v-if="$page.props.auth.user.role == 'admin'">
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">#</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Name</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Email</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Role</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">User Since</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="py-3" v-for="user in data" :key="user.id">
                                    <td class="text-center text-md">#</td>
                                    <td class="text-left text-md">{{  user.name }}</td>
                                    <td class="text-left text-md font-semibold">{{ user.email }}</td>
                                    <td class="text-center text-md font-semibold capitalize">{{ user.role }}</td>
                                    <td class="text-right text-md">{{ dayjs(user.created_at).format('DD/MM/YYYY') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="overflow-hidden shadow-sm sm:rounded-lg" v-if="$page.props.auth.user.role == 'owner'">
                    <h1 class="mb-6 font-semibold text-md">Car Booking Request</h1>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">#</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Renter</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Car Requested</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">From/Until</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Status</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Proof of Payment</th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="py-3" v-for="rental in data" :key="rental.id">
                                    <td class="text-center text-md">#</td>
                                    <td class="text-left text-md font-semibold">{{  rental.user.name }}</td>
                                    <td class="text-left text-md font-semibold">{{ rental.car.name }}</td>
                                    <td class="">
                                        <span class="text-md font-semibold">{{ dayjs(rental.start_date).format('DD/MM/YYYY') }}</span> to 
                                        <span class="text-md font-semibold">{{ dayjs(rental.end_date).format('DD/MM/YYYY') }}</span>
                                        ({{ dayjs(rental.end_date).diff(dayjs(rental.start_date),'day') }} Day)
                                    </td>
                                    <td class="capitalize">{{ formatText(rental.status) }}</td>
                                    <td>
                                        <button class="" v-if="rental.status == 'accepted' || rental.status == 'completed'">View Proof of Payment</button>
                                    </td>
                                    <td>
                                        <button class="px-3 py-1 rounded-md bg-green-900 text-green-200 mx-2" v-if="rental.status == 'pending_approval'" @click="updateStatus(rental.id, rental.car.id, 'approve')">Approve</button>
                                        <button class="px-3 py-1 rounded-md bg-yellow-900 text-yellow-200 mx-2" v-if="rental.status == 'pending_approval'" @click="updateStatus(rental.id, rental.car.id, 'reject')">Reject</button>
                                        <button class="px-3 py-1 rounded-md bg-yellow-900 text-yellow-200 mx-2" v-if="rental.status == 'accepted'" @click="updateStatus(rental.id, rental.car.id, 'vehicle_delivered')">Vehicle Delivered</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div 
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" 
                    v-if="$page.props.auth.user.role == 'customer'"
                >
                    <div class="p-6 text-gray-900 flex justify-between" v-if="data != null || data != undefined">
                        <div class="min-w-3/4">
                            <table>
                                <tr>
                                    <td class="font-semibold">Car name:</td>
                                    <td>{{ data.car.name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Car Brand:</td>
                                    <td class="capitalize">{{ data.car.brand }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Car Deposit:</td>
                                    <td>RM {{ data.car.deposit }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Total Day Booked:</td>
                                    <td>{{ totalDayBooked }} Days</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Total Charge:</td>
                                    <td>RM {{ totalCharge }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Booking Status:</td>
                                    <td class="text-center font-bold capitalize mx-3 px-5 rounded" :class="statusClass">{{ statusLabel }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="w-fill">
                            <form @submit.prevent="submitPayment" v-if="data.status == 'approved'">
                                <h1>Please Upload Proof of payment to complete boking process</h1>
                                <div class="w-fill">
                                    <input type="hidden" name="id" v-model="formPayment.id">
                                    <TextInput 
                                        id="receipt" 
                                        type="file" 
                                        class="mt-1 block w-full" 
                                        @input="formPayment.receipt = $event.target.files[0]"
                                        required 
                                    />
                                    <InputError class="mt-2" :message="formPayment.errors.receipt" />
                                </div>
                                <div class="flex items-center justify-center mt-4">

                                    <PrimaryButton 
                                        class="ml-4" 
                                        :class="{ 'opacity-25': formPayment.processing }"
                                        :disabled="formPayment.processing"
                                    >
                                        Submit
                                    </PrimaryButton>
                                </div>
                            </form>
                            <div class="flex-col justify-start" v-if="data.status == 'vehicle_delivered'">
                                <h1>Please verify if you receved the vehicle</h1>
                                <PrimaryButton 
                                    class="ml-4" 
                                    :class="{ 'opacity-25': processing.value }"
                                    :disabled="processing.value"
                                    @click="updateStatus(data.id, data.car.id, 'vehicle_received')"
                                >
                                    Vehicle Received
                                </PrimaryButton>
                            </div>
                            <div class="flex-col justify-start" v-if="data.status == 'vehicle_received'">
                                <h1>Clikc below to confirm the vehicle has been returned</h1>
                                <PrimaryButton 
                                    class="ml-4" 
                                    :class="{ 'opacity-25': processing.value }"
                                    :disabled="processing.value"
                                    @click="updateStatus(data.id, data.car.id, 'completed')"
                                >
                                    Return Vehicle
                                </PrimaryButton>
                            </div>
                            <div class="flex-col justify-start" v-if="data.status == 'accepted'">
                                <PrimaryButton 
                                    class="ml-4 bg-yellow-900 text-yellow-200" 
                                    :class="{ 'opacity-25': processing.value }"
                                    :disabled="processing.value"
                                    @click="updateStatus(data.id, data.car.id, 'booking_canceled')"
                                >
                                    Cancel Booking
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 text-gray-900 flex justify-between" v-else>
                        <h1>No Car booked, please go to <a class="text-md font-bold underline" :href="route('cars.index')">Available Cars</a> page to browse our cars selection to book.</h1>
                    </div>
                </div>
                <div 
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg" 
                        v-if="$page.props.auth.user.role == 'customer'"
                    >
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">#</th>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Car Requested</th>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">From/Until</th>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Status</th>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">Proof of Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="py-3" v-for="rental in history" :key="rental.id">
                                        <td class="text-center text-md">#</td>
                                        <td class="text-left text-md font-semibold">{{ rental.car.name }}</td>
                                        <td class="">
                                            <span class="text-md font-semibold">{{ dayjs(rental.start_date).format('DD/MM/YYYY') }}</span> to 
                                            <span class="text-md font-semibold">{{ dayjs(rental.end_date).format('DD/MM/YYYY') }}</span>
                                            ({{ dayjs(rental.end_date).diff(dayjs(rental.start_date), 'day') }} Day)
                                        </td>
                                        <td class="capitalize">{{ formatText(rental.status) }}</td>
                                        <td>
                                            <button class="px-3 rounded-md bg-gray-900 text-gray-300 hover:bg-gray-300 hover:text-gray-900 transition-colors duration-300" v-if="rental.status == 'accepted' || rental.status == 'completed'">View Proof of Payment</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
