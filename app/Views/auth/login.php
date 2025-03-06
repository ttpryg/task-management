<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Login<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="text-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Welcome Back!</h1>
    <p class="mt-2 text-sm text-gray-600">
        Don't have an account? 
        <a href="/auth/register" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
            Create a new account
        </a>
    </p>
</div>

<?= form_open('auth/attemptLogin', ['id' => 'loginForm', 'class' => 'space-y-6', 'novalidate' => true]) ?>
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" class="csrf_token">
    <div class="space-y-4">
        <!-- Email Field -->
        <div class="form-group">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email or Username <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <input type="text" 
                       id="email" 
                       name="email" 
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                              placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                              focus:border-transparent transition-colors text-sm"
                       placeholder="Enter your email or username"
                       autocomplete="off">
            </div>
            <div class="error-message mt-1 text-sm text-rose-500 hidden"></div>
        </div>

        <!-- Password Field -->
        <div class="form-group">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                              placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                              focus:border-transparent transition-colors text-sm"
                       placeholder="Enter your password"
                       autocomplete="off">
                <button type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 
                               focus:outline-none transition-colors"
                        onclick="togglePasswordVisibility(this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="error-message mt-1 text-sm text-rose-500 hidden"></div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" 
                class="relative w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg 
                       shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 
                       transition-all duration-200 ease-in-out">
            <span class="inline-flex items-center">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Sign in
            </span>
        </button>
    </div>
<?= form_close() ?>
<?= $this->endSection() ?> 