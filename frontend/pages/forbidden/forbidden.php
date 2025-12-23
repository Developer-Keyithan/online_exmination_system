<?php $this->extend('frontend'); ?>
<?php $this->start('content'); ?>

<div class="max-w-4xl w-full mx-auto relative">
    <!-- Main Content -->
    <div>
        <!-- Error Message -->
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-100 mb-4">
                <i class="fas fa-user-lock text-purple-400 mr-3"></i>
                403 Forbidden Access
            </h2>

            <p class="text-lg text-gray-300 mb-6 leading-relaxed">
                You don’t have permission to access this page.
                This area is restricted based on your role or access level.
            </p>

            <div
                class="inline-flex items-center gap-3 bg-[#0004] border border-gray-600 rounded-lg px-6 py-3 text-gray-300">
                <i class="fas fa-shield-alt text-cyan-400"></i>
                <span>Access denied for your account</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
            <!-- Dashboard Button -->
            <a href="<?php echo BASE_URL; ?>/dashboard"
                class="group bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-500 hover:to-cyan-600 text-white py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex flex-col items-center justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-tachometer-alt text-xl"></i>
                    <span class="text-lg font-semibold">Go to Dashboard</span>
                </div>
                <span class="text-sm text-cyan-200 opacity-80">
                    Return to a safe page
                </span>
            </a>

            <!-- Back Button -->
            <a href="javascript:history.back()"
                class="group bg-[#0004] hover:bg-[#0006] border-2 border-gray-600 hover:border-purple-500 text-gray-100 hover:text-purple-400 py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex flex-col items-center justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-arrow-left text-xl"></i>
                    <span class="text-lg font-semibold">Go Back</span>
                </div>
                <span class="text-sm text-gray-400 group-hover:text-purple-300">
                    Return to previous page
                </span>
            </a>
        </div>

        <!-- Footer Info -->
        <div class="pt-6 border-t border-gray-700">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-400">
                <div class="flex items-center gap-2">
                    <i class="fas fa-ban text-purple-400"></i>
                    <span>Error Code: 403</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-cyan-400"></i>
                    <span><?php echo date('M d, Y - H:i:s'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->end(); ?>