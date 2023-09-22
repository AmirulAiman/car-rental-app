<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue'
import { useForm, Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

const viewModal = ref(false);
const totalCharge = ref(0);
const processing = ref(false);
const { data, history, statuses } = defineProps(['data','history','statuses']);

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

const totalDayBooked = computed(() => {
    let day1 = dayjs(data.start_date)
    let day2 = dayjs(data.end_date)
    let diff = day2.diff(day1,'day')
    
    totalCharge.value = data.car.rental_charge * diff
    return diff;
})
const status = computed(() => statuses.find(s => s.value == data.status));
const formatText = (text) => text.replaceAll('_',' ');
/**
 * Update the booking status to the next status
 * @param {Number} id CarUser id
 * @param {Number} car_id Car id
 * @param {String} status current status
 */
const updateStatus = (id, car_id, status, approved = false) => {
    processing.value = true
    axios.post(route('app.respond', id),{
        'car_id': car_id,
        'status': status,
        'approved': approved
    })
    .then((res) => {
        alert('Respond saved.')
        window.location.reload()
    })
    .catch((err) => {
        alert('Failed to update')
        console.log(err)
        window.location.reload()
    })
    .finally(() => {
        processing.value = false
    })
}

const openModal = () => {
    viewModal.value = true
}
const closeModal = () => {
    viewModal.value = false;
}

const sendTestEmail = () => {
    axios.post(route('app.send-test-email'))
    .then(res => {
        alert('Test email send.');
    })
    .catch(errors => {
        console.log(errors)
    })
}

const canCancel = computed(() => {
    return ['pending_owner_approval','pending_payment_deposit','pending_validation'].includes(data.status);
})
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
                                <tr class="py-3" v-for="user in data" :key="user.id" v-if="data.length > 0">
                                    <td class="text-center text-md">#</td>
                                    <td class="text-left text-md">{{  user.name }}</td>
                                    <td class="text-left text-md font-semibold">{{ user.email }}</td>
                                    <td class="text-center text-md font-semibold capitalize">{{ user.role }}</td>
                                    <td class="text-right text-md">{{ dayjs(user.created_at).format('DD/MM/YYYY') }}</td>
                                </tr>
                                <tr v-else>
                                    <td colspan="5" class="text-center font-bold font-xl py-5">No User Registered</td>
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
                                <tr class="" v-for="rental in data" :key="rental.id" v-if="data,length > 0">
                                    <td class="text-center text-md py-5">#</td>
                                    <td class="text-left text-md font-semibold py-5">{{  rental.user.name }}</td>
                                    <td class="text-left text-md font-semibold py-5">{{ rental.car.name }}</td>
                                    <td class="">
                                        <span class="text-md font-semibold py-5">{{ dayjs(rental.start_date).format('DD/MM/YYYY') }}</span> to 
                                        <span class="text-md font-semibold py-5">{{ dayjs(rental.end_date).format('DD/MM/YYYY') }}</span>
                                        ({{ dayjs(rental.end_date).diff(dayjs(rental.start_date),'day') }} Day)
                                    </td>
                                    <td class="capitalize py-5">{{ formatText(rental.status) }}</td>
                                    <td>
                                        <button 
                                            class="px-3 py-1 bg-gray-900 text-gray-200 rounded-md hover:bg-gray-200 hover:text-gray-900" 
                                            v-if="rental.status == 'pending_validation' || rental.status == 'completed' || rental.status == 'pending_payment_final'"
                                            @click="openModal"
                                        >View Proof of Payment</button>
                                    </td>
                                    <td class=" py-5">
                                        <button class="px-3 py-1 rounded-md bg-green-900 text-green-200 mx-2" v-if="rental.status == 'pending_owner_approval'" @click="updateStatus(rental.id, rental.car.id, rental.status, true)">Approve</button>
                                        <button class="px-3 py-1 rounded-md bg-yellow-900 text-yellow-200 mx-2" v-if="rental.status == 'pending_owner_approval'" @click="updateStatus(rental.id, rental.car.id, rental.status, false)">Reject</button>
                                        <button class="px-3 py-1 rounded-md bg-green-900 text-green-200 mx-2" v-if="rental.status == 'pending_validation'" @click="updateStatus(rental.id, rental.car.id, rental.status, true)">Approve</button>
                                        <button class="px-3 py-1 rounded-md bg-yellow-900 text-yellow-200 mx-2" v-if="rental.status == 'pending_validation'" @click="updateStatus(rental.id, rental.car.id, rental.status, false)">Reject</button>
                                        <button class="px-3 py-1 rounded-md bg-yellow-900 text-yellow-200 mx-2" v-if="rental.status == 'waiting_vehicle'" @click="updateStatus(rental.id, rental.car.id, rental.status)">Vehicle Delivered</button>
                                        <button class="px-3 py-1 rounded-md bg-yellow-900 text-yellow-200 mx-2" v-if="rental.status == 'vehicle_returned'" @click="updateStatus(rental.id, rental.car.id, rental.status)">Retrieve Vehicle</button>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="5" class="text-center font-bold font-xl py-5">No Booking Request Existed</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div 
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" 
                    v-if="$page.props.auth.user.role == 'customer'"
                >
                    <div class="w-full p-6 text-gray-900 flex justify-between" v-if="data != null || data != undefined">
                        <div class="container flex justify-between">
                            <table class="w-1/2">
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
                                   <td class="font-semibold">Plate Number:</td> 
                                   <td class="capitalize font-bold text-lg">{{ data.car.plate_number }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold">Booking Status:</td>
                                    <td class="text-center font-bold capitalize mx-3 px-5" :class="status.indicator">{{ status.label }}</td>
                                </tr>
                            </table>
                            <div class="w-1/2">
                                <form @submit.prevent="submitPayment" v-if="data.status == 'pending_payment_deposit' || data.status == 'pending_payment_final'">
                                    <h1>Please Upload Proof of payment to complete booking process</h1>
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
                                    <div class="flex justify-center mt-4">
    
                                        <PrimaryButton 
                                            class="ml-4" 
                                            :class="{ 'opacity-25': formPayment.processing }"
                                            :disabled="formPayment.processing"
                                        >
                                            Submit
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="relative w-fill">
                            <div class="absolute flex justify-evenly bottom-0 right-0 px-5 py-2">
                                <PrimaryButton
                                    class="mx-3"
                                    v-if="data.status == 'waiting_vehicle'"
                                    @click="updateStatus(data.id, data.car_id, data.status)"
                                >Vehicle Received</PrimaryButton>
                                <PrimaryButton
                                    class="mx-3"
                                    v-if="data.status == 'vehicle_received'"
                                    @click="updateStatus(data.id, data.car_id, data.status)"
                                >
                                Return Vehicle</PrimaryButton>
                                <PrimaryButton 
                                    class="bg-orange-900 text-orange-500 hover:bg-orange-500 hover:text-orange-900 transition-colors duration-300" 
                                    @click="updateStatus(data.id, data.car_id, 'booking_cancelled')"
                                    v-if="canCancel"
                                >Cancel Booking</PrimaryButton>
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
                                    <tr class="py-3" v-for="rental in history" :key="rental.id" v-if="history.length > 0">
                                        <td class="text-center text-md py-5">#</td>
                                        <td class="text-left text-md font-semibold">{{ rental.car.name }}</td>
                                        <td class="">
                                            <span class="text-md font-semibold">{{ dayjs(rental.start_date).format('DD/MM/YYYY') }}</span> to 
                                            <span class="text-md font-semibold">{{ dayjs(rental.end_date).format('DD/MM/YYYY') }}</span>
                                            ({{ dayjs(rental.end_date).diff(dayjs(rental.start_date), 'day') }} Day)
                                        </td>
                                        <td class="capitalize">{{ formatText(rental.status) }}</td>
                                        <td>
                                            <button 
                                                class="px-3 rounded-md bg-gray-900 text-gray-300 hover:bg-gray-300 hover:text-gray-900 transition-colors duration-300" 
                                                v-if="rental.status == 'accepted' || rental.status == 'completed'"
                                                @click="openModal"
                                            >View Proof of Payment</button>
                                            
                                        <Modal
                                            :show="viewModal"
                                            @close="closeModal"
                                        >
                                            <div class="bg-white rounded-md p-6">
                                                <img :src="rental.proof_of_payment" alt="Proof of payment">
                                            </div>
                                        </Modal>
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="5" class="text-center font-bold font-xl py-5">No Booking History Existed</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
        <Modal
            :show="viewModal"
            @close="closeModal"
        >
            <div class="bg-white rounded-md p-6">
                <img :src="rental.proof_of_payment" alt="Proof of payment">
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
