<div class="bg-center bg-no-repeat bg-cover w-dvw h-dvh bg-loginBg ">
    <div class="flex w-full h-full">
        <div class="items-center justify-center flex-1 hidden lg:flex">
            <div class="flex flex-col items-center gap-4">
                <img src="{{ Vite::image('logo.png') }}" class="w-full max-w-96" />
                <h1 class="text-6xl font-bold text-center text-secondary-500 font-title tracking-[.25em]">
                    {{ config('app.name') }}
                </h1>
                <div class="text-5xl text-center text-white">{{ config('app.hospital_name') }}</div>
            </div>
        </div>
        <div
            class="relative w-full before:absolute before:inset-0 before:drop-shadow-lg before:bg-primarydark before:backdrop-blur-md lg:max-w-125">
            <div class="relative flex flex-col items-center justify-center h-full py-6 gap-9 px-9">
                <div class="flex flex-col items-center gap-2 lg:hidden">
                    <img src="{{ Vite::image('logo.png') }}" class="w-full max-w-60" />
                    <h1 class="text-2xl font-bold text-center text-secondary-500 font-title tracking-[.25em]">
                        {{ config('app.name') }}
                    </h1>
                    <div class="text-xl text-center text-white">{{ config('app.hospital_name') }}</div>
                </div>
                <form wire:submit='login' class="w-full space-y-3 sm:space-y-6">
                    <x-form.input base="!rounded-full" labelClass="text-white" :loading="$isLoading" label="Nama Pengguna"
                        block placeholder="Masukkan nama pengguna" type='text'
                        error="{{ $errors->first('username') }}" wire:model.blur='username' required />
                    <x-form.input base="!rounded-full" labelClass="text-white" :loading="$isLoading" label="Kata Sandi"
                        block placeholder="Masukkan kata sandi" type='password' wire:model.blur='password'
                        error="{{ $errors->first('password') }}" required />

                    <div class="flex items-center justify-between !mt-7">
                        <x-form.toggle :loading="$isLoading" label="Ingatkan Saya" class="text-white"
                            error="{{ $errors->first('rememberMe') }}" wire:model='rememberMe' />
                    </div>

                    <x-button :loading="$isLoading" wire:target="login, username, password" color="secondary" type="submit"
                        block radius="rounded-full" base="!mt-8 lg:!mt-10">
                        Login
                    </x-button>
                </form>
            </div>
        </div>
        <div class="w-[10%] hidden lg:block"></div>
    </div>
</div>
