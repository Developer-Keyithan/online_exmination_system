<?php $this->extend('frontend'); ?>
<?php $this->start('content'); ?>

<div class="max-w-4xl md:w-full mx-4 md:mx-auto">
    <!-- Error Code with Animation -->
    <!-- <div class="text-center mb-8">
            <div class="relative inline-block">
                <h1 class="text-9xl md:text-[12rem] font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-purple-500 mb-4">
                    404
                </h1>
                <div class="absolute -inset-4 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 blur-3xl rounded-full"></div>
            </div>
            <div class="w-48 h-1 bg-gradient-to-r from-cyan-500 to-purple-600 rounded-full mx-auto mb-6"></div>
        </div> -->

    <!-- Main Content -->
    <div>
        <!-- Error Message -->
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-100 mb-4">
                <i class="fas fa-map-signs text-cyan-400 mr-3"></i>
                404 Page Not Found
            </h2>
            <p class="text-lg text-gray-300 mb-6 leading-relaxed">
                The page you're looking for seems to have wandered off into the digital wilderness.
                It might have been moved, deleted, or never existed in the first place.
            </p>

            <!-- Error Details -->
            <!-- <div class="inline-block bg-[#0004] border border-gray-600 rounded-lg p-4 mb-8">
                <div class="flex flex-col md:flex-row items-center justify-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-link text-cyan-400"></i>
                        <span class="text-gray-300">Path:</span>
                        <code class="bg-gray-800 px-3 py-1 rounded text-cyan-300">/<?php 
                        // $displayPath = isset($_GET['path']) && $_GET['path'] !== ''
                        //     ? $_GET['path']
                        //     : 'Unknown';

                        // if ($displayPath !== 'Unknown') {
                        //     $segments = explode('/', $displayPath);

                        //     // Truncate each segment > 15 chars
                        //     foreach ($segments as &$segment) {
                        //         $segment = htmlspecialchars($segment);
                        //         if (strlen($segment) > 15) {
                        //             $segment = substr($segment, 0, 15) . '...';
                        //         }
                        //     }

                        //     $displayPath = implode('/', $segments);
                        // }

                        // echo $displayPath;
                        ?></code>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-code text-purple-400"></i>
                        <span class="text-gray-300">Method:</span>
                        <code
                            class="bg-gray-800 px-3 py-1 rounded text-purple-300"><?php // echo isset($_GET['method']) ? htmlspecialchars($_GET['method']) : 'GET'; ?></code>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
            <!-- Home Button -->
            <a href="<?php echo BASE_URL; ?>/dashboard"
                class="group bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-500 hover:to-cyan-600 text-white py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex flex-col items-center justify-center">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <i class="fas fa-home text-xl"></i>
                    <span class="text-lg font-semibold">Back to Exams</span>
                </div>
                <span class="text-sm text-cyan-200 opacity-80">Return to dashboard</span>
            </a>

            <!-- Back Button -->
            <a href="javascript:history.back()"
                class="group bg-[#0004] hover:bg-[#0006] border-2 border-gray-600 hover:border-cyan-500 text-gray-100 hover:text-cyan-400 py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex flex-col items-center justify-center">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <i class="fas fa-arrow-left text-xl"></i>
                    <span class="text-lg font-semibold">Go Back</span>
                </div>
                <span class="text-sm text-gray-400 group-hover:text-cyan-300">Return to previous page</span>
            </a>

            <!-- Contact Support -->
            <!-- <a href="<?php echo BASE_URL; ?>/contact" 
                   class="group bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 text-white py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex flex-col items-center justify-center">
                    <div class="flex items-center justify-center gap-3 mb-2">
                        <i class="fas fa-headset text-xl"></i>
                        <span class="text-lg font-semibold">Contact Support</span>
                    </div>
                    <span class="text-sm text-purple-200 opacity-80">Get help from our team</span>
                </a> -->
        </div>

        <!-- Quick Links -->
        <!-- <div class="mb-10">
                <h3 class="text-xl font-semibold text-gray-100 mb-6 text-center flex items-center justify-center gap-2">
                    <i class="fas fa-compass text-cyan-400"></i>
                    Quick Navigation
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <a href="<?php echo BASE_URL; ?>/exams/create" 
                       class="bg-[#0006] hover:bg-[#0008] border border-gray-600 hover:border-cyan-500 text-gray-300 hover:text-cyan-400 py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-plus text-sm"></i>
                        <span>Create Exam</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/exams/list" 
                       class="bg-[#0006] hover:bg-[#0008] border border-gray-600 hover:border-cyan-500 text-gray-300 hover:text-cyan-400 py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-list text-sm"></i>
                        <span>All Exams</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/dashboard" 
                       class="bg-[#0006] hover:bg-[#0008] border border-gray-600 hover:border-cyan-500 text-gray-300 hover:text-cyan-400 py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-tachometer-alt text-sm"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/help" 
                       class="bg-[#0006] hover:bg-[#0008] border border-gray-600 hover:border-cyan-500 text-gray-300 hover:text-cyan-400 py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-question-circle text-sm"></i>
                        <span>Help Center</span>
                    </a>
                </div>
            </div> -->

        <!-- Search Box -->
        <!-- <div class="mb-8">
                <div class="max-w-md mx-auto">
                    <div class="relative group">
                        <input type="text" 
                               placeholder="Search for exams, questions, or resources..." 
                               class="w-full py-4 pl-12 pr-4 bg-[#0006] border-2 border-gray-600 rounded-xl focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 focus:outline-none transition-all duration-300 text-gray-100 placeholder-gray-400">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-cyan-400"></i>
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
                            Search
                        </button>
                    </div>
                </div>
            </div> -->

        <!-- Technical Details -->
        <div class="pt-6 border-t border-gray-700">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-400">
                <!-- <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-cyan-400"></i>
                        <span>If you believe this is an error, please report it to our support team.</span>
                    </div> -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt text-purple-400"></i>
                        <span>Error Code: 404</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-cyan-400"></i>
                        <span><?php echo date('M d, Y - H:i:s'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none -z-10">
        <div
            class="absolute -top-40 -left-40 w-96 h-96 bg-gradient-to-r from-cyan-500/10 to-purple-500/10 rounded-full blur-3xl">
        </div>
        <div
            class="absolute top-40 -right-40 w-96 h-96 bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-full blur-3xl">
        </div>
        <div
            class="absolute -bottom-40 left-40 w-96 h-96 bg-gradient-to-r from-cyan-500/10 to-purple-500/10 rounded-full blur-3xl">
        </div>
    </div>
</div>

<style>
    /* Custom animations for the 404 text */
    @keyframes glitch {
        0% {
            transform: translate(0);
        }

        20% {
            transform: translate(-2px, 2px);
        }

        40% {
            transform: translate(-2px, -2px);
        }

        60% {
            transform: translate(2px, 2px);
        }

        80% {
            transform: translate(2px, -2px);
        }

        100% {
            transform: translate(0);
        }
    }

    h1 {
        animation: glitch 2s infinite;
    }

    /* Pulse animation for buttons */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }
    }

    .pulse {
        animation: pulse 2s infinite;
    }
</style>

<script>
    // Add subtle hover effects
    document.addEventListener('DOMContentLoaded', function () {
        const errorCode = document.querySelector('h1');
        errorCode.addEventListener('mouseenter', function () {
            this.style.animation = 'glitch 0.3s infinite';
        });
        errorCode.addEventListener('mouseleave', function () {
            this.style.animation = 'glitch 2s infinite';
        });
    });
</script>

<?php $this->end(); ?>