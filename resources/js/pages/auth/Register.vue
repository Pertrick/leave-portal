<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SearchSelect } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { computed, watch, getCurrentInstance } from 'vue';
import type { ComponentInternalInstance } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useFlash } from '@/composables/useFlash';

const { flash } = useFlash();
const instance = getCurrentInstance();
const proxy = instance?.proxy;

const props = defineProps<{
    departments: Array<{ id: number; name: string }>;
    userLevels: Array<{ id: number; name: string }>;
    staffData: {
        staff_id: string;
        firstname: string;
        lastname: string;
        email: string;
        phone?: string;
        address?: string;
        department_id: number;
        designation?: string;
        gender?: string;
        dob?: string;
        join_date: string;
    } | null;
}>();

const form = useForm({
    staff_id: props.staffData?.staff_id || '',
    username: '',
    firstname: props.staffData?.firstname || '',
    lastname: props.staffData?.lastname || '',
    email: props.staffData?.email || '',
    phone: props.staffData?.phone || '',
    address: props.staffData?.address || '',
    user_level_id: null as number | null,
    department_id: props.staffData?.department_id || null,
    designation: props.staffData?.designation || '',
    gender: props.staffData?.gender || '',
    dob: props.staffData?.dob || '',
    join_date: props.staffData?.join_date || '',
    password: '',
    password_confirmation: '',
});

// Watch for changes in firstname and lastname to update username
watch(
    [() => form.firstname, () => form.lastname],
    ([newFirstname, newLastname]) => {
        if (newFirstname && newLastname) {
            // Convert to lowercase and remove spaces
            const firstname = newFirstname.toLowerCase().trim();
            const lastname = newLastname.toLowerCase().trim();
            
            // Generate username: firstname.lastname
            form.username = `${firstname}.${lastname}`;
        }
    }
);

// Watch all form fields to clear errors when user starts typing
const formFields = [
    'staff_id',
    'username',
    'firstname',
    'lastname',
    'email',
    'phone',
    'address',
    'department_id',
    'user_level_id',
    'designation',
    'gender',
    'dob',
    'join_date',
    'password',
    'password_confirmation'
] as const;

// Create watchers for each form field
formFields.forEach(field => {
    watch(() => form[field], () => {
        if (form.errors[field]) {
            form.clearErrors(field);
        }
    });
});

// Validation rules
const validateForm = () => {
    const errors: Record<string, string> = {};

    // Staff ID validation
    if (!form.staff_id) {
        errors.staff_id = 'Staff ID is required';
    } else if (form.staff_id.length < 3) {
        errors.staff_id = 'Staff ID must be at least 3 characters';
    }

    // Username validation
    if (!form.username) {
        errors.username = 'Username is required';
    } else if (form.username.length < 3) {
        errors.username = 'Username must be at least 3 characters';
    } else if (!/^[a-zA-Z0-9._]+$/.test(form.username)) {
        errors.username = 'Username can only contain letters, numbers, dots, and underscores';
    }

    // First name validation
    if (!form.firstname) {
        errors.firstname = 'First name is required';
    } else if (form.firstname.length < 2) {
        errors.firstname = 'First name must be at least 2 characters';
    }

    // Last name validation
    if (!form.lastname) {
        errors.lastname = 'Last name is required';
    } else if (form.lastname.length < 2) {
        errors.lastname = 'Last name must be at least 2 characters';
    }

    // Email validation
    if (!form.email) {
        errors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address';
    }

    // Phone validation (optional but must be valid if provided)
    if (form.phone && !/^\+?[\d\s-]{10,}$/.test(form.phone)) {
        errors.phone = 'Please enter a valid phone number';
    }

    // Department validation
    if (!form.department_id) {
        errors.department_id = 'Department is required';
    }

    // User level validation
    if (!form.user_level_id) {
        errors.user_level_id = 'User level is required';
    }

    // Gender validation (optional but must be valid if provided)
    if (form.gender && !['male', 'female', 'other'].includes(form.gender)) {
        errors.gender = 'Please select a valid gender';
    }

    // Date of birth validation (optional but must be valid if provided)
    if (form.dob) {
        const dobDate = new Date(form.dob);
        const today = new Date();
        if (dobDate > today) {
            errors.dob = 'Date of birth cannot be in the future';
        }
    }

    // Join date validation
    if (!form.join_date) {
        errors.join_date = 'Join date is required';
    } else {
        const joinDate = new Date(form.join_date);
        const today = new Date();
        if (joinDate > today) {
            errors.join_date = 'Join date cannot be in the future';
        }
    }

    // Password validation
    if (!form.password) {
        errors.password = 'Password is required';
    } else if (form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters';
    } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(form.password)) {
        errors.password = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
    }

    // Password confirmation validation
    if (!form.password_confirmation) {
        errors.password_confirmation = 'Please confirm your password';
    } else if (form.password !== form.password_confirmation) {
        errors.password_confirmation = 'Passwords do not match';
    }

    return errors;
};

type FormFields = 'staff_id' | 'username' | 'firstname' | 'lastname' | 'email' | 'phone' | 
    'address' | 'designation' | 'gender' | 'dob' | 'join_date' | 'password' | 
    'password_confirmation' | 'user_level_id' | 'department_id';

const submit = () => {
    const errors = validateForm();
    
    if (Object.keys(errors).length > 0) {
        // Set form errors
        Object.entries(errors).forEach(([field, message]) => {
            if (field in form) {
                form.setError(field as FormFields, message);
            }
        });
        proxy?.$toast.error('Please correct the errors in the form.');
        return;
    }

    form.post(route('register'), {
        onSuccess: () => {
            proxy?.$toast.success('Staff Account Created Successfully!');
            form.reset();
        },
        onError: (errors) => {
            proxy?.$toast.error('Registration failed. Please check the form for errors.');
        },
    });
};
</script>

<template>
    <AppLayout title="Register New User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Register New Account
                <span v-if="staffData" class="text-sm text-gray-500">
                    (Pre-filled from Staff ID: {{ staffData.staff_id }})
                </span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="flex flex-col gap-6">
                            <div class="grid gap-6">
                                <!-- Row 1: Staff ID, Username, First Name, Last Name -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="grid gap-2">
                                        <Label for="staff_id">Staff ID</Label>
                                        <Input
                                            id="staff_id"
                                            type="text"
                                            autofocus
                                            v-model="form.staff_id"
                                            placeholder="Staff ID"
                                        />
                                        <InputError :message="form.errors.staff_id" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="firstname">First Name</Label>
                                        <Input
                                            id="firstname"
                                            type="text"
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
                                            v-model="form.lastname"
                                            placeholder="Last name"
                                        />
                                        <InputError :message="form.errors.lastname" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="username">Username</Label>
                                        <Input
                                            id="username"
                                            type="text"
                                            v-model="form.username"
                                            placeholder="Username"
                                        />
                                        <InputError :message="form.errors.username" />
                                    </div>
                                </div>

                                <!-- Row 2: Email, Phone, Department, User Level -->
                                <div class="grid grid-cols-4 gap-4">
                                    <div class="grid gap-2">
                                        <Label for="email">Email</Label>
                                        <Input
                                            id="email"
                                            type="email"
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
                                            v-if="userLevels.length"
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
    </AppLayout>
</template>
