<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('btn.create', ['object' => __('business.business')]) }}
        </h2>
    </x-slot>

    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('businesses.store') }}">
            @csrf
            <div class="flex items-center justify-between overflow-hidden" style="flex-wrap: wrap; flex-direction: row;">
                <div class="w-full mr-2" style="max-width: 43%; min-width: 200px">
                    <!-- Email Address -->
                    <div class="mt-4">
                        <x-label for="email" :value="__('register.email')" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-label for="password" :value="__('register.password')" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-label for="password_confirmation" :value="__('register.confirm_password')" />
                        <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    </div>
                </div>

                <div class="w-full mr-2" style="max-width: 53%; min-width: 200px">
                    <!-- First Name -->
                    <div class="mt-4">
                        <x-label for="name" :value="__('business.name')" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                    </div>

                    <!-- Tax ID -->
                    <div class="mt-4">
                        <x-label for="tax_id" :value="__('business.tax_id')" />
                        <x-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id')" required autofocus />
                    </div>

                    <div class="flex items-center justify-between mt-4 overflow-hidden" style="flex-wrap: wrap; flex-direction: row;">
                        <!-- Province Name -->
                        <div class="w-full" style="max-width: 30%;">
                            <x-label for="addr_province" :value="__('register.province')" />
                            <select name="addr_province" id="addr_province" class="block mt-1 w-full">
                            </select>
                        </div>

                        <!-- District Name -->
                        <div class="w-full" style="max-width: 30%;">
                            <x-label for="addr_district" :value="__('register.district')" />
                            <select name="addr_district" id="addr_district" class="block mt-1 w-full">
                            </select>
                        </div>

                        <!-- Ward Name -->
                        <div class="w-full" style="max-width: 30%;">
                            <x-label for="addr_ward" :value="__('register.ward')" />
                            <select name="addr_ward" id="addr_ward" class="block mt-1 w-full">
                            </select>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mt-4">
                        <x-label for="address" :value="__('register.address')" />
                        <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" autofocus />
                    </div>

                    <!-- Contact -->
                    <div class="mt-4">
                        <x-label for="contact" :value="__('register.contact')" />
                        <x-input id="contact" class="block mt-1 w-full" type="text" name="contact" :value="old('contact')" autofocus />
                    </div>

                    <x-button class="mt-4" >
                        {{ __('btn.create') }}
                    </x-button>

                    @if (Session::get('success'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{{ __('message.success') }}</li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </form>
    </x-auth-card>
</x-app-layout>
<script src="{{ asset('js/getLocalRegion.js') }}" defer></script>
