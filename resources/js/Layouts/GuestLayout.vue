<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <section
        class="bg-center bg-no-repeat bg-[url('/prhism/bg-2.jpg')] bg-gray-600 bg-blend-multiply"
    >
        <div class="min-h-screen flex justify-center items-center pt-3 sm:pt-0">
            <section
                class="xl:px-40 px-5 mx-auto xl:w-8/12 w-10/12 py-10 rounded-3xl shadow-md overflow-hidden bg-fit bg-no-repeat bg-[url('/prhism/bg-1.jpg')]"
            >
                <div class="flex flex-col items-center">
                    <div class="flex flex-row items-center text-center">
                        <Link href="/">
                            <ApplicationLogo
                                class="w-15 h-15 my-5 fill-current text-gray-500"
                            />
                        </Link>
                        <h2
                            class="text-4xl font-bold text-gray-900 dark:text-white"
                        >
                            PRHISM
                        </h2>
                    </div>
                </div>

                <slot />
            </section>
        </div>
    </section>
</template>
