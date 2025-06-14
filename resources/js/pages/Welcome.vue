<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="relative min-h-screen flex flex-col items-center bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a]">

        <!-- Full-Page Background Image -->
        <img src="/images/img2.jpg" alt="Website Logo" class="absolute inset-0 w-full h-full object-cover">

        <!-- Navigation Links (Placed Above Image) -->
        <header class="relative z-10 w-full max-w-[335px] text-sm lg:max-w-4xl p-6">
            <nav class="flex items-center justify-end gap-4 mb-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-[white] px-5 py-1.5 text-sm leading-normal text-[white] hover:border-[blue] dark:text-[red] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[white] px-5 py-1.5 text-sm leading-normal text-[white] hover:border-[blue] dark:border-[red] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>

            <!-- Added Login Credentials -->
            <p class="relative z-10 text-center text-white text-sm mt-4 bg-black bg-opacity-50 p-2 rounded">
                Admin Login: **admin@example.com**, Password: **password** <br>
                User Login: **user@example.com**, Password: **password**
            </p>
        </header>
    </div>
</template>