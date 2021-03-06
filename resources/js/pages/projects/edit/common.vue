<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.edit', project)" class="text-gray-500 hover:underline">Settings</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Common
        </nav>

        <div class="flex flex-col md:flex-row">
            <project-edit-nav :project="project" />

            <form @submit.prevent="submit" class="w-full">
                <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                    <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                        <h2>Project settings</h2>
                    </div>
                    <div class="w-full px-5 py-4">
                        <form-input class="mb-4" label="Name" placeholder="My awesome project" type="text" name="name" v-model="form.name" :errors="$page.props.errors.name" />

                        <div class="flex flex-row items-center mb-4">
                            <form-input class="w-1/2" label="Repository" placeholder="mgkprod/rocket" type="text" name="repository" v-model="form.repository" :errors="$page.props.errors.repository" />
                            <div class="px-4"></div>
                            <form-input class="w-1/2" label="Branch" placeholder="main" type="text" name="branch" v-model="form.branch" :errors="$page.props.errors.branch" />
                        </div>

                        <form-checkbox class="mb-4" label="Trigger a deployment when code is pushed" name="push_to_deploy" v-model="form.push_to_deploy" :errors="$page.props.errors.push_to_deploy" />

                        <form-input class="mb-4" label="Live URL" type="text" placeholder="https://rocket.mgk.dev" name="live_url" v-model="form.live_url" :errors="$page.props.errors.live_url" />
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="fas fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                    <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                        <h2>Remote settings</h2>
                    </div>
                    <div class="w-full px-5 py-4">
                        <form-select class="mb-4" label="Server" name="server_id" required v-model="form.server_id" :errors="$page.props.errors.server_id" :options="servers" />
                        <form-input class="mb-4" label="Deploy path" type="text" placeholder="/home/websites/rocket" name="deploy_path" v-model="form.deploy_path" :errors="$page.props.errors.deploy_path" />
                        <form-input class="mb-4" label="Environment" type="text" placeholder="production" name="environment" v-model="form.environment" :errors="$page.props.errors.environment" />
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="fas fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                    <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                        <h2>Notifications</h2>
                    </div>
                    <div class="w-full px-5 py-4">
                        <form-input class="mb-4" label="Discord Webhook" type="text" placeholder="https://discord.com/api/webhooks/..." name="discord_webhook_url" v-model="form.discord_webhook_url" :errors="$page.props.errors.discord_webhook_url" />
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="fas fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>

                <button @click="destroy" class="inline-block text-sm text-red-500 hover:text-red-600"><i class="mr-1 opacity-50 fas fa-times"></i> Remove this project from Rocket</button>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../../layouts/app').default,

        props: {
            project: Object,
            servers: Object,
        },

        data() {
            return {
                form: {
                    name: '',
                    repository_url: '',
                    live_url: '',
                    server_id: 0,
                    deploy_path: '',
                    environment: '',
                    discord_webhook_url: '',
                    push_to_deploy: '',
                }
            }
        },

        mounted() {
            this.form = { ...this.project }
        },

        methods: {
            submit() {
                this.$page.props.errors = {}

                this.$inertia.put(
                    this.$route('projects.update.common', this.project), { ...this.form }
                )
            },
            destroy() {
                if (confirm("Do you really want to remove this project?")) {
                    this.$inertia.delete(this.$route('projects.destroy', this.project))
                }
            }
        }
    }
</script>
