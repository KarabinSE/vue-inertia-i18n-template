<template>
    <AuthLayout
        :title="$t('Verify email')"
        :description="$t('Please verify your email address by clicking on the link we just emailed to you.')"
    >
        <Head :title="$t('Email verification')" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <Form
            v-slot="{ processing }"
            v-bind="send.form()"
            class="space-y-6 text-center"
        >
            <Button :disabled="processing" variant="secondary">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Resend verification email
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block text-sm"
            >
                Log out
            </TextLink>
        </Form>
    </AuthLayout>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { logout } from '@/routes'
import { send } from '@/routes/verification'
import { Form, Head } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

defineProps<{
    status?: string;
}>()
</script>
