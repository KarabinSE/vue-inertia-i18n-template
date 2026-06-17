<template>
    <Head :title="$t('Appearance settings')" />

    <div class="space-y-6">
        <HeadingSmall
            :title="$t('Appearance settings')"
            :description="$t('Update your account\'s appearance settings')"
        />
        <AppearanceTabs />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

import AppearanceTabs from '@/components/AppearanceTabs.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import { edit } from '@/routes/appearance'
import { wTrans } from 'laravel-vue-i18n'


defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: wTrans('Appearance settings').value,
                href: edit().url,
            },
        ],
    },
})
</script>
