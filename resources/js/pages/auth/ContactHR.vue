<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ToastService } from '@/services/toast';

const form = useForm({
    staff_id: '',
    notes: '',
});

const submit = () => {
    form.post(route('contact.hr.submit'), {
        onSuccess: () => {
            ToastService.success('Request submitted successfully!. HR will contact you soon.');
            form.reset();
        },
        onError: (errors) => {
            ToastService.error('Failed to submit request. Please check the form for errors.');
        },
    });
};
</script>

<template>
    <AuthBase title="Contact HR" description="Enter your staff ID to request account access">
        <Head title="Contact HR" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="staff_id">Staff ID</Label>
                    <Input
                        id="staff_id"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        v-model="form.staff_id"
                        placeholder="Enter your staff ID"
                    />
                    <InputError :message="form.errors.staff_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="notes">Additional Notes (Optional)</Label>
                    <Textarea
                        id="notes"
                        :tabindex="2"
                        v-model="form.notes"
                        placeholder="Any additional information you'd like to share"
                        rows="4"
                    />
                    <InputError :message="form.errors.notes" />
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="3" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Submit Request
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account? <TextLink :href="route('login')">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template> 