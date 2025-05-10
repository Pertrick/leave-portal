<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SearchSelect } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { ToastService } from '@/services/toast';

const props = defineProps<{
    departments: Array<{ id: number; name: string }>;
    userLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    staff_id: '',
    username: '',
    firstname: '',
    lastname: '',
    email: '',
    phone: '',
    address: '',
    department_id: '',
    user_level_id: '',
    designation: '',
    gender: '',
    dob: '',
    join_date: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onSuccess: () => {
            ToastService.success('Registration successful! Welcome to the portal.');
            form.reset('password', 'password_confirmation');
        },
        onError: (errors) => {
            ToastService.error('Registration failed. Please check the form for errors.');
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <Head title="Register New User" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-6">Register New User</h2>
                        
                        <form @submit.prevent="submit" class="flex flex-col gap-6">
                            <div class="grid gap-6">
                                <!-- Row 1: Staff ID, Username, First Name, Last Name -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="grid gap-2">
                                        <Label for="staff_id">Staff ID</Label>
                                        <Input
                                            id="staff_id"
                                            type="text"
                                            required
                                            autofocus
                                            v-model="form.staff_id"
                                            placeholder="Staff ID"
                                        />
                                        <InputError :message="form.errors.staff_id" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="username">Username</Label>
                                        <Input
                                            id="username"
                                            type="text"
                                            required
                                            v-model="form.username"
                                            placeholder="Username"
                                        />
                                        <InputError :message="form.errors.username" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="firstname">First Name</Label>
                                        <Input
                                            id="firstname"
                                            type="text"
                                            required
                                            v-model="form.firstname"
                                            placeholder="First name"
                                        />
                                        <InputError :message="form.errors.firstname" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="lastname">Last Name</Label>
                                        <Input
                                            id="lastname"
                                            type="text"
                                            required
                                            v-model="form.lastname"
                                            placeholder="Last name"
                                        />
                                        <InputError :message="form.errors.lastname" />
                                    </div>
                                </div>

                                <!-- Row 2: Email, Phone, Department, User Level -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="grid gap-2">
                                        <Label for="email">Email</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            required
                                            v-model="form.email"
                                            placeholder="email@example.com"
                                        />
                                        <InputError :message="form.errors.email" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="phone">Phone</Label>
                                        <Input
                                            id="phone"
                                            type="tel"
                                            v-model="form.phone"
                                            placeholder="Phone number"
                                        />
                                        <InputError :message="form.errors.phone" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="department_id">Department</Label>
                                        <SearchSelect
                                            id="department_id"
                                            v-model="form.department_id"
                                            :options="departments.map(dept => ({ value: dept.id, label: dept.name }))"
                                            placeholder="Select department"
                                            searchable
                                        />
                                        <InputError :message="form.errors.department_id" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="user_level_id">User Level</Label>
                                        <SearchSelect
                                            id="user_level_id"
                                            v-model="form.user_level_id"
                                            :options="userLevels.map(level => ({ value: level.id, label: level.name }))"
                                            placeholder="Select user level"
                                            searchable
                                        />
                                        <InputError :message="form.errors.user_level_id" />
                                    </div>
                                </div>

                                <!-- Row 3: Address, Designation, Gender, Join Date -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="grid gap-2">
                                        <Label for="address">Address</Label>
                                        <Input
                                            id="address"
                                            type="text"
                                            v-model="form.address"
                                            placeholder="Your address"
                                        />
                                        <InputError :message="form.errors.address" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="designation">Designation</Label>
                                        <Input
                                            id="designation"
                                            type="text"
                                            v-model="form.designation"
                                            placeholder="Job title"
                                        />
                                        <InputError :message="form.errors.designation" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="gender">Gender</Label>
                                        <SearchSelect
                                            id="gender"
                                            v-model="form.gender"
                                            :options="[
                                                { value: 'male', label: 'Male' },
                                                { value: 'female', label: 'Female' },
                                                { value: 'other', label: 'Other' }
                                            ]"
                                            placeholder="Select gender"
                                        />
                                        <InputError :message="form.errors.gender" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="join_date">Join Date</Label>
                                        <Input
                                            id="join_date"
                                            type="date"
                                            required
                                            v-model="form.join_date"
                                        />
                                        <InputError :message="form.errors.join_date" />
                                    </div>
                                </div>

                                <!-- Row 4: Date of Birth, Password, Confirm Password -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="grid gap-2">
                                        <Label for="dob">Date of Birth</Label>
                                        <Input
                                            id="dob"
                                            type="date"
                                            v-model="form.dob"
                                        />
                                        <InputError :message="form.errors.dob" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="password">Password</Label>
                                        <Input
                                            id="password"
                                            type="password"
                                            required
                                            v-model="form.password"
                                            placeholder="Create password"
                                        />
                                        <InputError :message="form.errors.password" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="password_confirmation">Confirm Password</Label>
                                        <Input
                                            id="password_confirmation"
                                            type="password"
                                            required
                                            v-model="form.password_confirmation"
                                            placeholder="Confirm password"
                                        />
                                        <InputError :message="form.errors.password_confirmation" />
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <Button type="submit" class="w-32" :disabled="form.processing">
                                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                                        Register
                                    </Button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
