<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Use the same head as guest.blade.php -->
    <style>
        .atom-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .atom {
            position: absolute;
            border-radius: 50%;
            opacity: 0.4;
            filter: blur(40px);
            animation: float 20s infinite linear;
        }
        
        .atom-1 {
            width: 400px;
            height: 400px;
            background: rgba(99, 102, 241, 0.4);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .atom-2 {
            width: 300px;
            height: 300px;
            background: rgba(139, 92, 246, 0.4);
            bottom: 20%;
            right: 15%;
            animation-delay: -5s;
            animation-duration: 25s;
        }

        .atom-3 {
            width: 350px;
            height: 350px;
            background: rgba(236, 72, 153, 0.4);
            top: 40%;
            right: 25%;
            animation-delay: -10s;
            animation-duration: 30s;
        }

        .atom-4 {
            width: 250px;
            height: 250px;
            background: rgba(16, 185, 129, 0.4);
            bottom: 30%;
            left: 20%;
            animation-delay: -15s;
            animation-duration: 22s;
        }
        
        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(50px, 50px) rotate(90deg);
            }
            50% {
                transform: translate(0, 100px) rotate(180deg);
            }
            75% {
                transform: translate(-50px, 50px) rotate(270deg);
            }
            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }
        
        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: particle-float 15s infinite linear;
        }
        
        @keyframes particle-float {
            0%, 100% {
                transform: translate(0, 0);
                opacity: 0.3;
            }
            50% {
                transform: translate(100px, -100px);
                opacity: 0.1;
            }
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-sans">
    <!-- Moving Atom Background -->
    <div class="atom-container">
        <div class="atom atom-1"></div>
        <div class="atom atom-2"></div>
        <div class="atom atom-3"></div>
        <div class="atom atom-4"></div>

        <!-- Particles -->
        @for ($i = 0; $i < 20; $i++)
            <div class="particle" style="
                top: {{ rand(0, 100) }}%;
                left: {{ rand(0, 100) }}%;
                animation-delay: -{{ $i * 0.5 }}s;
                animation-duration: {{ rand(10, 20) }}s;
            "></div>
        @endfor
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo Header -->
            <div class="text-center mb-8">
                <!-- Triangle Logo -->
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20">
                        <svg viewBox="0 0 100 100" class="w-full h-full">
                            <polygon 
                                points="50,15 85,75 15,75" 
                                fill="none" 
                                stroke="url(#gradient)" 
                                stroke-width="3"
                                class="animate-pulse"
                            />
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#ec4899;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <text x="50" y="52" text-anchor="middle" dy=".3em" 
                                  class="text-sm font-bold fill-white">POS AI</text>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold mb-2">Welcome Back</h1>
                <p class="text-gray-400">Sign in to your account to continue</p>
            </div>

            <!-- Form -->
            <div class="bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl shadow-black/20 p-8 hover:bg-gray-700 transition-all duration-300">
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 bg-gray-800/60 border border-gray-700 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/30 transition-all duration-200"
                               placeholder="you@example.com">
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-4 py-3 bg-gray-800/60 border border-gray-700 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/30 transition-all duration-200"
                               placeholder="••••••••">
                    </div>
                    
                    <!-- Button -->
                    <button type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300">
                        Sign In
                    </button>
                    
                    <!-- Register Link -->
                    <div class="text-center pt-4 border-t border-gray-700/50">
                        <a href="{{ route('select-role') }}" 
                           class="text-purple-400 hover:text-purple-300">
                            Create an Account
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>